@extends('layouts.app')
@section('title', __('Manage Vouchers & Coupons') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
            <h1>{{ __('Vouchers & Discount Coupons') }}</h1>
            <p class="section-subtitle">{{ __('Create promo codes for percentage or fixed BDT discounts with usage limits and expiry dates.') }}</p>
        </div>
        <div style="display:flex;gap:10px">
            <a class="btn btn-outline" href="{{ route('admin.dashboard') }}">← {{ __('Dashboard') }}</a>
            <a class="btn btn-primary" href="{{ route('admin.vouchers.create') }}">+ {{ __('Create Voucher') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div role="status" style="margin:16px 0;padding:12px;border-radius:9px;background:#e4f8ed;color:#11633d">{{ session('success') }}</div>
    @endif

    <div style="overflow-x:auto;margin-top:20px;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
        <table style="width:100%;border-collapse:collapse;min-width:700px">
            <thead>
                <tr style="background:#f5f8fc;text-align:left">
                    <th style="padding:13px">{{ __('Code') }}</th>
                    <th style="padding:13px">{{ __('Discount') }}</th>
                    <th style="padding:13px">{{ __('Usage / Limit') }}</th>
                    <th style="padding:13px">{{ __('Validity') }}</th>
                    <th style="padding:13px">{{ __('Status') }}</th>
                    <th style="padding:13px;text-align:right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $voucher)
                    <tr style="border-top:1px solid #edf1f6">
                        <td style="padding:13px">
                            <strong style="letter-spacing:1px;font-family:monospace;font-size:15px;color:#0868bd">{{ $voucher->code }}</strong>
                        </td>
                        <td style="padding:13px;font-weight:700">
                            @if($voucher->discount_type === 'percent')
                                {{ bn_number($voucher->discount_value) }}% {{ __('OFF') }}
                            @else
                                {{ bdt($voucher->discount_value, true) }} {{ __('OFF') }}
                            @endif
                        </td>
                        <td style="padding:13px">
                            {{ bn_number($voucher->times_used ?? 0) }} / {{ $voucher->max_uses ? bn_number($voucher->max_uses) : '∞' }}
                        </td>
                        <td style="padding:13px;font-size:12px;color:#64748b">
                            @if($voucher->expires_at)
                                {{ __('Expires') }}: {{ bangla_date($voucher->expires_at) }}
                            @else
                                {{ __('No expiration') }}
                            @endif
                        </td>
                        <td style="padding:13px">
                            <span class="pill" style="{{ $voucher->is_active ? 'background:#e4f8ed;color:#078451' : 'background:#fee2e2;color:#991b1b' }}">
                                {{ $voucher->is_active ? __('Active') : __('Disabled') }}
                            </span>
                        </td>
                        <td style="padding:13px;text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.vouchers.edit', $voucher) }}">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" style="display:inline" onsubmit="return confirm('Delete this voucher code?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="border:0;background:none;color:#c52c4b;cursor:pointer;margin-left:10px">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:24px;text-align:center">{{ __('No vouchers created yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px">{{ $vouchers->links() }}</div>
</div></section>
@endsection
