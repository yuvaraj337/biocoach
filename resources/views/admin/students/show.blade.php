@extends('layouts.app')
@section('title', 'Student Details | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:1000px">
<a href="{{ route('admin.students.index') }}">← Students</a><p class="eyebrow" style="margin-top:18px">STUDENT RECORD</p><h1>{{ $student->name }}</h1>
@if(session('success'))<div role="status" style="padding:12px;background:#e4f8ed;color:#11633d;border-radius:9px;margin:14px 0">{{ session('success') }}</div>@endif
@if($errors->any())<div role="alert" style="padding:12px;background:#fff0f2;color:#a61b38;border-radius:9px;margin:14px 0">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div style="padding:22px;background:#fff;border:1px solid #e3eaf2;border-radius:16px"><p><strong>Email:</strong> {{ $student->email }}</p><p><strong>Account type:</strong> {{ ($student->is_admin ?? false) ? 'Administrator' : 'Student' }}</p><p><strong>Joined:</strong> {{ $student->created_at?->format('d M Y H:i') }}</p></div>
<h2 style="margin-top:28px">Enrolments</h2>
<div style="overflow-x:auto;background:#fff;border:1px solid #e3eaf2;border-radius:14px"><table style="width:100%;border-collapse:collapse;min-width:850px"><thead><tr style="background:#f5f8fc;text-align:left"><th style="padding:12px">Course</th><th style="padding:12px">Status</th><th style="padding:12px">Enrolled</th><th style="padding:12px">Manage access</th></tr></thead><tbody>
@forelse($student->enrollments as $enrollment)<tr style="border-top:1px solid #edf1f6"><td style="padding:12px">{{ $enrollment->course->title }}</td><td style="padding:12px">{{ ucfirst($enrollment->status) }}</td><td style="padding:12px">{{ $enrollment->enrolled_at?->format('d M Y') ?? '—' }}</td><td style="padding:12px">
<form method="POST" action="{{ route('admin.students.enrollment.update',[$student,$enrollment]) }}" style="display:flex;gap:8px;flex-wrap:wrap">@csrf @method('PATCH')
<select name="status" style="padding:8px;border:1px solid #d8e1ec;border-radius:7px"><option value="active" @selected($enrollment->status==='active')>Active</option><option value="revoked" @selected($enrollment->status==='revoked')>Revoked</option></select>
<input name="reason" required minlength="8" maxlength="500" placeholder="Reason (required)" style="padding:8px;border:1px solid #d8e1ec;border-radius:7px;min-width:170px"><button class="mini-btn">Save</button></form></td></tr>
@empty<tr><td colspan="4" style="padding:20px;text-align:center">No enrolments.</td></tr>@endforelse
</tbody></table></div>
<h2 style="margin-top:28px">Order history</h2><div style="overflow-x:auto;background:#fff;border:1px solid #e3eaf2;border-radius:14px"><table style="width:100%;border-collapse:collapse;min-width:650px"><thead><tr style="background:#f5f8fc;text-align:left"><th style="padding:12px">Order</th><th style="padding:12px">Course</th><th style="padding:12px">Amount</th><th style="padding:12px">Payment status</th><th style="padding:12px">Created</th></tr></thead><tbody>
@forelse($student->orders as $order)<tr style="border-top:1px solid #edf1f6"><td style="padding:12px"><a href="{{ route('admin.orders.show',$order) }}">{{ $order->order_number }}</a></td><td style="padding:12px">{{ $order->course->title }}</td><td style="padding:12px">{{ bdt($order->amount_paise, true) }}</td><td style="padding:12px">{{ ucfirst(str_replace('_',' ',$order->payment_status)) }}</td><td style="padding:12px">{{ bangla_date($order->created_at) }}</td></tr>
@empty<tr><td colspan="5" style="padding:20px;text-align:center">No orders.</td></tr>@endforelse
</tbody></table></div>
</div></section>
@endsection
