@extends('layouts.app')
@section('title', 'Test checkout | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:680px">
    <span class="pill">TEST MODE ONLY</span><h1 style="font-size:2.6rem">Checkout test</h1>
    <p>This is a development placeholder. No real payment has been taken.</p>
    <div class="card"><p><strong>Course:</strong> {{ $course->title }}</p><p><strong>Order:</strong> {{ $order->order_number }}</p><p><strong>Amount:</strong> ₹{{ number_format($order->amount_paise / 100, 2) }}</p><p><strong>Status:</strong> {{ $order->status }}</p></div>
    <p class="muted">Next step: connect a payment provider sandbox and implement server-side signature/webhook verification. Do not mark an order as paid from a browser-only action.</p>
</div></section>
@endsection
