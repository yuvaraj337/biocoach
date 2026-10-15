@extends('layouts.app')
@section('title', __('Add User') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container" style="max-width:650px">
    <div style="margin-bottom:20px">
        <a class="btn btn-outline" style="padding:6px 12px;font-size:12px;margin-bottom:10px;display:inline-block" href="{{ route('admin.users.index') }}">← {{ __('Back to Users') }}</a>
        <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
        <h1>{{ __('Add New User') }}</h1>
    </div>

    @if($errors->any())
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div style="background:#fff;border:1px solid #e3eaf2;border-radius:14px;padding:24px">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Full Name') }} *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Dr. Tanjina Jerin" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Email Address') }} *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Phone Number') }} ({{ __('Optional') }})</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="017xxxxxxxx" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Password') }} *</label>
                <input type="password" name="password" required minlength="6" placeholder="Minimum 6 characters" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Role') }} *</label>
                    <select name="role" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="moderator" {{ old('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                        @if(auth()->user()->isSuperAdmin())
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Status') }} *</label>
                    <select name="status" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="padding:12px 24px">{{ __('Create User') }}</button>
        </form>
    </div>
</div></section>
@endsection
