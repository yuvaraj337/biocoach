@extends('layouts.app')

@section('content')
<section class="section">
  <div class="container" style="max-width:520px">
    <div class="panel" style="padding:28px;border-radius:18px;background:#fff;border:1px solid #e6edf4">
      <p class="eyebrow">START LEARNING</p>
      <h1 style="margin:0 0 8px">Create your account</h1>
      <p class="section-subtitle">Join Jerin’s Coaching and learn without fear.</p>
      @if ($errors->any())
        <div role="alert" style="padding:12px;background:#fff0f2;color:#a61b38;border-radius:8px;margin:14px 0">
          @foreach ($errors->all() as $error)<p style="margin:0 0 4px">{{ $error }}</p>@endforeach
        </div>
      @endif
      <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <label for="name" style="display:block;margin:14px 0 6px;font-weight:700">Full name</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="120" autocomplete="name" style="width:100%;padding:12px;border:1px solid #d8e1ec;border-radius:8px">
        <label for="email" style="display:block;margin:14px 0 6px;font-weight:700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" style="width:100%;padding:12px;border:1px solid #d8e1ec;border-radius:8px">
        <label for="password" style="display:block;margin:14px 0 6px;font-weight:700">Password (at least 8 characters)</label>
        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" style="width:100%;padding:12px;border:1px solid #d8e1ec;border-radius:8px">
        <label for="password_confirmation" style="display:block;margin:14px 0 6px;font-weight:700">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" style="width:100%;padding:12px;border:1px solid #d8e1ec;border-radius:8px">
        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px">Create Account</button>
      </form>
      <p style="margin-top:18px">Already registered? <a href="{{ route('login') }}">Log in</a></p>
    </div>
  </div>
</section>
@endsection
