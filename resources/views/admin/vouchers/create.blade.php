@extends('layouts.app')
@section('title', __('Create Voucher') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container" style="max-width:650px">
    <div style="margin-bottom:20px">
        <a class="btn btn-outline" style="padding:6px 12px;font-size:12px;margin-bottom:10px;display:inline-block" href="{{ route('admin.vouchers.index') }}">← {{ __('Back to Vouchers') }}</a>
        <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
        <h1>{{ __('Create Discount Voucher') }}</h1>
    </div>

    @if($errors->any())
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div style="background:#fff;border:1px solid #e3eaf2;border-radius:14px;padding:24px">
        <form method="POST" action="{{ route('admin.vouchers.store') }}">
            @csrf

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Voucher Code') }} *</label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. BIOLOGY2026 or HSC500" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px;text-transform:uppercase;font-family:monospace">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Discount Type') }} *</label>
                    <select name="discount_type" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                        <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed_paise" {{ old('discount_type') === 'fixed_paise' ? 'selected' : '' }}>Fixed Amount (৳ BDT)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Discount Value') }} *</label>
                    <input type="number" step="any" name="discount_value" value="{{ old('discount_value') }}" required placeholder="e.g. 20 (for 20%) or 500 (for ৳500)" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Max Uses') }} ({{ __('Optional - blank for unlimited') }})</label>
                <input type="number" name="max_uses" value="{{ old('max_uses') }}" min="1" placeholder="e.g. 100" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Valid From') }} ({{ __('Optional') }})</label>
                    <input type="date" name="starts_at" value="{{ old('starts_at') }}" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Expires At') }} ({{ __('Optional') }})</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
            </div>

            <div style="margin-bottom:24px">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    {{ __('Active (Can be applied during checkout)') }}
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="padding:12px 24px">{{ __('Create Voucher') }}</button>
        </form>
    </div>
</div></section>
@endsection
