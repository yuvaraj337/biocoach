@extends('layouts.app')
@section('title','Order Details | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:900px">
<a href="{{ route('admin.orders.index') }}">← Orders & payments</a><p class="eyebrow" style="margin-top:18px">ORDER REVIEW</p><h1>{{ $order->order_number }}</h1>
@if(session('success'))<div role="status" style="padding:12px;background:#e4f8ed;color:#11633d;border-radius:9px;margin:14px 0">{{ session('success') }}</div>@endif
@if($errors->any())<div role="alert" style="padding:12px;background:#fff0f2;color:#a61b38;border-radius:9px;margin:14px 0">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div style="padding:22px;background:#fff;border:1px solid #e3eaf2;border-radius:16px">
<div class="resource-grid">
<article class="resource"><div><h3>Student</h3><p>{{ $order->user->name }}<br>{{ $order->user->email }}</p></div></article>
<article class="resource"><div><h3>Course</h3><p>{{ $order->course->title }}</p></div></article>
<article class="resource"><div><h3>Amount</h3><p>{{ bdt($order->amount_paise, true) }}</p></div></article>
<article class="resource"><div><h3>Payment status</h3><p>{{ ucfirst(str_replace('_',' ',$order->payment_status)) }}</p></div></article>
</div>
<p><strong>Created:</strong> {{ $order->created_at->format('d M Y H:i:s') }}</p><p><strong>Paid at:</strong> {{ $order->paid_at?->format('d M Y H:i:s') ?? '—' }}</p><p><strong>Provider payment reference:</strong> {{ $order->payment_reference ?? '—' }}</p>
</div>
<div style="margin:20px 0;padding:16px;background:#e0f2fe;border:1px solid #bae6fd;border-radius:12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px">
  <div>
    <strong style="color:#0369a1">Manual Payment Verification:</strong>
    <p style="margin:2px 0 0;font-size:12px;color:#0c4a6e">Review student's uploaded payment proof and transaction reference, approve to unlock course access, or reject with a reason.</p>
  </div>
  <a class="btn" href="{{ route('admin.verifications.show', $order) }}">Open Verification Review →</a>
</div>
<h2 style="margin-top:28px">Payment attempts</h2><div style="overflow-x:auto;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
<table style="width:100%;border-collapse:collapse;min-width:650px"><thead><tr style="background:#f5f8fc;text-align:left"><th style="padding:12px">Provider order</th><th style="padding:12px">Payment ID</th><th style="padding:12px">Status</th><th style="padding:12px">Amount</th><th style="padding:12px">Verified</th></tr></thead><tbody>
@forelse($order->paymentAttempts as $attempt)<tr style="border-top:1px solid #edf1f6"><td style="padding:12px">{{ $attempt->provider_order_id ?? '—' }}</td><td style="padding:12px">{{ $attempt->provider_payment_id ?? '—' }}</td><td style="padding:12px">{{ ucfirst(str_replace('_',' ',$attempt->status)) }}</td><td style="padding:12px">{{ bdt($attempt->amount_paise, true) }}</td><td style="padding:12px">{{ $attempt->verified_at?->format('d M Y H:i') ?? '—' }}</td></tr>
@empty<tr><td colspan="5" style="padding:20px;text-align:center">No payment attempts recorded.</td></tr>@endforelse
</tbody></table></div>
@if($order->payment_status !== 'paid')
<div style="margin-top:28px;padding:20px;border:1px solid #f1d9a4;background:#fffaf0;border-radius:14px">
<h2>Administrative actions</h2><p>Use only after checking the provider dashboard. This panel cannot manually mark orders as paid.</p>
<form method="POST" action="{{ route('admin.orders.fail',$order) }}" style="margin-top:14px">@csrf<label for="admin_note">Reason for marking failed (required)</label><textarea id="admin_note" name="admin_note" required minlength="8" maxlength="500" rows="2" style="width:100%;padding:10px;border:1px solid #d8e1ec;border-radius:8px"></textarea><button class="btn" type="submit" style="margin-top:10px">Mark failed after review</button></form>
<form method="POST" action="{{ route('admin.orders.revoke',$order) }}" style="margin-top:16px">@csrf<label for="revoke_note">Reason for revoking course access (required)</label><textarea id="revoke_note" name="admin_note" required minlength="8" maxlength="500" rows="2" style="width:100%;padding:10px;border:1px solid #d8e1ec;border-radius:8px"></textarea><button class="btn" type="submit" style="margin-top:10px">Revoke enrolment access</button></form>
</div>@endif
</div></section>
@endsection
