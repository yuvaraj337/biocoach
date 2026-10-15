@extends('layouts.app')
@section('title', 'Review Payment | ' . $order->order_number)

@section('content')
<section class="section" style="background:#f8fafc;padding:32px 0 60px">
  <div class="container" style="max-width:1050px">
    
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:20px">
      <div>
        <a href="{{ route('admin.verifications.index') }}" style="color:var(--blue);font-weight:700;font-size:13px">← Back to Payment Verifications</a>
        <p class="eyebrow" style="margin-top:10px">ORDER VERIFICATION REVIEW</p>
        <h1 style="margin:4px 0 6px;font-size:28px">{{ $order->order_number }}</h1>
      </div>
      <div>
        @if($order->isPaid())
          <span style="display:inline-block;padding:6px 14px;border-radius:8px;background:#d1fae5;color:#065f46;font-size:13px;font-weight:800">
            ✓ Verified / Enrolled
          </span>
        @elseif($order->isAwaitingVerification())
          <span style="display:inline-block;padding:6px 14px;border-radius:8px;background:#fef3c7;color:#92400e;font-size:13px;font-weight:800">
            ⏳ Awaiting Verification
          </span>
        @elseif($order->isRejected())
          <span style="display:inline-block;padding:6px 14px;border-radius:8px;background:#fee2e2;color:#991b1b;font-size:13px;font-weight:800">
            ✕ Payment Rejected
          </span>
        @else
          <span style="display:inline-block;padding:6px 14px;border-radius:8px;background:#f1f5f9;color:#64748b;font-size:13px;font-weight:800">
            Payment Not Submitted
          </span>
        @endif
      </div>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:22px;font-weight:600">
        ✓ {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div role="alert" style="padding:14px 18px;background:#fff0f2;border:1px solid #fecdd3;color:#a61b38;border-radius:10px;margin-bottom:22px">
        <strong style="display:block;margin-bottom:6px">Please resolve the following:</strong>
        <ul style="margin:0;padding-left:20px;font-size:13px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:24px" class="review-grid">
      
      <!-- Left Column: Verification Details & Proof Preview -->
      <div>
        
        <!-- Payment Proof Card -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
            <h3 style="margin:0;font-size:16px;color:var(--navy)">Submitted Payment Proof</h3>
            @if($order->payment_proof_path)
              <a href="{{ route('orders.proof', $order) }}" target="_blank" class="mini-btn" style="background:#0868bd;color:#fff;border-color:#0868bd">
                Open in Full Window ↗
              </a>
            @endif
          </div>

          @if($order->payment_proof_path)
            @php($isPdf = Str::endsWith(strtolower($order->payment_proof_original_name ?: $order->payment_proof_path), '.pdf'))
            @if($isPdf)
              <div style="padding:30px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:10px;text-align:center">
                <div style="font-size:44px;margin-bottom:10px">📄</div>
                <h4 style="margin:0 0 6px;color:#1e293b">{{ $order->payment_proof_original_name ?: 'Payment Receipt (PDF)' }}</h4>
                <p style="margin:0 0 16px;color:#64748b;font-size:12px">PDF document uploaded by student</p>
                <a href="{{ route('orders.proof', $order) }}" target="_blank" class="btn" style="padding:10px 18px;font-size:12px">
                  View PDF Receipt
                </a>
              </div>
            @else
              <div style="text-align:center;background:#0f172a;border-radius:10px;padding:12px;overflow:hidden">
                <a href="{{ route('orders.proof', $order) }}" target="_blank">
                  <img 
                    src="{{ route('orders.proof', $order) }}" 
                    alt="Payment receipt proof" 
                    style="max-width:100%;max-height:450px;object-fit:contain;border-radius:6px;display:block;margin:0 auto"
                  >
                </a>
              </div>
              <small style="display:block;text-align:center;color:#64748b;margin-top:8px;font-size:11px">
                Filename: {{ $order->payment_proof_original_name ?: 'receipt' }} — Click image to view full resolution.
              </small>
            @endif
          @else
            <div style="padding:36px;text-align:center;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:10px;color:#64748b">
              <div style="font-size:36px;margin-bottom:8px">📭</div>
              <p style="margin:0;font-size:13px">No payment proof uploaded yet for this order.</p>
            </div>
          @endif
        </div>

        <!-- Student Submission Notes & Audit Trail -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
          <h3 style="margin:0 0 14px;font-size:16px;color:var(--navy)">Transaction Reference & Submission Details</h3>
          
          <table style="width:100%;font-size:13px;border-collapse:collapse">
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:10px 0;color:#64748b;width:40%">Payment Method:</td>
              <td style="padding:10px 0;font-weight:700;color:#0f172a">
                @if(in_array($order->payment_method, ['bkash', 'manual_bkash']))
                  📱 bKash (Mobile Banking)
                @elseif(in_array($order->payment_method, ['nagad', 'manual_nagad']))
                  📱 Nagad (Mobile Banking)
                @elseif(in_array($order->payment_method, ['rocket', 'manual_rocket']))
                  📱 Rocket (Mobile Banking)
                @elseif($order->payment_method === 'sslcommerz')
                  💳 SSLCommerz Gateway
                @elseif(in_array($order->payment_method, ['manual_bank', 'bank']))
                  🏦 Bank Transfer (Dutch-Bangla Bank / DBBL)
                @else
                  ⚡ {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'Not selected')) }}
                @endif
              </td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:10px 0;color:#64748b">Transaction / Reference ID:</td>
              <td style="padding:10px 0;font-weight:800;font-family:monospace;font-size:14px;color:#0284c7">
                {{ $order->transaction_id ?: '— Not provided —' }}
              </td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:10px 0;color:#64748b">Submitted At:</td>
              <td style="padding:10px 0;color:#334155">
                {{ $order->proof_submitted_at?->format('d M Y, h:i:s A') ?? 'Not submitted yet' }}
              </td>
            </tr>
            @if($order->verification_notes)
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:10px 0;color:#64748b">Student Note:</td>
                <td style="padding:10px 0;color:#334155;font-style:italic">
                  "{{ $order->verification_notes }}"
                </td>
              </tr>
            @endif
            @if($order->verified_by)
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:10px 0;color:#64748b">Verified By:</td>
                <td style="padding:10px 0;font-weight:700;color:#065f46">
                  {{ $order->verifier?->name ?? 'Admin #' . $order->verified_by }} ({{ $order->verified_at?->format('d M Y, h:i A') }})
                </td>
              </tr>
            @endif
            @if($order->rejection_reason)
              <tr>
                <td style="padding:10px 0;color:#dc2626">Rejection Reason:</td>
                <td style="padding:10px 0;font-weight:600;color:#dc2626">
                  {{ $order->rejection_reason }}
                </td>
              </tr>
            @endif
          </table>
        </div>

      </div>

      <!-- Right Column: Student Info, Course Info & Verification Action Panel -->
      <div>
        
        <!-- Student Info Card -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
          <h3 style="margin:0 0 14px;font-size:16px;color:var(--navy)">Student Details</h3>
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            <div style="width:44px;height:44px;border-radius:50%;background:#e0f2fe;color:#0369a1;display:grid;place-items:center;font-weight:800;font-size:18px">
              {{ strtoupper(substr($order->user->name, 0, 1)) }}
            </div>
            <div>
              <div style="font-weight:700;font-size:15px;color:#0f172a">{{ $order->user->name }}</div>
              <div style="font-size:12px;color:#64748b">{{ $order->user->email }}</div>
            </div>
          </div>
          <div style="font-size:12px;color:#64748b;border-top:1px solid #f1f5f9;padding-top:10px">
            <div><strong>Student ID:</strong> #{{ $order->user->id }}</div>
            <div><strong>Registered:</strong> {{ $order->user->created_at->format('d M Y') }}</div>
          </div>
        </div>

        <!-- Course & Price Card -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
          <h3 style="margin:0 0 14px;font-size:16px;color:var(--navy)">Course & Price Breakdown</h3>
          <div style="margin-bottom:12px">
            <div style="font-weight:700;font-size:16px;color:#0f172a">{{ $order->course->title }}</div>
            <div style="font-size:12px;color:#64748b;margin-top:2px">{{ $order->course->summary }}</div>
          </div>
          <div style="background:#f8fafc;padding:12px;border-radius:8px;display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:13px;color:#475569;font-weight:600">Expected Total</span>
            <span style="font-size:20px;font-weight:800;color:var(--blue)">{{ bdt($order->amount_paise, true) }}</span>
          </div>
        </div>

        <!-- Actions Panel -->
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:22px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
          <h3 style="margin:0 0 8px;font-size:16px;color:var(--navy)">Verification Decision</h3>
          <p style="font-size:12px;color:#64748b;margin:0 0 18px">
            Carefully cross-check the transaction reference and bank credit statement before approving.
          </p>

          @if($order->isPaid())
            <div style="background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;padding:16px;text-align:center;margin-bottom:14px">
              <span style="font-size:24px">✅</span>
              <h4 style="margin:6px 0 2px;color:#065f46">Payment Verified</h4>
              <p style="margin:0;font-size:12px;color:#047857">Course enrollment is active. The student has full access to lessons and quizzes.</p>
            </div>
          @else
            <!-- Verify Payment Form -->
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px;margin-bottom:20px">
              <h4 style="margin:0 0 6px;color:#166534;font-size:14px">Approve & Unlock Course</h4>
              <p style="margin:0 0 12px;font-size:12px;color:#15803d">
                Validates order amount ({{ bdt($order->amount_paise, true) }}), activates course enrollment, and logs your administrator audit record.
              </p>
              <form method="POST" action="{{ route('admin.verifications.verify', $order) }}" onsubmit="return confirm('Are you sure you have verified this payment in your bank/bKash/Nagad account? This will grant course access to {{ $order->user->name }}.');">
                @csrf
                <div style="margin-bottom:10px">
                  <label for="admin_note" style="display:block;font-size:11px;font-weight:700;color:#166534;margin-bottom:4px">Admin Verification Note (Optional)</label>
                  <input type="text" id="admin_note" name="admin_note" placeholder="e.g. Verified in bKash/Nagad/Bank records matching TrxID {{ $order->transaction_id }}" style="width:100%;padding:8px 10px;font-size:12px;border:1px solid #86efac;border-radius:6px">
                </div>
                <button type="submit" class="btn" style="width:100%;background:#16a34a;padding:12px;font-size:13px;font-weight:800">
                  ✓ Verify Payment & Activate Enrolment
                </button>
              </form>
            </div>

            <!-- Reject Payment Form -->
            <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:16px">
              <h4 style="margin:0 0 6px;color:#9f1239;font-size:14px">Reject Payment</h4>
              <p style="margin:0 0 12px;font-size:12px;color:#be123c">
                Requires a rejection reason. Course access will NOT be granted. The student will be prompted to resubmit correct payment details.
              </p>
              <form method="POST" action="{{ route('admin.verifications.reject', $order) }}" onsubmit="return confirm('Reject this payment submission? The student will be requested to provide valid proof.');">
                @csrf
                <div style="margin-bottom:10px">
                  <label for="rejection_reason" style="display:block;font-size:11px;font-weight:700;color:#9f1239;margin-bottom:4px">Rejection Reason (Required) <span style="color:#e11d48">*</span></label>
                  <textarea id="rejection_reason" name="rejection_reason" required minlength="5" maxlength="500" rows="3" placeholder="e.g. Transaction ID (TrxID) was not found in our bKash/Nagad statement. Please provide the legitimate TrxID or receipt." style="width:100%;padding:8px 10px;font-size:12px;border:1px solid #fda4af;border-radius:6px"></textarea>
                </div>
                <button type="submit" class="btn" style="width:100%;background:#e11d48;padding:10px;font-size:13px;font-weight:800">
                  ✕ Reject Payment Submission
                </button>
              </form>
            </div>
          @endif

        </div>

      </div>

    </div>

  </div>
</section>

<style>
@media(max-width:850px){
  .review-grid{
    grid-template-columns:1fr !important;
  }
}
</style>
@endsection
