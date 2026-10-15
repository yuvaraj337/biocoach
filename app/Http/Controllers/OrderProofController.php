<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderProofController extends Controller
{
    public function show(Request $request, Order $order)
    {
        $user = $request->user();
        abort_unless(
            $user->is_admin || ($user->role ?? '') === 'admin' || (int)$order->user_id === (int)$user->id,
            403,
            'You are not authorized to view this payment proof.'
        );

        abort_unless(
            $order->payment_proof_path && Storage::disk('local')->exists($order->payment_proof_path),
            404,
            'Payment proof file was not found on server.'
        );

        return Storage::disk('local')->response(
            $order->payment_proof_path,
            $order->payment_proof_original_name ?: 'payment_proof'
        );
    }
}
