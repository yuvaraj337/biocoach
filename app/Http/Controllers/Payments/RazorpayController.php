<?php
namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\PaymentAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayController extends Controller
{
    public function createOrder(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->payment_status === 'pending' && $order->status === 'pending', 409);
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');
        if (!$key || !$secret) return back()->withErrors(['payment'=>'Sandbox keys are not configured.']);
        $existing = $order->paymentAttempts()->where('status','created')->whereNotNull('provider_order_id')->latest()->first();
        if ($existing) return view('payments.checkout', ['order'=>$order,'attempt'=>$existing,'keyId'=>$key]);

        $response = Http::withBasicAuth($key,$secret)->acceptJson()->post('https://api.razorpay.com/v1/orders', [
            'amount'=>$order->amount_paise, 'currency'=>'INR', 'receipt'=>$order->order_number,
            'notes'=>['internal_order_id'=>(string)$order->id],
        ]);
        if (!$response->successful() || !$response->json('id')) {
            Log::warning('Razorpay order creation failed',['status'=>$response->status()]);
            return back()->withErrors(['payment'=>'Could not create sandbox payment order.']);
        }
        $attempt = PaymentAttempt::create([
            'order_id'=>$order->id,'provider'=>'razorpay','provider_order_id'=>$response->json('id'),
            'status'=>'created','amount_paise'=>$order->amount_paise,'currency'=>'INR',
        ]);
        return view('payments.checkout',['order'=>$order,'attempt'=>$attempt,'keyId'=>$key]);
    }

    public function verify(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id,404);
        $data = $request->validate([
            'razorpay_payment_id'=>['required','string','max:120'],
            'razorpay_order_id'=>['required','string','max:120'],
            'razorpay_signature'=>['required','string','max:256'],
        ]);
        $attempt = PaymentAttempt::where('order_id',$order->id)->where('provider_order_id',$data['razorpay_order_id'])->firstOrFail();
        $secret = config('services.razorpay.secret');
        abort_unless($secret,503);
        $expected = hash_hmac('sha256',$data['razorpay_order_id'].'|'.$data['razorpay_payment_id'],$secret);
        if (!hash_equals($expected,$data['razorpay_signature'])) {
            Log::warning('Invalid Razorpay signature',['order_id'=>$order->id]);
            return redirect()->route('student.orders')->withErrors(['payment'=>'Signature verification failed; order remains pending.']);
        }
        DB::transaction(function () use ($order,$attempt,$data) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->payment_status === 'paid') return;
            $attempt->update([
                'provider_payment_id'=>$data['razorpay_payment_id'],'status'=>'signature_verified',
                'verified_at'=>now(),'payload_json'=>['verification'=>'checkout_signature','razorpay_order_id'=>$data['razorpay_order_id']],
            ]);
            // A checkout signature is not enough to grant access; wait for captured webhook.
            $locked->update(['payment_reference'=>$data['razorpay_payment_id'],'status'=>'verification_pending','payment_status'=>'verification_pending']);
        });
        return view('payments.verifying',['order'=>$order]);
    }

    public function webhook(Request $request)
    {
        $secret = config('services.razorpay.webhook_secret');
        $signature = $request->header('X-Razorpay-Signature');
        $body = $request->getContent();
        if (!$secret || !$signature) return response('Webhook not configured',503);
        if (!hash_equals(hash_hmac('sha256',$body,$secret),$signature)) return response('Invalid signature',400);
        $event = json_decode($body,true);
        $name = data_get($event,'event');
        if ($name !== 'payment.captured') return response('Ignored',200);
        $payment = data_get($event,'payload.payment.entity',[]);
        $providerOrderId = data_get($payment,'order_id');
        $paymentId = data_get($payment,'id');
        $amount = data_get($payment,'amount');
        $currency = data_get($payment,'currency');
        if (!$providerOrderId || !$paymentId) return response('Missing payment data',400);
        $attempt = PaymentAttempt::where('provider_order_id',$providerOrderId)->first();
        if (!$attempt) return response('Unknown order',404);
        if ((int)$amount !== (int)$attempt->amount_paise || $currency !== 'INR') {
            Log::warning('Razorpay amount/currency mismatch',['attempt_id'=>$attempt->id]);
            return response('Amount mismatch',400);
        }
        DB::transaction(function () use ($attempt,$paymentId,$event) {
            $lockedAttempt = PaymentAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();
            $order = Order::whereKey($lockedAttempt->order_id)->lockForUpdate()->firstOrFail();
            if ($order->payment_status === 'paid') return;
            $lockedAttempt->update(['provider_payment_id'=>$paymentId,'status'=>'captured','verified_at'=>now(),'payload_json'=>['event'=>'payment.captured']]);
            $order->update([
                'status'=>'paid',
                'payment_status'=>'paid',
                'payment_method'=>'razorpay_gateway',
                'payment_reference'=>$paymentId,
                'paid_at'=>now()
            ]);
            Enrollment::firstOrCreate(
                ['user_id'=>$order->user_id,'course_id'=>$order->course_id],
                ['order_id'=>$order->id,'status'=>'active','enrolled_at'=>now()]
            );
        });
        return response('OK',200);
    }
}
