@extends('layouts.app')
@section('title', 'Sandbox Checkout | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:720px">
<p class="eyebrow">TEST MODE CHECKOUT</p><h1>Complete your order</h1>
<div style="padding:24px;background:#fff;border:1px solid #e3eaf2;border-radius:16px;margin-top:20px">
<p>Course: <strong>{{ $order->course->title }}</strong></p>
<p>Order: <strong>{{ $order->order_number }}</strong></p>
<p>Amount: <strong>₹{{ number_format($order->amount_paise / 100, 2) }}</strong></p>
<p style="padding:12px;background:#e8f2ff;color:#104a84;border-radius:8px">Sandbox checkout only. Use test credentials; no live charge should be made.</p>
<button id="pay-button" class="btn btn-primary" type="button">Pay with Razorpay Test Mode</button><p id="payment-error" role="alert"></p>
</div></div></section>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function(){
const button=document.getElementById('pay-button'), error=document.getElementById('payment-error');
button.addEventListener('click',function(){
 if(!window.Razorpay){error.textContent='Checkout library could not load.';return;}
 const checkout=new Razorpay({
  key:@json($keyId),amount:@json($attempt->amount_paise),currency:'INR',
  name:'Jerin’s Coaching',description:@json($order->course->title),order_id:@json($attempt->provider_order_id),
  handler:function(response){
   const form=document.createElement('form');form.method='POST';form.action=@json(route('payments.verify',$order));
   const csrf=document.createElement('input');csrf.type='hidden';csrf.name='_token';csrf.value=@json(csrf_token());form.appendChild(csrf);
   ['razorpay_payment_id','razorpay_order_id','razorpay_signature'].forEach(function(key){
    const input=document.createElement('input');input.type='hidden';input.name=key;input.value=response[key];form.appendChild(input);
   });
   document.body.appendChild(form);form.submit();
  },
  modal:{ondismiss:function(){error.textContent='Checkout closed. Order remains pending.';}},
  theme:{color:'#0868bd'}
 });checkout.open();
});
})();
</script>
@endsection
