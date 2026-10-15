@extends('layouts.app')
@section('title', 'Admin Audit Log | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
    <div><p class="eyebrow">SECURITY & ACCOUNTABILITY</p><h1>Admin audit log</h1><p class="section-subtitle">Review recorded administrator actions and their before/after values.</p></div>
    <a class="btn" href="{{ route('admin.dashboard') }}">← Dashboard</a>
  </div>
  <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin:20px 0">
    <input name="q" value="{{ request('q') }}" placeholder="Search action, subject or reason" style="flex:1;min-width:220px;padding:11px;border:1px solid #d8e1ec;border-radius:8px">
    <select name="action" style="padding:11px;border:1px solid #d8e1ec;border-radius:8px"><option value="">All actions</option>@foreach($actions as $action)<option value="{{ $action }}" @selected(request('action')===$action)>{{ $action }}</option>@endforeach</select>
    <button class="btn btn-primary" type="submit">Filter</button><a class="btn" href="{{ route('admin.audit.index') }}">Reset</a>
  </form>
  <div style="overflow-x:auto;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
  <table style="width:100%;border-collapse:collapse;min-width:850px">
    <thead><tr style="background:#f5f8fc;text-align:left"><th style="padding:13px">Time</th><th style="padding:13px">Admin</th><th style="padding:13px">Action</th><th style="padding:13px">Subject</th><th style="padding:13px">Reason</th><th style="padding:13px">Changes</th></tr></thead>
    <tbody>
    @forelse($logs as $log)
    <tr style="border-top:1px solid #edf1f6">
      <td style="padding:13px;white-space:nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
      <td style="padding:13px">{{ $log->admin?->name ?? 'Deleted admin' }}<br><small>ID: {{ $log->admin_user_id ?? '—' }}</small></td>
      <td style="padding:13px">{{ $log->action }}</td>
      <td style="padding:13px">{{ $log->subject_type }} #{{ $log->subject_id ?? '—' }}</td>
      <td style="padding:13px">{{ $log->reason ?? '—' }}</td>
      <td style="padding:13px"><details><summary>View</summary><strong>Before</strong><pre style="white-space:pre-wrap">{{ json_encode($log->before_json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre><strong>After</strong><pre style="white-space:pre-wrap">{{ json_encode($log->after_json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre><small>IP: {{ $log->ip_address ?? '—' }}</small></details></td>
    </tr>
    @empty<tr><td colspan="6" style="padding:24px;text-align:center">No audit records yet.</td></tr>@endforelse
    </tbody>
  </table></div>
  <div style="margin-top:18px">{{ $logs->links() }}</div>
</div></section>
@endsection
