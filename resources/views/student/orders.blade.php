@extends('layouts.app')
@section('title', 'My Orders | Jerin’s Coaching')

@section('content')
<section class="section" style="background:#f8fafc;padding:36px 0 60px">
  <div class="container">
    
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px">
      <div>
        <p class="eyebrow">STUDENT DASHBOARD</p>
        <h1 style="margin:4px 0 8px;font-size:30px">My Course Orders</h1>
        <p class="section-subtitle">Track your payment submissions, verification status, and active course enrollments.</p>
      </div>
      <div>
        <a class="btn" href="{{ route('student.courses') }}">My Enrolled Courses →</a>
      </div>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:22px;font-weight:600">
        ✓ {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div role="alert" style="padding:14px 18px;background:#fff0f2;border:1px solid #fecdd3;color:#a61b38;border-radius:10px;margin-bottom:22px">
        @foreach($errors->all() as $error)
          <p style="margin:0">{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <!-- Orders Table Card -->
    <div style="overflow-x:auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
      <table style="width:100%;border-collapse:collapse;min-width:850px;font-size:13px">
        <thead>
          <tr style="background:#f1f5f9;text-align:left;border-bottom:1px solid #e2e8f0">
            <th style="padding:14px 18px;color:#334155;font-weight:700">Order Number</th>
            <th style="padding:14px 18px;color:#334155;font-weight:700">Course</th>
            <th style="padding:14px 18px;color:#334155;font-weight:700">Amount</th>
            <th style="padding:14px 18px;color:#334155;font-weight:700">Payment Status</th>
            <th style="padding:14px 18px;color:#334155;font-weight:700">Order Date</th>
            <th style="padding:14px 18px;color:#334155;font-weight:700;text-align:right">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              
              <!-- Order Number & Reference ID -->
              <td style="padding:16px 18px">
                <div style="font-family:monospace;font-weight:700;color:var(--navy)">
                  {{ $order->order_number }}
                </div>
                @if($order->transaction_id)
                  <div style="font-size:11px;color:#0369a1;margin-top:3px">
                    Ref: <code>{{ $order->transaction_id }}</code>
                  </div>
                @endif
              </td>

              <!-- Course -->
              <td style="padding:16px 18px">
                <strong style="color:#0f172a;display:block">{{ $order->course->title }}</strong>
                <span style="font-size:11px;color:#64748b">
                  {{ $order->payment_method === 'manual_upi' ? 'UPI Transfer' : ($order->payment_method === 'manual_bank' ? 'Bank Transfer' : 'Direct Checkout') }}
                </span>
              </td>

              <!-- Amount -->
              <td style="padding:16px 18px;font-weight:800;font-size:14px;color:#0f172a">
                {{ bdt($order->amount_paise, true) }}
              </td>

              <!-- Payment Status Badge -->
              <td style="padding:16px 18px">
                @if($order->isPaid())
                  <span style="display:inline-block;padding:4px 10px;border-radius:6px;background:#d1fae5;color:#065f46;font-size:11px;font-weight:800">
                    ✓ Verified / Enrolled
                  </span>
                @elseif($order->isAwaitingVerification())
                  <span style="display:inline-block;padding:4px 10px;border-radius:6px;background:#fef3c7;color:#92400e;font-size:11px;font-weight:800">
                    ⏳ Awaiting Verification
                  </span>
                  @if($order->proof_submitted_at)
                    <div style="font-size:10px;color:#a16207;margin-top:3px">
                      Submitted {{ $order->proof_submitted_at->diffForHumans() }}
                    </div>
                  @endif
                @elseif($order->isRejected())
                  <span style="display:inline-block;padding:4px 10px;border-radius:6px;background:#fee2e2;color:#991b1b;font-size:11px;font-weight:800">
                    ⚠️ Rejected — Action Required
                  </span>
                  @if($order->rejection_reason)
                    <div style="font-size:11px;color:#b91c1c;margin-top:4px;max-width:220px;font-weight:500">
                      "{{ Str::limit($order->rejection_reason, 60) }}"
                    </div>
                  @endif
                @else
                  <span style="display:inline-block;padding:4px 10px;border-radius:6px;background:#f1f5f9;color:#475569;font-size:11px;font-weight:800">
                    Payment Not Submitted
                  </span>
                @endif
              </td>

              <!-- Order Date -->
              <td style="padding:16px 18px;color:#64748b;font-size:12px">
                {{ $order->created_at->format('d M Y') }}
              </td>

              <!-- Action -->
              <td style="padding:16px 18px;text-align:right">
                @if($order->isPaid())
                  <a href="{{ route('student.courses') }}" class="btn" style="padding:8px 14px;font-size:12px;background:#10b981">
                    Start Learning →
                  </a>
                @elseif($order->isRejected())
                  <a href="{{ route('checkout.show', $order) }}" class="btn" style="padding:8px 14px;font-size:12px;background:#dc2626">
                    Resubmit Proof →
                  </a>
                @elseif($order->isAwaitingVerification())
                  <a href="{{ route('checkout.show', $order) }}" class="btn btn-outline" style="padding:8px 14px;font-size:12px">
                    Review Status / Receipt →
                  </a>
                @else
                  <a href="{{ route('checkout.show', $order) }}" class="btn" style="padding:8px 14px;font-size:12px">
                    Complete Payment →
                  </a>
                @endif
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="6" style="padding:40px;text-align:center;color:#64748b">
                <div style="font-size:36px;margin-bottom:8px">🛍️</div>
                <h4 style="margin:0 0 6px;color:#1e293b">You have no course orders yet.</h4>
                <p style="margin:0 0 16px;font-size:13px">Browse our biology courses and enroll to begin learning.</p>
                <a href="{{ route('courses.index') }}" class="btn" style="padding:10px 18px">Explore Courses</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div style="margin-top:20px">
      {{ $orders->links() }}
    </div>

  </div>
</section>
@endsection
