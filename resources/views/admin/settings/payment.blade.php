@extends('layouts.app')
@section('title', __('Business Payment Settings') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section" style="background:#f8fafc;padding:32px 0 60px">
  <div class="container" style="max-width:900px">
    
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px">
      <div>
        <p class="eyebrow">{{ __('ADMINISTRATION SETTINGS') }}</p>
        <h1 style="margin:4px 0 8px;font-size:30px">{{ __('Bangladesh Payment Configuration') }}</h1>
        <p class="section-subtitle">{{ __('Configure official bKash, Nagad, Rocket, Bank transfer details and SSLCommerz payment gateway.') }}</p>
      </div>
      <div style="display:flex;gap:10px">
        <a class="btn btn-outline" href="{{ route('admin.verifications.index') }}">← {{ __('Verifications') }}</a>
        <a class="btn" href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
      </div>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:20px;font-weight:600">
        ✓ {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div role="alert" style="padding:14px 18px;background:#fff0f2;border:1px solid #fecdd3;color:#a61b38;border-radius:10px;margin-bottom:20px">
        <strong style="display:block;margin-bottom:6px">Please resolve the following:</strong>
        <ul style="margin:0;padding-left:20px;font-size:13px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.payment-settings.update') }}">
      @csrf

      <!-- Business Name -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
        <h3 style="margin:0 0 14px;font-size:16px;color:var(--navy)">{{ __('General Business Details') }}</h3>
        <div>
          <label for="business_name" style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:6px">{{ __('Business / Brand Name') }} *</label>
          <input type="text" id="business_name" name="business_name" value="{{ old('business_name', $settings['business_name'] ?? 'Jerin’s Coaching') }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px">
        </div>
      </div>

      <!-- SSLCommerz Gateway Card -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-wrap:wrap;gap:10px">
          <h3 style="margin:0;font-size:16px;color:var(--navy)">💳 SSLCommerz Bangladesh Gateway</h3>
          <span style="font-size:11px;background:#e0f2fe;color:#0369a1;padding:3px 8px;border-radius:6px;font-weight:700">Official Server-Side Flow</span>
        </div>

        <div style="display:grid;gap:14px">
          <label style="display:flex;align-items:flex-start;gap:12px;padding:12px;border:1px solid #cbd5e1;border-radius:10px;background:#f8fafc;cursor:pointer">
            <input type="checkbox" name="sslcommerz_enabled" value="1" @checked(old('sslcommerz_enabled', $settings['sslcommerz_enabled'] ?? true)) style="margin-top:3px;transform:scale(1.2)">
            <div>
              <strong style="font-size:13px;color:#0f172a">{{ __('Enable SSLCommerz Automated Gateway') }}</strong>
              <p style="margin:2px 0 0;font-size:12px;color:#64748b">
                {{ __('Accepts cards (Visa, Mastercard), mobile banking, and internet banking with instant automated order fulfillment.') }}
              </p>
            </div>
          </label>

          <label style="display:flex;align-items:flex-start;gap:12px;padding:12px;border:1px solid #fde047;border-radius:10px;background:#fefce8;cursor:pointer">
            <input type="checkbox" name="sslcommerz_sandbox" value="1" @checked(old('sslcommerz_sandbox', $settings['sslcommerz_sandbox'] ?? true)) style="margin-top:3px;transform:scale(1.2)">
            <div>
              <strong style="font-size:13px;color:#854d0e">{{ __('Sandbox / Testing Mode') }}</strong>
              <p style="margin:2px 0 0;font-size:12px;color:#713f12">
                {{ __('Keep enabled for sandbox testing. Uncheck only when production merchant credentials from SSLCommerz are ready.') }}
              </p>
            </div>
          </label>

          <div>
            <label for="sslcommerz_store_id" style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:6px">{{ __('Store ID (Configured in .env or override here)') }}</label>
            <input type="text" id="sslcommerz_store_id" name="sslcommerz_store_id" value="{{ old('sslcommerz_store_id', $settings['sslcommerz_store_id'] ?? 'testbox') }}" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;font-family:monospace">
            <small style="display:block;color:#64748b;margin-top:4px;font-size:11px">Default test store: <code>testbox</code> with password <code>qwerty</code></small>
          </div>
        </div>
      </div>

      <!-- Bangladesh Mobile Banking (bKash, Nagad, Rocket) -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
        <h3 style="margin:0 0 16px;font-size:16px;color:var(--navy)">📱 {{ __('Manual Mobile Banking (bKash / Nagad / Rocket)') }}</h3>

        <!-- bKash -->
        <div style="border:1px solid #fbcfe8;border-radius:10px;padding:16px;background:#fff1f2;margin-bottom:16px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:700;color:#9d174d;font-size:14px">
              <input type="checkbox" name="manual_bkash_enabled" value="1" @checked(old('manual_bkash_enabled', $settings['manual_bkash_enabled'] ?? true))>
              {{ __('Enable bKash Manual Payment') }}
            </label>
            <span style="font-size:11px;font-weight:800;color:#e11d48;background:#ffe4e6;padding:2px 8px;border-radius:4px">bKash</span>
          </div>
          <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px">
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">{{ __('bKash Number') }}</label>
              <input type="text" name="bkash_number" value="{{ old('bkash_number', $settings['bkash_number'] ?? '01712-345678') }}" placeholder="017xxxxxxxx" style="width:100%;padding:8px 12px;border:1px solid #f43f5e;border-radius:6px;font-size:13px;font-family:monospace">
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">{{ __('Account Type') }}</label>
              <select name="bkash_type" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px">
                <option value="Merchant" {{ ($settings['bkash_type'] ?? '') === 'Merchant' ? 'selected' : '' }}>Merchant</option>
                <option value="Personal" {{ ($settings['bkash_type'] ?? '') === 'Personal' ? 'selected' : '' }}>Personal</option>
                <option value="Agent" {{ ($settings['bkash_type'] ?? '') === 'Agent' ? 'selected' : '' }}>Agent</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Nagad -->
        <div style="border:1px solid #fed7aa;border-radius:10px;padding:16px;background:#fff7ed;margin-bottom:16px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:700;color:#c2410c;font-size:14px">
              <input type="checkbox" name="manual_nagad_enabled" value="1" @checked(old('manual_nagad_enabled', $settings['manual_nagad_enabled'] ?? true))>
              {{ __('Enable Nagad Manual Payment') }}
            </label>
            <span style="font-size:11px;font-weight:800;color:#ea580c;background:#ffedd5;padding:2px 8px;border-radius:4px">Nagad</span>
          </div>
          <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px">
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">{{ __('Nagad Number') }}</label>
              <input type="text" name="nagad_number" value="{{ old('nagad_number', $settings['nagad_number'] ?? '01812-345678') }}" placeholder="018xxxxxxxx" style="width:100%;padding:8px 12px;border:1px solid #f97316;border-radius:6px;font-size:13px;font-family:monospace">
            </div>
            <div>
              <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">{{ __('Account Type') }}</label>
              <select name="nagad_type" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px">
                <option value="Merchant" {{ ($settings['nagad_type'] ?? '') === 'Merchant' ? 'selected' : '' }}>Merchant</option>
                <option value="Personal" {{ ($settings['nagad_type'] ?? '') === 'Personal' ? 'selected' : '' }}>Personal</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Rocket -->
        <div style="border:1px solid #e9d5ff;border-radius:10px;padding:16px;background:#faf5ff">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:700;color:#7e22ce;font-size:14px">
              <input type="checkbox" name="manual_rocket_enabled" value="1" @checked(old('manual_rocket_enabled', $settings['manual_rocket_enabled'] ?? true))>
              {{ __('Enable Rocket (DBBL) Manual Payment') }}
            </label>
            <span style="font-size:11px;font-weight:800;color:#9333ea;background:#f3e8ff;padding:2px 8px;border-radius:4px">Rocket</span>
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px">{{ __('Rocket Number (with 12th check digit)') }}</label>
            <input type="text" name="rocket_number" value="{{ old('rocket_number', $settings['rocket_number'] ?? '01912-345678-9') }}" placeholder="019xxxxxxxx-x" style="width:100%;padding:8px 12px;border:1px solid #a855f7;border-radius:6px;font-size:13px;font-family:monospace">
          </div>
        </div>
      </div>

      <!-- Official Bangladesh Bank Account -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
          <h3 style="margin:0;font-size:16px;color:var(--navy)">🏦 {{ __('Bangladesh Bank Account Details') }}</h3>
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:700;font-size:13px">
            <input type="checkbox" name="manual_bank_enabled" value="1" @checked(old('manual_bank_enabled', $settings['manual_bank_enabled'] ?? true))>
            {{ __('Enable Bank Transfer') }}
          </label>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div>
            <label style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:4px">{{ __('Bank Name') }}</label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? 'Dutch-Bangla Bank PLC') }}" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:4px">{{ __('Branch Name') }}</label>
            <input type="text" name="branch_name" value="{{ old('branch_name', $settings['branch_name'] ?? 'Dhanmondi Branch, Dhaka') }}" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:4px">{{ __('Account Holder Name') }}</label>
            <input type="text" name="account_holder" value="{{ old('account_holder', $settings['account_holder'] ?? 'Dr. Tanjina Jerin') }}" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:4px">{{ __('Account Number') }}</label>
            <input type="text" name="account_number" value="{{ old('account_number', $settings['account_number'] ?? '115.101.0029345') }}" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;font-family:monospace">
          </div>
          <div>
            <label style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:4px">{{ __('Routing Number') }}</label>
            <input type="text" name="routing_number" value="{{ old('routing_number', $settings['routing_number'] ?? '090271234') }}" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;font-family:monospace">
          </div>
        </div>
      </div>

      <!-- Payment Instructions Card -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:24px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
        <h3 style="margin:0 0 10px;font-size:16px;color:var(--navy)">📝 {{ __('Student Checkout Instructions') }}</h3>
        
        <div style="margin-bottom:14px">
          <label style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:4px">{{ __('Bangla Instructions (বাংলা)') }}</label>
          <textarea name="payment_instructions" rows="4" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;line-height:1.5">{{ old('payment_instructions', $settings['payment_instructions'] ?? '') }}</textarea>
        </div>

        <div>
          <label style="display:block;font-size:12px;font-weight:700;color:var(--navy);margin-bottom:4px">{{ __('English Instructions') }}</label>
          <textarea name="payment_instructions_en" rows="3" style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;line-height:1.5">{{ old('payment_instructions_en', $settings['payment_instructions_en'] ?? '') }}</textarea>
        </div>
      </div>

      <div style="display:flex;justify-content:flex-end;gap:12px">
        <button type="submit" class="btn btn-primary" style="padding:14px 28px;font-size:14px">
          {{ __('Save Payment Settings') }}
        </button>
      </div>

    </form>

  </div>
</section>
@endsection
