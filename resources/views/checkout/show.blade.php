@extends('layouts.app')
@section('title', __('Student Checkout') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section" style="background:#f8fafc;padding:36px 0 60px">
  <div class="container" style="max-width:940px">
    
    <div style="margin-bottom:20px">
      <a href="{{ route('student.orders') }}" style="color:var(--blue);font-weight:700;font-size:13px">← {{ __('My Orders') }}</a>
      <p class="eyebrow" style="margin-top:14px">{{ __('Student Checkout') }}</p>
      <h1 style="font-size:32px;margin:6px 0 10px">{{ $order->course->title }}</h1>
      <p class="section-subtitle">{{ __('Complete your payment using the verified business details below. Your course will unlock once Dr. Jerin verifies your submission.') }}</p>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:22px;display:flex;align-items:center;gap:10px;font-weight:600">
        <span style="font-size:20px">✓</span>
        <div>{{ session('success') }}</div>
      </div>
    @endif

    @if(session('error'))
      <div role="alert" style="padding:14px 18px;background:#fff0f2;border:1px solid #fecdd3;color:#a61b38;border-radius:10px;margin-bottom:22px">
        {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div role="alert" style="padding:14px 18px;background:#fff0f2;border:1px solid #fecdd3;color:#a61b38;border-radius:10px;margin-bottom:22px">
        <strong style="display:block;margin-bottom:6px">Please fix the following issues:</strong>
        <ul style="margin:0;padding-left:20px;font-size:13px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Status Alert Banner -->
    @if($order->isPaid())
      <div style="padding:20px 24px;background:#ecfdf5;border:2px solid #10b981;border-radius:14px;margin-bottom:26px">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px">
          <div>
            <span style="display:inline-block;padding:4px 10px;background:#10b981;color:#fff;border-radius:6px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase">{{ __('Verified / Enrolled') }}</span>
            <h3 style="margin:8px 0 4px;color:#065f46;font-size:18px">Payment Verified & Course Access Active!</h3>
            <p style="margin:0;color:#047857;font-size:13px">Your payment has been verified. You can now access all video lessons, quizzes, and resources.</p>
          </div>
          @php($firstLesson = $order->course->lessons()->where('is_published', true)->orderBy('sort_order')->first())
          @if($firstLesson)
            <a class="btn" style="background:#059669;padding:12px 22px" href="{{ route('student.lessons.show', [$order->course, $firstLesson]) }}">{{ __('Go to Course →') }}</a>
          @else
            <a class="btn" style="background:#059669;padding:12px 22px" href="{{ route('student.courses') }}">{{ __('My Courses') }} →</a>
          @endif
        </div>
      </div>
    @elseif($order->isAwaitingVerification())
      <div style="padding:20px 24px;background:#fffbeb;border:2px solid #f59e0b;border-radius:14px;margin-bottom:26px">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div style="font-size:28px;line-height:1">⏳</div>
          <div style="flex:1">
            <span style="display:inline-block;padding:4px 10px;background:#f59e0b;color:#fff;border-radius:6px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase">{{ __('Awaiting Verification') }}</span>
            <h3 style="margin:8px 0 4px;color:#92400e;font-size:18px">Payment Submitted & Under Review</h3>
            <p style="margin:0 0 10px;color:#78350f;font-size:13px">
              Your transaction details and receipt were submitted on <strong>{{ bangla_date($order->proof_submitted_at, 'd M Y, h:i A') }}</strong>. 
              Our teaching staff verifies payments manually. Once verified, this course will unlock automatically.
            </p>
            <div style="display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:#78350f;background:rgba(255,255,255,0.7);padding:10px 14px;border-radius:8px">
              <div><strong>{{ __('Transaction ID (TrxID)') }}:</strong> <code>{{ $order->transaction_id }}</code></div>
              <div><strong>{{ __('Payment Method') }}:</strong> {{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</div>
              @if($order->payment_proof_path)
                <div>
                  <a href="{{ route('orders.proof', $order) }}" target="_blank" style="color:#b45309;font-weight:700;text-decoration:underline">
                    📄 View Uploaded Receipt ({{ $order->payment_proof_original_name ?: 'proof' }})
                  </a>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    @elseif($order->isRejected())
      <div style="padding:20px 24px;background:#fef2f2;border:2px solid #ef4444;border-radius:14px;margin-bottom:26px">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div style="font-size:28px;line-height:1">⚠️</div>
          <div style="flex:1">
            <span style="display:inline-block;padding:4px 10px;background:#ef4444;color:#fff;border-radius:6px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase">{{ __('Rejected — Action Required') }}</span>
            <h3 style="margin:8px 0 4px;color:#991b1b;font-size:18px">Payment Proof Could Not Be Verified</h3>
            <p style="margin:0 0 10px;color:#7f1d1d;font-size:13px">
              Dr. Jerin reviewed your submission and could not confirm the payment.
            </p>
            <div style="padding:12px 14px;background:#fff;border-left:4px solid #dc2626;border-radius:6px;margin-bottom:12px">
              <strong style="color:#991b1b;display:block;font-size:12px;text-transform:uppercase">Reason:</strong>
              <p style="margin:4px 0 0;color:#374151;font-size:14px;font-weight:500">{{ $order->rejection_reason }}</p>
            </div>
            <p style="margin:0;color:#991b1b;font-size:13px;font-weight:600">Please review the reason above and resubmit your correct transaction ID and proof screenshot below.</p>
          </div>
        </div>
      </div>
    @else
      <div style="padding:16px 20px;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:12px;margin-bottom:26px;display:flex;align-items:center;gap:12px">
        <div style="font-size:20px">ℹ️</div>
        <div>
          <span style="display:inline-block;padding:3px 8px;background:#64748b;color:#fff;border-radius:5px;font-size:10px;font-weight:800;text-transform:uppercase">{{ __('Payment Not Submitted') }}</span>
          <p style="margin:3px 0 0;font-size:13px;color:#334155">Complete payment via bKash, Nagad, Rocket, or instant SSLCommerz, then submit verification details below.</p>
        </div>
      </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px" class="checkout-grid">
      
      <!-- Order & Payment Details Column -->
      <div>
        <!-- Order Summary Card -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
          <h3 style="margin:0 0 16px;font-size:16px;color:var(--navy);border-bottom:1px solid #f1f5f9;padding-bottom:12px">{{ __('Order Summary') }}</h3>
          <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:13px">
            <span style="color:#64748b">{{ __('Order Number') }}</span>
            <strong style="font-family:monospace;font-size:13px">{{ $order->order_number }}</strong>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:13px">
            <span style="color:#64748b">{{ __('Courses') }}</span>
            <strong style="max-width:220px;text-align:right">{{ $order->course->title }}</strong>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:10px;font-size:13px">
            <span style="color:#64748b">Date</span>
            <span>{{ bangla_date($order->created_at, 'd M Y') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;margin-bottom:12px;font-size:13px">
            <span style="color:#64748b">Status</span>
            <span style="font-weight:700;color:{{ $order->status_badge_color }}">{{ $order->status_label }}</span>
          </div>

          <!-- Voucher Application -->
          @if(!$order->isPaid())
            <form method="POST" action="{{ route('checkout.voucher', $order) }}" style="margin:14px 0 6px;display:flex;gap:8px">
              @csrf
              <input type="text" name="voucher_code" placeholder="Coupon Code (e.g. BIOLOGY10)" style="flex:1;padding:8px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:12px;text-transform:uppercase">
              <button type="submit" class="btn" style="padding:8px 14px;font-size:12px;background:#082c50">Apply</button>
            </form>
          @endif

          <div style="border-top:1px dashed #cbd5e1;padding-top:14px;margin-top:10px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:15px;font-weight:700;color:var(--navy)">{{ __('Total Amount') }}</span>
            <span style="font-size:24px;font-weight:800;color:var(--blue)">{{ bdt($order->amount_paise, true) }}</span>
          </div>
        </div>

        <!-- SSLCommerz Instant Payment Card -->
        @if(!$order->isPaid() && ($settings['sslcommerz_enabled'] ?? true))
          <div style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%);color:#fff;border-radius:14px;padding:22px;margin-bottom:20px;box-shadow:0 4px 14px rgba(15,23,42,0.15)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
              <h3 style="color:#fff;margin:0;font-size:16px">⚡ {{ __('SSLCommerz (Card / Mobile Banking)') }}</h3>
              <span style="background:#10b981;color:#fff;padding:3px 8px;border-radius:5px;font-size:10px;font-weight:800">AUTOMATED</span>
            </div>
            <p style="color:#cbd5e1;font-size:12px;line-height:1.5;margin:0 0 16px">
              Pay immediately using VISA, Mastercard, bKash, Nagad, Rocket, Upay or Internet Banking with instant enrollment activation.
            </p>
            <form method="POST" action="{{ route('payments.sslcommerz.initiate', $order) }}">
              @csrf
              <button type="submit" class="btn" style="width:100%;padding:12px;background:#0868bd;color:#fff;font-size:14px;font-weight:700;box-shadow:0 2px 8px rgba(8,104,189,0.3)">
                {{ __('Pay with SSLCommerz') }} — {{ bdt($order->amount_paise, true) }} →
              </button>
            </form>
          </div>
        @endif

        <!-- Verified Business Manual Payment Details -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
            <span style="background:#dbeafe;color:#1e40af;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:800">MANUAL TRANSFER</span>
            <h3 style="margin:0;font-size:16px;color:var(--navy)">bKash / Nagad / Bank Details</h3>
          </div>
          <p style="font-size:12px;color:#64748b;margin:0 0 16px">Transfer the course fee of <strong>{{ bdt($order->amount_paise, true) }}</strong> to any of the following:</p>

          <!-- bKash Box -->
          <div style="background:#fdf2f8;border:1px solid #fbcfe8;border-radius:10px;padding:12px;margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
              <span style="font-weight:800;font-size:13px;color:#be185d">🌸 bKash ({{ $settings['bkash_type'] ?? 'Merchant' }})</span>
              <span style="font-size:11px;color:#db2777;font-weight:700">Send Money / Payment</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <input type="text" readonly value="{{ $settings['bkash_number'] ?? '01712-345678' }}" id="bkashField" style="background:#fff;border:1px solid #f472b6;padding:8px 10px;border-radius:6px;font-family:monospace;font-weight:800;font-size:14px;width:100%;color:#9d174d">
              <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('bkashField').value); this.innerText='Copied!'; setTimeout(()=>this.innerText='Copy', 2000)" style="padding:8px 12px;background:#db2777;color:#fff;border:0;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer">Copy</button>
            </div>
          </div>

          <!-- Nagad Box -->
          <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:12px;margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
              <span style="font-weight:800;font-size:13px;color:#c2410c">🔥 Nagad ({{ $settings['nagad_type'] ?? 'Personal' }})</span>
              <span style="font-size:11px;color:#ea580c;font-weight:700">Send Money</span>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <input type="text" readonly value="{{ $settings['nagad_number'] ?? '01812-345678' }}" id="nagadField" style="background:#fff;border:1px solid #fb923c;padding:8px 10px;border-radius:6px;font-family:monospace;font-weight:800;font-size:14px;width:100%;color:#9a3412">
              <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('nagadField').value); this.innerText='Copied!'; setTimeout(()=>this.innerText='Copy', 2000)" style="padding:8px 12px;background:#ea580c;color:#fff;border:0;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer">Copy</button>
            </div>
          </div>

          <!-- Bank Account Section -->
          @if($settings['manual_bank_enabled'] ?? true)
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;margin-bottom:12px">
              <div style="font-weight:700;font-size:13px;color:#0f172a;margin-bottom:6px">🏦 Bank Transfer ({{ $settings['bank_name'] ?? 'Dutch-Bangla Bank PLC' }})</div>
              <table style="width:100%;font-size:12px;border-collapse:collapse;color:#334155">
                <tr><td style="padding:3px 0;color:#64748b">Account Holder:</td><td style="font-weight:600;text-align:right">{{ $settings['account_holder'] ?? 'Dr. Tanjina Jerin' }}</td></tr>
                <tr><td style="padding:3px 0;color:#64748b">Bank Name:</td><td style="font-weight:600;text-align:right">{{ $settings['bank_name'] ?? 'Dutch-Bangla Bank PLC' }}</td></tr>
                <tr><td style="padding:3px 0;color:#64748b">Account No:</td><td style="font-weight:700;font-family:monospace;text-align:right">{{ $settings['account_number'] ?? '115.101.0029345' }}</td></tr>
                <tr><td style="padding:3px 0;color:#64748b">Routing / IFSC No:</td><td style="font-weight:700;font-family:monospace;text-align:right">{{ $settings['ifsc_code'] ?? ($settings['routing_number'] ?? '090271234') }}</td></tr>
              </table>
              @if(!empty($settings['upi_id']))
                <div style="margin-top:6px;font-size:11px;color:#64748b">
                  UPI Reference: <code>{{ $settings['upi_id'] }}</code>
                </div>
              @endif
            </div>
          @endif

          <!-- Instructions -->
          @if(!empty($settings['payment_instructions']))
            <div style="background:#fffbeb;border:1px solid #fef3c7;border-radius:8px;padding:12px;font-size:12px;color:#92400e;line-height:1.5">
              <strong>{{ __('Payment Instructions') }}:</strong>
              <div style="margin-top:4px;white-space:pre-line">{{ is_bangla() ? $settings['payment_instructions'] : ($settings['payment_instructions_en'] ?? $settings['payment_instructions']) }}</div>
            </div>
          @endif

        </div>
      </div>

      <!-- Proof Submission Form Column -->
      <div>
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:26px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
          
          <h3 style="margin:0 0 8px;font-size:18px;color:var(--navy)">
            @if($order->isRejected())
              Resubmit Payment Proof
            @elseif($order->isAwaitingVerification())
              Update Submitted Payment Proof
            @else
              {{ __('Submit for Verification') }}
            @endif
          </h3>
          <p style="font-size:13px;color:#64748b;margin:0 0 20px;line-height:1.5">
            After sending payment via bKash, Nagad, Rocket, or Bank, provide your TrxID and upload the receipt screenshot.
          </p>

          @if($order->isPaid())
            <div style="text-align:center;padding:30px 10px">
              <div style="font-size:48px;margin-bottom:12px">🎉</div>
              <h4 style="margin:0 0 6px;color:#065f46;font-size:18px">Course Access Activated</h4>
              <p style="color:#64748b;font-size:13px;margin:0 0 20px">No further action is required. Your payment has been confirmed and verified.</p>
              <a href="{{ route('student.courses') }}" class="btn" style="width:100%">{{ __('My Courses') }}</a>
            </div>
          @else
            <form method="POST" action="{{ route('checkout.proof', $order) }}" enctype="multipart/form-data">
              @csrf

              <!-- Payment Method Selection -->
              <div style="margin-bottom:18px">
                <label style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:6px">{{ __('Payment Method') }} <span style="color:#dc2626">*</span></label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                  <label style="display:flex;align-items:center;gap:8px;border:1px solid #cbd5e1;padding:10px 12px;border-radius:8px;cursor:pointer;background:#f8fafc">
                    <input type="radio" name="payment_method" value="bkash" @checked(old('payment_method', $order->payment_method ?? 'bkash') === 'bkash') required>
                    <span style="font-size:13px;font-weight:700;color:#be185d">bKash (বিকাশ)</span>
                  </label>
                  <label style="display:flex;align-items:center;gap:8px;border:1px solid #cbd5e1;padding:10px 12px;border-radius:8px;cursor:pointer;background:#f8fafc">
                    <input type="radio" name="payment_method" value="nagad" @checked(old('payment_method', $order->payment_method) === 'nagad') required>
                    <span style="font-size:13px;font-weight:700;color:#c2410c">Nagad (নগদ)</span>
                  </label>
                  <label style="display:flex;align-items:center;gap:8px;border:1px solid #cbd5e1;padding:10px 12px;border-radius:8px;cursor:pointer;background:#f8fafc">
                    <input type="radio" name="payment_method" value="rocket" @checked(old('payment_method', $order->payment_method) === 'rocket') required>
                    <span style="font-size:13px;font-weight:700;color:#7c3aed">Rocket (রকেট)</span>
                  </label>
                  <label style="display:flex;align-items:center;gap:8px;border:1px solid #cbd5e1;padding:10px 12px;border-radius:8px;cursor:pointer;background:#f8fafc">
                    <input type="radio" name="payment_method" value="manual_bank" @checked(old('payment_method', $order->payment_method) === 'manual_bank') required>
                    <span style="font-size:13px;font-weight:700;color:#0369a1">{{ __('Bank Transfer') }}</span>
                  </label>
                </div>
              </div>

              <!-- Transaction ID / Reference ID -->
              <div style="margin-bottom:18px">
                <label for="transaction_id" style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:6px">
                  {{ __('Transaction ID (TrxID)') }} <span style="color:#dc2626">*</span>
                </label>
                <input 
                  type="text" 
                  id="transaction_id" 
                  name="transaction_id" 
                  value="{{ old('transaction_id', $order->transaction_id) }}" 
                  required 
                  minlength="4" 
                  maxlength="100" 
                  placeholder="e.g. 9K72BXYZ1A or DBBL102938"
                  style="width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;outline:none"
                >
                <small style="display:block;color:#64748b;margin-top:4px;font-size:11px">Enter the transaction ID received in SMS after payment.</small>
              </div>

              <!-- Sender Phone Number -->
              <div style="margin-bottom:18px">
                <label for="sender_phone" style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:6px">
                  {{ __('Sender Mobile Number') }} (e.g. 017XXXXXXXX)
                </label>
                <input 
                  type="text" 
                  id="sender_phone" 
                  name="sender_phone" 
                  value="{{ old('sender_phone') }}" 
                  placeholder="01XXXXXXXXX"
                  style="width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;outline:none"
                >
              </div>

              <!-- Payment Proof Upload -->
              <div style="margin-bottom:18px">
                <label for="payment_proof" style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:6px">
                  {{ __('Upload Payment Proof (Receipt / Screenshot)') }} <span style="color:#dc2626">*</span>
                </label>
                <input 
                  type="file" 
                  id="payment_proof" 
                  name="payment_proof" 
                  accept="image/jpeg,image/png,application/pdf" 
                  required 
                  style="width:100%;padding:10px;border:1px dashed #94a3b8;border-radius:8px;font-size:13px;background:#f8fafc"
                >
                <small style="display:block;color:#64748b;margin-top:4px;font-size:11px">Accepted formats: JPG, PNG, PDF (Max size: 5MB). Stored securely.</small>
                @if($order->payment_proof_path)
                  <div style="margin-top:8px;font-size:12px;color:#0284c7">
                    Current file: 
                    <a href="{{ route('orders.proof', $order) }}" target="_blank" style="text-decoration:underline;font-weight:600">
                      {{ $order->payment_proof_original_name ?: 'View current receipt' }}
                    </a>
                  </div>
                @endif
              </div>

              <!-- Student Note -->
              <div style="margin-bottom:22px">
                <label for="verification_notes" style="display:block;font-size:13px;font-weight:700;color:var(--navy);margin-bottom:6px">
                  Additional Notes (Optional)
                </label>
                <textarea 
                  id="verification_notes" 
                  name="verification_notes" 
                  rows="2" 
                  maxlength="500" 
                  placeholder="Any additional info for verification"
                  style="width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px"
                >{{ old('verification_notes', $order->verification_notes) }}</textarea>
              </div>

              <!-- Security Notice -->
              <div style="background:#f1f5f9;border-left:3px solid #0868bd;padding:10px 14px;border-radius:4px;margin-bottom:20px;font-size:12px;color:#334155;line-height:1.4">
                🔒 <strong>Notice:</strong> All submissions are verified against bank/bKash statement before granting course access.
              </div>

              <!-- Submit Button -->
              <button type="submit" class="btn" style="width:100%;padding:14px;font-size:14px;background:var(--blue)">
                @if($order->isRejected())
                  Resubmit Payment Proof for Review →
                @elseif($order->isAwaitingVerification())
                  Update Submission Details →
                @else
                  {{ __('Submit for Verification') }} →
                @endif
              </button>
            </form>
          @endif

        </div>
      </div>

    </div>

  </div>
</section>

<style>
@media(max-width:768px){
  .checkout-grid{
    grid-template-columns:1fr !important;
  }
}
</style>
@endsection
