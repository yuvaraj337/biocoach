@extends('layouts.app')

@section('content')
<section class="section">
  <div class="container" style="max-width:520px">
    <div class="panel" style="padding:28px;border-radius:18px;background:#fff;border:1px solid #e6edf4">
      <p class="eyebrow">WELCOME BACK</p>
      <h1 style="margin:0 0 8px">Log in to learn</h1>
      <p class="section-subtitle">Continue your learning journey with Jerin’s Coaching.</p>
      @if ($errors->any())
        <div role="alert" style="padding:12px;background:#fff0f2;color:#a61b38;border-radius:8px;margin:14px 0">
          @foreach ($errors->all() as $error)<p style="margin:0 0 4px">{{ $error }}</p>@endforeach
        </div>
      @endif
      @if (session('success'))<p role="status">{{ session('success') }}</p>@endif
      <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email" style="display:block;margin:14px 0 6px;font-weight:700">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" style="width:100%;padding:12px;border:1px solid #d8e1ec;border-radius:8px">
        <label for="password" style="display:block;margin:14px 0 6px;font-weight:700">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password" style="width:100%;padding:12px;border:1px solid #d8e1ec;border-radius:8px">
        <label style="display:flex;align-items:center;gap:8px;margin:14px 0"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button type="submit" class="btn btn-primary" style="width:100%">Log In</button>
      </form>
      <p style="margin-top:18px">New here? <a href="{{ route('register') }}">Create an account</a></p>
    </div>
  </div>
</section>
@endsection
