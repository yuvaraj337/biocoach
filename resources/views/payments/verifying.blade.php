@extends('layouts.app')
@section('title','Verifying Payment | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:680px">
<p class="eyebrow">PAYMENT VERIFICATION</p><h1>We’re checking your payment</h1>
<p>Your checkout signature was verified. Course access is enabled only after the signed provider webhook confirms payment capture.</p>
<p>Order: <strong>{{ $order->order_number }}</strong></p>
<a class="btn btn-primary" href="{{ route('student.orders') }}">View order status</a>
</div></section>
@endsection
