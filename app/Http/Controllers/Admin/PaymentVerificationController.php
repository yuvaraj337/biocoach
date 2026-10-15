<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'awaiting');
        $query = Order::with(['user', 'course', 'verifier'])->latest();

        if ($status === 'awaiting') {
            $query->where('payment_status', 'verification_pending');
        } elseif ($status === 'pending') {
            $query->whereIn('payment_status', ['pending', 'verification_pending']);
        } elseif ($status === 'verified') {
            $query->where('payment_status', 'paid');
        } elseif ($status === 'rejected') {
            $query->where('payment_status', 'rejected');
        }

        if ($request->filled('q')) {
            $term = trim((string)$request->q);
            $query->where(function ($sub) use ($term) {
                $sub->where('order_number', 'like', "%{$term}%")
                    ->orWhere('transaction_id', 'like', "%{$term}%")
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"))
                    ->orWhereHas('course', fn($q) => $q->where('title', 'like', "%{$term}%"));
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'awaiting' => Order::where('payment_status', 'verification_pending')->count(),
            'pending_submission' => Order::where('payment_status', 'pending')->count(),
            'verified' => Order::where('payment_status', 'paid')->count(),
            'rejected' => Order::where('payment_status', 'rejected')->count(),
            'all' => Order::count(),
        ];

        return view('admin.verifications.index', compact('orders', 'counts', 'status'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'course', 'verifier', 'paymentAttempts']);
        return view('admin.verifications.show', compact('order'));
    }

    public function verify(Request $request, Order $order)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($order->isPaid()) {
            return back()->withErrors(['order' => 'This order is already marked as verified and paid.']);
        }

        DB::transaction(function () use ($request, $order) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->isPaid()) {
                return;
            }

            $before = [
                'status' => $lockedOrder->status,
                'payment_status' => $lockedOrder->payment_status,
                'verified_by' => $lockedOrder->verified_by,
            ];

            $lockedOrder->update([
                'status' => 'paid',
                'payment_status' => 'paid',
                'paid_at' => now(),
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
                'verification_notes' => $request->input('admin_note', 'Verified by administrator.'),
                'payment_reference' => $lockedOrder->transaction_id ?: $lockedOrder->payment_reference,
            ]);

            // Activate course enrollment for student
            Enrollment::updateOrCreate(
                [
                    'user_id' => $lockedOrder->user_id,
                    'course_id' => $lockedOrder->course_id,
                ],
                [
                    'order_id' => $lockedOrder->id,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]
            );

            AdminAuditLog::create([
                'admin_user_id' => $request->user()->id,
                'action' => 'payment.manually_verified',
                'subject_type' => Order::class,
                'subject_id' => $lockedOrder->id,
                'reason' => $request->input('admin_note', 'Manual payment verified by teacher; course access activated.'),
                'before_json' => $before,
                'after_json' => [
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'verified_by' => $request->user()->id,
                    'verified_at' => now()->toIso8601String(),
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.verifications.show', $order)->with(
            'success',
            "Payment verified! {$order->course->title} has been successfully unlocked for {$order->user->name}."
        );
    }

    public function reject(Request $request, Order $order)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        if ($order->isPaid()) {
            return back()->withErrors(['order' => 'Paid orders cannot be marked as rejected.']);
        }

        DB::transaction(function () use ($request, $order, $data) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            $before = [
                'status' => $lockedOrder->status,
                'payment_status' => $lockedOrder->payment_status,
                'rejection_reason' => $lockedOrder->rejection_reason,
            ];

            $lockedOrder->update([
                'status' => 'rejected',
                'payment_status' => 'rejected',
                'rejection_reason' => $data['rejection_reason'],
            ]);

            // Revoke any pending enrollment
            Enrollment::where('user_id', $lockedOrder->user_id)
                ->where('course_id', $lockedOrder->course_id)
                ->where('order_id', $lockedOrder->id)
                ->update(['status' => 'revoked']);

            AdminAuditLog::create([
                'admin_user_id' => $request->user()->id,
                'action' => 'payment.rejected',
                'subject_type' => Order::class,
                'subject_id' => $lockedOrder->id,
                'reason' => $data['rejection_reason'],
                'before_json' => $before,
                'after_json' => [
                    'status' => 'rejected',
                    'payment_status' => 'rejected',
                    'rejection_reason' => $data['rejection_reason'],
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.verifications.show', $order)->with(
            'success',
            'Payment rejected. Rejection reason has been recorded and the student can now review and resubmit their payment proof.'
        );
    }
}
