@extends('layouts.app')
@section('title','Orders & Payments | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container">
<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap">
<div><p class="eyebrow">ADMINISTRATION</p><h1>Orders & payments</h1><p class="section-subtitle">Revenue includes only orders marked paid through the verified webhook flow.</p></div>
<a class="btn" href="{{ route('admin.dashboard') }}">← Dashboard</a></div>
<div class="resource-grid" style="margin-bottom:20px">
<article class="resource"><div class="resource-icon">🧾</div><div><h3>{{ $stats['all'] }} orders</h3><p>All recorded orders</p></div></article>
<article class="resource"><div class="resource-icon">⏳</div><div><h3>{{ $stats['pending'] }} pending</h3><p>Awaiting verification</p></div></article>
<article class="resource"><div class="resource-icon">✅</div><div><h3>{{ $stats['paid'] }} paid</h3><p>Verified paid orders</p></div></article>
<article class="resource"><div class="resource-icon">৳</div><div><h3>{{ bdt($stats['revenue_paise'], true) }}</h3><p>Recorded paid total</p></div></article>
</div>
<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin:18px 0">
<input name="q" value="{{ request('q') }}" placeholder="Search order, student, email or course" style="flex:1;min-width:220px;padding:11px;border:1px solid #d8e1ec;border-radius:8px">
<select name="status" style="padding:11px;border:1px solid #d8e1ec;border-radius:8px"><option value="">All statuses</option>
@foreach(['pending'=>'Pending','verification_pending'=>'Verification pending','paid'=>'Paid','failed'=>'Failed','cancelled'=>'Cancelled'] as $value=>$label)<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>@endforeach
</select><button class="btn btn-primary" type="submit">Filter</button><a class="btn" href="{{ route('admin.orders.index') }}">Reset</a>
</form>
<div style="overflow-x:auto;background:#fff;border:1px solid #e3eaf2;border-radius:14px"><table style="width:100%;border-collapse:collapse;min-width:850px">
<thead><tr style="background:#f5f8fc;text-align:left"><th style="padding:13px">Order</th><th style="padding:13px">Student</th><th style="padding:13px">Course</th><th style="padding:13px">Amount</th><th style="padding:13px">Payment</th><th style="padding:13px">Created</th><th style="padding:13px">Details</th></tr></thead><tbody>
@forelse($orders as $order)<tr style="border-top:1px solid #edf1f6">
<td style="padding:13px">{{ $order->order_number }}</td><td style="padding:13px">{{ $order->user->name }}<br><small>{{ $order->user->email }}</small></td>
<td style="padding:13px">{{ $order->course->title }}</td><td style="padding:13px">{{ bdt($order->amount_paise, true) }}</td>
<td style="padding:13px">{{ ucfirst(str_replace('_',' ',$order->payment_status)) }}</td><td style="padding:13px">{{ bangla_date($order->created_at) }}</td>
<td style="padding:13px"><a href="{{ route('admin.orders.show',$order) }}">Review</a></td></tr>
@empty<tr><td colspan="7" style="padding:24px;text-align:center">No matching orders.</td></tr>@endforelse
</tbody></table></div><div style="margin-top:18px">{{ $orders->links() }}</div>
</div></section>
@endsection
