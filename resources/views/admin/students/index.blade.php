@extends('layouts.app')
@section('title', 'Manage Students | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container">
<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap"><div><p class="eyebrow">ADMINISTRATION</p><h1>Students</h1><p class="section-subtitle">Find student accounts, enrolments and order history.</p></div><a class="btn" href="{{ route('admin.dashboard') }}">← Dashboard</a></div>
<form method="GET" style="display:flex;gap:10px;margin:20px 0"><input name="q" value="{{ request('q') }}" placeholder="Search name or email" style="flex:1;min-width:200px;padding:11px;border:1px solid #d8e1ec;border-radius:8px"><button class="btn btn-primary">Search</button><a class="btn" href="{{ route('admin.students.index') }}">Reset</a></form>
<div style="overflow-x:auto;background:#fff;border:1px solid #e3eaf2;border-radius:14px"><table style="width:100%;border-collapse:collapse;min-width:700px">
<thead><tr style="background:#f5f8fc;text-align:left"><th style="padding:13px">Student</th><th style="padding:13px">Role</th><th style="padding:13px">Enrolments</th><th style="padding:13px">Orders</th><th style="padding:13px">Joined</th><th style="padding:13px">Details</th></tr></thead><tbody>
@forelse($students as $student)<tr style="border-top:1px solid #edf1f6"><td style="padding:13px">{{ $student->name }}<br><small>{{ $student->email }}</small></td><td style="padding:13px">{{ ($student->is_admin ?? false) ? 'Admin' : 'Student' }}</td><td style="padding:13px">{{ $student->enrollments_count }}</td><td style="padding:13px">{{ $student->orders_count }}</td><td style="padding:13px">{{ $student->created_at?->format('d M Y') }}</td><td style="padding:13px"><a href="{{ route('admin.students.show',$student) }}">View</a></td></tr>
@empty<tr><td colspan="6" style="padding:24px;text-align:center">No students found.</td></tr>@endforelse
</tbody></table></div><div style="margin-top:18px">{{ $students->links() }}</div>
</div></section>
@endsection
