@extends('layouts.app')
@section('title', 'Payment Verifications | Jerin’s Coaching Admin')

@section('content')
<section class="section" style="background:#f8fafc;padding:32px 0 60px">
  <div class="container">
    
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px">
      <div>
        <p class="eyebrow">TEACHER & ADMIN VERIFICATION</p>
        <h1 style="margin:4px 0 8px;font-size:30px">Payment Verifications</h1>
        <p class="section-subtitle">Review student payment receipts and manually unlock course enrolments after confirming bank/UPI credits.</p>
      </div>
      <div style="display:flex;gap:10px;align-items:center">
        <a class="btn btn-outline" href="{{ route('admin.payment-settings.edit') }}">⚙️ Business Payment Settings</a>
        <a class="btn" href="{{ route('admin.dashboard') }}">← Admin Dashboard</a>
      </div>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:20px;font-weight:600">
        ✓ {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div role="alert" style="padding:14px 18px;background:#fff0f2;border:1px solid #fecdd3;color:#a61b38;border-radius:10px;margin-bottom:20px">
        @foreach($errors->all() as $error)
          <p style="margin:0">{{ $error }}</p>
        @endforeach
      </div>
    @endif

    <!-- Filter Stats Tabs -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:14px;margin-bottom:24px">
      <a href="{{ route('admin.verifications.index', ['status' => 'awaiting']) }}" style="text-decoration:none">
        <div style="background:#fff;border:2px solid {{ $status === 'awaiting' ? '#f59e0b' : '#e2e8f0' }};border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.03);position:relative">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:12px;font-weight:700;color:#92400e;text-transform:uppercase">Awaiting Review</span>
            <span style="background:#fef3c7;color:#92400e;padding:3px 8px;border-radius:12px;font-size:12px;font-weight:800">{{ $counts['awaiting'] }}</span>
          </div>
          <div style="font-size:24px;font-weight:800;color:#78350f;margin-top:6px">{{ $counts['awaiting'] }}</div>
          <small style="color:#a16207;font-size:11px">Proof uploaded, needs verification</small>
        </div>
      </a>

      <a href="{{ route('admin.verifications.index', ['status' => 'verified']) }}" style="text-decoration:none">
        <div style="background:#fff;border:2px solid {{ $status === 'verified' ? '#10b981' : '#e2e8f0' }};border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:12px;font-weight:700;color:#065f46;text-transform:uppercase">Verified & Enrolled</span>
            <span style="background:#d1fae5;color:#065f46;padding:3px 8px;border-radius:12px;font-size:12px;font-weight:800">{{ $counts['verified'] }}</span>
          </div>
          <div style="font-size:24px;font-weight:800;color:#047857;margin-top:6px">{{ $counts['verified'] }}</div>
          <small style="color:#059669;font-size:11px">Access successfully granted</small>
        </div>
      </a>

      <a href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}" style="text-decoration:none">
        <div style="background:#fff;border:2px solid {{ $status === 'rejected' ? '#ef4444' : '#e2e8f0' }};border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:12px;font-weight:700;color:#991b1b;text-transform:uppercase">Rejected Payments</span>
            <span style="background:#fee2e2;color:#991b1b;padding:3px 8px;border-radius:12px;font-size:12px;font-weight:800">{{ $counts['rejected'] }}</span>
          </div>
          <div style="font-size:24px;font-weight:800;color:#b91c1c;margin-top:6px">{{ $counts['rejected'] }}</div>
          <small style="color:#dc2626;font-size:11px">Requires student action</small>
        </div>
      </a>

      <a href="{{ route('admin.verifications.index', ['status' => 'all']) }}" style="text-decoration:none">
        <div style="background:#fff;border:2px solid {{ $status === 'all' ? '#0868bd' : '#e2e8f0' }};border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,0.03)">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <span style="font-size:12px;font-weight:700;color:var(--navy);text-transform:uppercase">All Orders</span>
            <span style="background:#e0f2fe;color:#0369a1;padding:3px 8px;border-radius:12px;font-size:12px;font-weight:800">{{ $counts['all'] }}</span>
          </div>
          <div style="font-size:24px;font-weight:800;color:var(--navy);margin-top:6px">{{ $counts['all'] }}</div>
          <small style="color:#64748b;font-size:11px">Full order records</small>
        </div>
      </a>
    </div>

    <!-- Search Form -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:20px">
      <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
        <input type="hidden" name="status" value="{{ $status }}">
        <input 
          type="text" 
          name="q" 
          value="{{ request('q') }}" 
          placeholder="Search by order #, transaction ID, student name, email, or course..." 
          style="flex:1;min-width:280px;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px"
        >
        <button type="submit" class="btn" style="padding:10px 18px">Search</button>
        @if(request('q'))
          <a href="{{ route('admin.verifications.index', ['status' => $status]) }}" class="btn btn-outline" style="padding:10px 14px">Clear</a>
        @endif
      </form>
    </div>

    <!-- Verifications Table -->
    <div style="overflow-x:auto;background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
      <table style="width:100%;border-collapse:collapse;min-width:960px;font-size:13px">
        <thead>
          <tr style="background:#f1f5f9;text-align:left;border-bottom:1px solid #e2e8f0">
            <th style="padding:14px 16px;font-weight:700;color:#334155">Order ID</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155">Student</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155">Course</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155">Amount Due</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155">Method & Reference ID</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155">Submitted Proof</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155">Submission Date</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155">Verification Status</th>
            <th style="padding:14px 16px;font-weight:700;color:#334155;text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.15s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
              
              <!-- Order ID -->
              <td style="padding:14px 16px;font-family:monospace;font-weight:700;color:var(--navy)">
                <a href="{{ route('admin.verifications.show', $order) }}" style="color:var(--blue);text-decoration:underline">
                  {{ $order->order_number }}
                </a>
              </td>

              <!-- Student -->
              <td style="padding:14px 16px">
                <div style="font-weight:700;color:#0f172a">{{ $order->user->name }}</div>
                <div style="font-size:11px;color:#64748b">{{ $order->user->email }}</div>
              </td>

              <!-- Course -->
              <td style="padding:14px 16px;max-width:180px">
                <div style="font-weight:600;color:#0f172a">{{ $order->course->title }}</div>
              </td>

              <!-- Amount Due -->
              <td style="padding:14px 16px;font-weight:800;color:#0f172a">
                {{ bdt($order->amount_paise, true) }}
              </td>

              <!-- Payment Method & Reference ID -->
              <td style="padding:14px 16px">
                <div style="font-size:12px;font-weight:600;color:#334155">
                  @if(in_array($order->payment_method, ['bkash', 'manual_bkash']))
                    📱 bKash
                  @elseif(in_array($order->payment_method, ['nagad', 'manual_nagad']))
                    📱 Nagad
                  @elseif(in_array($order->payment_method, ['rocket', 'manual_rocket']))
                    📱 Rocket
                  @elseif($order->payment_method === 'sslcommerz')
                    💳 SSLCommerz
                  @elseif(in_array($order->payment_method, ['manual_bank', 'bank']))
                    🏦 Bank Transfer
                  @else
                    ⚡ {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'Manual')) }}
                  @endif
                </div>
                @if($order->transaction_id)
                  <div style="font-family:monospace;font-size:11px;color:#0369a1;background:#f0f9ff;padding:2px 6px;border-radius:4px;display:inline-block;margin-top:3px">
                    {{ $order->transaction_id }}
                  </div>
                @else
                  <span style="color:#94a3b8;font-size:11px">Not entered</span>
                @endif
              </td>

              <!-- Submitted Payment Proof -->
              <td style="padding:14px 16px">
                @if($order->payment_proof_path)
                  <a href="{{ route('orders.proof', $order) }}" target="_blank" class="mini-btn" style="background:#e0f2fe;color:#0369a1;border-color:#bae6fd;display:inline-flex;align-items:center;gap:4px">
                    📄 View Proof
                  </a>
                @else
                  <span style="color:#94a3b8;font-size:11px">No proof</span>
                @endif
              </td>

              <!-- Submission Date -->
              <td style="padding:14px 16px;color:#64748b;font-size:12px">
                @if($order->proof_submitted_at)
                  {{ $order->proof_submitted_at->format('d M Y') }}<br>
                  <small>{{ $order->proof_submitted_at->format('h:i A') }}</small>
                @else
                  {{ $order->created_at->format('d M Y') }}
                @endif
              </td>

              <!-- Verification Status -->
              <td style="padding:14px 16px">
                @if($order->isPaid())
                  <span style="display:inline-block;padding:3px 8px;border-radius:6px;background:#d1fae5;color:#065f46;font-size:11px;font-weight:700">
                    ✓ Verified / Enrolled
                  </span>
                @elseif($order->isAwaitingVerification())
                  <span style="display:inline-block;padding:3px 8px;border-radius:6px;background:#fef3c7;color:#92400e;font-size:11px;font-weight:700">
                    ⏳ Awaiting Verification
                  </span>
                @elseif($order->isRejected())
                  <span style="display:inline-block;padding:3px 8px;border-radius:6px;background:#fee2e2;color:#991b1b;font-size:11px;font-weight:700">
                    ✕ Rejected
                  </span>
                @else
                  <span style="display:inline-block;padding:3px 8px;border-radius:6px;background:#f1f5f9;color:#64748b;font-size:11px;font-weight:700">
                    Payment Not Submitted
                  </span>
                @endif
              </td>

              <!-- Actions -->
              <td style="padding:14px 16px;text-align:right">
                <a href="{{ route('admin.verifications.show', $order) }}" class="btn" style="padding:6px 12px;font-size:12px;background:var(--blue)">
                  Review & Verify →
                </a>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="9" style="padding:36px;text-align:center;color:#64748b">
                <div style="font-size:32px;margin-bottom:8px">📭</div>
                <p style="margin:0;font-size:14px">No orders matching this filter.</p>
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
