<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function start(Request $request, Course $course)
    {
        abort_unless($course->is_published, 404);

        $alreadyEnrolled = $request->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->route('student.courses')->with('success', 'You already have active access to this course.');
        }

        // Check if student has an existing order awaiting payment/verification
        $existingOrder = $request->user()->orders()
            ->where('course_id', $course->id)
            ->whereIn('payment_status', ['pending', 'verification_pending', 'rejected'])
            ->latest()
            ->first();

        if ($existingOrder) {
            return redirect()->route('checkout.show', $existingOrder);
        }

        $order = Order::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'order_number' => 'JC-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
            'amount_paise' => $course->price_paise,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'manual_upi',
        ]);

        return redirect()->route('checkout.show', $order);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless((int)$order->user_id === (int)$request->user()->id, 403, 'Unauthorized order access.');

        $order->load('course');
        $settings = PaymentSetting::allSettings();

        $upiId = $settings['upi_id'] ?? '';
        $businessName = urlencode($settings['business_name'] ?? "Jerin's Coaching");
        $amountRupees = number_format($order->amount_paise / 100, 2, '.', '');
        $orderNumber = urlencode($order->order_number);
        $upiUri = "upi://pay?pa={$upiId}&pn={$businessName}&am={$amountRupees}&cu=INR&tn=Order%20{$orderNumber}";

        return view('checkout.show', compact('order', 'settings', 'upiUri'));
    }

    public function submitProof(Request $request, Order $order)
    {
        abort_unless((int)$order->user_id === (int)$request->user()->id, 403, 'Unauthorized order access.');

        if ($order->isPaid()) {
            return redirect()->route('checkout.show', $order)->with('success', 'This order is already verified and active.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:bkash,nagad,rocket,manual_bank,manual_upi'],
            'transaction_id' => ['required', 'string', 'min:4', 'max:100'],
            'sender_phone' => ['nullable', 'string', 'max:30'],
            'payment_proof' => ['required', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'], // 5MB max
            'verification_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $request->file('payment_proof');
        $storedPath = $file->store('payment_proofs', 'local');

        $notes = $validated['verification_notes'] ?? '';
        if (!empty($validated['sender_phone'])) {
            $notes = 'Sender: ' . $validated['sender_phone'] . ($notes ? ' | ' . $notes : '');
        }

        $order->update([
            'payment_method' => $validated['payment_method'],
            'transaction_id' => trim($validated['transaction_id']),
            'payment_proof_path' => $storedPath,
            'payment_proof_original_name' => $file->getClientOriginalName(),
            'proof_submitted_at' => now(),
            'status' => 'verification_pending',
            'payment_status' => 'verification_pending',
            'rejection_reason' => null, // reset previous rejection if re-submitting
            'verification_notes' => $notes ?: null,
        ]);

        return redirect()->route('checkout.show', $order)->with('success', __('Payment proof submitted! Dr. Jerin will verify your payment and activate your course.'));
    }

    public function applyVoucher(Request $request, Order $order)
    {
        abort_unless((int)$order->user_id === (int)$request->user()->id, 403);
        abort_if($order->isPaid(), 400);

        $request->validate(['voucher_code' => ['required', 'string', 'max:50']]);
        $code = strtoupper(trim($request->input('voucher_code')));

        $voucher = \App\Models\Voucher::where('code', $code)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$voucher) {
            return redirect()->route('checkout.show', $order)->with('error', 'Invalid or expired voucher code.');
        }

        if ($voucher->max_uses && $voucher->uses >= $voucher->max_uses) {
            return redirect()->route('checkout.show', $order)->with('error', 'This voucher has reached its usage limit.');
        }

        $baseAmount = $order->course->price_paise;
        $discountPaise = 0;

        if ($voucher->discount_type === 'percent') {
            $discountPaise = (int) round(($baseAmount * $voucher->discount_value) / 100);
        } else {
            $discountPaise = min($baseAmount, (int) $voucher->discount_value);
        }

        $newAmount = max(0, $baseAmount - $discountPaise);
        $order->update(['amount_paise' => $newAmount]);
        $voucher->increment('uses');

        return redirect()->route('checkout.show', $order)->with('success', "Voucher '{$code}' applied successfully!");
    }
}
