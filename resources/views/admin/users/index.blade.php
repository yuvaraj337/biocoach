@extends('layouts.app')
@section('title', __('Manage Users & Roles') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
            <h1>{{ __('User & Role Management') }}</h1>
            <p class="section-subtitle">{{ __('Manage students, teachers, moderators, and administrator accounts with role-based access control.') }}</p>
        </div>
        <div style="display:flex;gap:10px">
            <a class="btn btn-outline" href="{{ route('admin.dashboard') }}">← {{ __('Dashboard') }}</a>
            <a class="btn btn-primary" href="{{ route('admin.users.create') }}">+ {{ __('Add User') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div role="status" style="margin:16px 0;padding:12px;border-radius:9px;background:#e4f8ed;color:#11633d">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">{{ session('error') }}</div>
    @endif

    <!-- Filters & Search Bar -->
    <div style="background:#fff;border:1px solid #e3eaf2;border-radius:12px;padding:16px;margin:20px 0">
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Search by name, email, or phone...') }}" style="flex:1;min-width:220px;border:1px solid #cbd5e1;border-radius:8px;padding:9px 12px;font-size:13px">
            
            <select name="role" style="border:1px solid #cbd5e1;border-radius:8px;padding:9px 12px;font-size:13px">
                <option value="">{{ __('All Roles') }}</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>{{ __('Teacher') }}</option>
                <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>{{ __('Moderator') }}</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
            </select>

            <select name="status" style="border:1px solid #cbd5e1;border-radius:8px;padding:9px 12px;font-size:13px">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>

            <button type="submit" class="mini-btn" style="padding:9px 16px">{{ __('Filter') }}</button>
            @if(request()->hasAny(['q', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" style="font-size:12px;color:#64748b;text-decoration:underline">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    <div style="overflow-x:auto;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
        <table style="width:100%;border-collapse:collapse;min-width:750px">
            <thead>
                <tr style="background:#f5f8fc;text-align:left">
                    <th style="padding:13px">{{ __('User') }}</th>
                    <th style="padding:13px">{{ __('Role') }}</th>
                    <th style="padding:13px">{{ __('Status') }}</th>
                    <th style="padding:13px">{{ __('Enrollments') }}</th>
                    <th style="padding:13px">{{ __('Joined') }}</th>
                    <th style="padding:13px;text-align:right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-top:1px solid #edf1f6">
                        <td style="padding:13px">
                            <strong>{{ $user->name }}</strong>
                            <div style="font-size:12px;color:#64748b">{{ $user->email }} @if($user->phone) • {{ $user->phone }} @endif</div>
                        </td>
                        <td style="padding:13px">
                            @php
                                $roleColors = [
                                    'super_admin' => 'background:#fee2e2;color:#991b1b',
                                    'admin' => 'background:#fef3c7;color:#92400e',
                                    'teacher' => 'background:#e0f2fe;color:#0369a1',
                                    'moderator' => 'background:#f3e8ff;color:#6b21a8',
                                    'student' => 'background:#f1f5f9;color:#334155',
                                ];
                            @endphp
                            <span class="pill" style="{{ $roleColors[$user->role] ?? 'background:#f1f5f9;color:#334155' }};font-weight:700">
                                {{ strtoupper(str_replace('_', ' ', $user->role ?? 'student')) }}
                            </span>
                        </td>
                        <td style="padding:13px">
                            <span class="pill" style="{{ $user->status === 'active' ? 'background:#e4f8ed;color:#078451' : 'background:#fee2e2;color:#991b1b' }}">
                                {{ ucfirst($user->status ?? 'active') }}
                            </span>
                        </td>
                        <td style="padding:13px;font-weight:600">{{ bn_number($user->enrollments_count) }}</td>
                        <td style="padding:13px;font-size:12px;color:#64748b">{{ bangla_date($user->created_at) }}</td>
                        <td style="padding:13px;text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}">{{ __('Edit') }}</a>
                            @if(auth()->id() !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline" onsubmit="return confirm('Are you sure you want to deactivate/delete this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="border:0;background:none;color:#c52c4b;cursor:pointer;margin-left:10px">{{ __('Delete') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:24px;text-align:center">{{ __('No users found matching query.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px">{{ $users->links() }}</div>
</div></section>
@endsection
