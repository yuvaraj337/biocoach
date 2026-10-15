@extends('layouts.app')
@section('title', 'Checkout | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:700px">
  <p class="eyebrow">SECURE CHECKOUT</p><h1>Order created</h1>
  <div style="padding:24px;background:#fff;border:1px solid #e3eaf2;border-radius:16px;margin-top:20px">
    <p>Your order for <strong>{{ $course->title }}</strong> has been created.</p>
    <p>Order number: <strong>{{ $order->order_number }}</strong></p>
    <p>Amount: <strong>₹{{ number_format($order->amount_paise / 100, 2) }}</strong></p>
    <p style="padding:12px;border-radius:8px;background:#fff5d9;color:#765100">Payment status: Pending. This project has not yet been connected to a payment gateway. Do not treat this order as paid.</p>
    <a class="btn btn-primary" href="{{ route('student.orders') }}">View my orders</a>
    <a class="btn" href="{{ route('courses.index') }}">Back to courses</a>
  </div>
</div></section>
@endsection
