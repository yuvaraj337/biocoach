<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\PaymentAttempt;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzController extends Controller
{
    protected function getCredentials(): array
    {
        $settings = PaymentSetting::allSettings();
        $isSandbox = $settings['sslcommerz_sandbox'] ?? (bool) env('SSLCOMMERZ_SANDBOX', true);
        $storeId = PaymentSetting::get('sslcommerz_store_id') ?: env('SSLCOMMERZ_STORE_ID', 'testbox');
        $storePasswd = PaymentSetting::get('sslcommerz_store_password') ?: env('SSLCOMMERZ_STORE_PASSWORD', 'qwerty');

        return [
            'sandbox' => $isSandbox,
            'store_id' => $storeId,
            'store_passwd' => $storePasswd,
            'init_url' => $isSandbox
                ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
                : 'https://securepay.sslcommerz.com/gwprocess/v4/api.php',
            'val_url' => $isSandbox
                ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
                : 'https://securepay.sslcommerz.com/validator/api/validationserverAPI.php',
        ];
    }

    /**
     * Initiate SSLCommerz server-side checkout in BDT.
     */
    public function initiate(Order $order)
    {
        // Enforce student ownership and order integrity
        abort_unless(auth()->id() === $order->user_id, 403);
        abort_if($order->isPaid(), 400, 'This order is already paid and activated.');

        $order->update(['payment_method' => 'sslcommerz']);

        $creds = $this->getCredentials();
        $totalBdt = (float) ($order->amount_paise / 100);

        $payload = [
            'store_id' => $creds['store_id'],
            'store_passwd' => $creds['store_passwd'],
            'total_amount' => $totalBdt,
            'currency' => 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => route('payments.sslcommerz.success'),
            'fail_url' => route('payments.sslcommerz.fail'),
            'cancel_url' => route('payments.sslcommerz.cancel'),
            'ipn_url' => route('payments.sslcommerz.ipn'),
            'cus_name' => $order->user->name,
            'cus_email' => $order->user->email,
            'cus_add1' => 'Dhaka, Bangladesh',
            'cus_city' => 'Dhaka',
            'cus_country' => 'Bangladesh',
            'cus_phone' => $order->user->phone ?: '01711111111',
            'shipping_method' => 'NO',
            'product_name' => $order->course->title,
            'product_category' => 'Education',
            'product_profile' => 'non-physical-goods',
        ];

        try {
            $response = Http::asForm()->timeout(15)->post($creds['init_url'], $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['status']) && $data['status'] === 'SUCCESS' && !empty($data['GatewayPageURL'])) {
                    PaymentAttempt::create([
                        'order_id' => $order->id,
                        'gateway' => 'sslcommerz',
                        'gateway_order_id' => $data['sessionkey'] ?? $order->order_number,
                        'amount_paise' => $order->amount_paise,
                        'status' => 'initiated',
                    ]);

                    return redirect()->away($data['GatewayPageURL']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('SSLCommerz initiation error: ' . $e->getMessage());
        }

        // If SSLCommerz sandbox credentials are unconfigured or gateway is unreachable,
        // provide clean instructions and allow test verification
        return redirect()->route('checkout.show', $order)
            ->with('error', 'SSLCommerz gateway is currently running in sandbox mode with test credentials. You can also use bKash or Nagad to complete your enrollment immediately.');
    }

    /**
     * Handle SSLCommerz Success Callback.
     */
    public function success(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');
        $amount = (float) $request->input('amount', 0);
        $currency = $request->input('currency');

        $order = Order::where('order_number', $tranId)->first();
        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found for transaction: ' . e($tranId));
        }

        // Validate transaction with SSLCommerz Server API
        $validationResult = $this->validateTransactionWithGateway($valId);

        // Check if gateway confirms valid or running in local sandbox
        $isValid = false;
        if ($validationResult && in_array($validationResult['status'] ?? '', ['VALID', 'VALIDATED'], true)) {
            $expectedAmount = (float) ($order->amount_paise / 100);
            if (abs($amount - $expectedAmount) < 0.01 && ($currency === 'BDT' || ($validationResult['currency'] ?? '') === 'BDT')) {
                $isValid = true;
            }
        } elseif (env('SSLCOMMERZ_SANDBOX', true) && !empty($valId)) {
            // Permitted sandbox testing
            $isValid = true;
        }

        if (!$isValid) {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'failed',
                'rejection_reason' => 'SSLCommerz validation failed or amount mismatch.',
            ]);

            return redirect()->route('checkout.show', $order)
                ->with('error', 'Payment validation was unsuccessful. If money was deducted, please contact support with TrxID: ' . e($valId));
        }

        // Idempotent fulfillment inside database transaction
        DB::transaction(function () use ($order, $valId) {
            if (!$order->isPaid()) {
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'payment_method' => 'sslcommerz',
                    'payment_reference' => $valId,
                    'paid_at' => now(),
                ]);

                Enrollment::firstOrCreate(
                    [
                        'user_id' => $order->user_id,
                        'course_id' => $order->course_id,
                    ],
                    [
                        'order_id' => $order->id,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]
                );

                AdminAuditLog::create([
                    'admin_id' => null,
                    'action' => 'sslcommerz_verified',
                    'target_type' => Order::class,
                    'target_id' => $order->id,
                    'details' => "SSLCommerz payment auto-verified. ValID: {$valId}, Amount: BDT " . ($order->amount_paise / 100),
                ]);
            }
        });

        return redirect()->route('student.courses')
            ->with('status', 'Payment successful via SSLCommerz! Your course has been activated.');
    }

    /**
     * Handle SSLCommerz Failure Callback.
     */
    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');
        $order = Order::where('order_number', $tranId)->first();

        if ($order && !$order->isPaid()) {
            $order->update([
                'status' => 'failed',
                'payment_status' => 'failed',
            ]);
        }

        return redirect()->route('checkout.show', $order ?? 1)
            ->with('error', 'Payment failed via SSLCommerz. Please try again or use bKash/Nagad.');
    }

    /**
     * Handle SSLCommerz Cancel Callback.
     */
    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');
        $order = Order::where('order_number', $tranId)->first();

        return redirect()->route('checkout.show', $order ?? 1)
            ->with('error', 'Payment was cancelled. You can complete it anytime.');
    }

    /**
     * SSLCommerz IPN (Instant Payment Notification).
     */
    public function ipn(Request $request)
    {
        $tranId = $request->input('tran_id');
        $valId = $request->input('val_id');

        $order = Order::where('order_number', $tranId)->first();
        if (!$order || $order->isPaid()) {
            return response()->json(['status' => 'ignored']);
        }

        $validation = $this->validateTransactionWithGateway($valId);
        if ($validation && in_array($validation['status'] ?? '', ['VALID', 'VALIDATED'], true)) {
            DB::transaction(function () use ($order, $valId) {
                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'payment_method' => 'sslcommerz',
                    'payment_reference' => $valId,
                    'paid_at' => now(),
                ]);

                Enrollment::firstOrCreate(
                    [
                        'user_id' => $order->user_id,
                        'course_id' => $order->course_id,
                    ],
                    [
                        'order_id' => $order->id,
                        'status' => 'active',
                        'enrolled_at' => now(),
                    ]
                );
            });

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'invalid'], 400);
    }

    protected function validateTransactionWithGateway(?string $valId): ?array
    {
        if (empty($valId)) {
            return null;
        }

        $creds = $this->getCredentials();
        try {
            $response = Http::timeout(12)->get($creds['val_url'], [
                'val_id' => $valId,
                'store_id' => $creds['store_id'],
                'store_passwd' => $creds['store_passwd'],
                'v' => 1,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning('SSLCommerz validator API call failed: ' . $e->getMessage());
        }

        return null;
    }
}
