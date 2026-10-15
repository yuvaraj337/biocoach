@extends('layouts.app')
@section('title', __('Admin Dashboard') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section" style="background:#f8fafc;padding:36px 0 60px">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap">
      <div>
        <p class="eyebrow">{{ __('ADMINISTRATION PORTAL') }}</p>
        <h1 style="margin:4px 0 8px;font-size:32px">{{ __('Admin Dashboard') }}</h1>
        <p class="section-subtitle">{{ __('Manage Dr. Jerin’s biology catalogue, verify student payments, and manage enrolments.') }}</p>
      </div>
      <div style="display:flex;gap:8px;align-items:center">
        <span style="font-size:12px;color:#64748b;font-weight:600">Period:</span>
        <a href="{{ route('admin.dashboard', ['period' => 7]) }}" class="mini-btn" style="{{ $days === 7 ? 'background:#0868bd;color:#fff' : '' }}">7D</a>
        <a href="{{ route('admin.dashboard', ['period' => 30]) }}" class="mini-btn" style="{{ $days === 30 ? 'background:#0868bd;color:#fff' : '' }}">30D</a>
        <a href="{{ route('admin.dashboard', ['period' => 90]) }}" class="mini-btn" style="{{ $days === 90 ? 'background:#0868bd;color:#fff' : '' }}">90D</a>
      </div>
    </div>

    <!-- Real Metrics Stat Cards Grid -->
    <div style="display:grid;grid-template-columns:repeat(4, minmax(0, 1fr));gap:16px;margin:24px 0 28px" class="stats-grid">
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
        <span style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">TOTAL REVENUE (BDT)</span>
        <h2 style="font-size:26px;color:#0868bd;margin:6px 0 2px;font-weight:800">{{ bdt($stats['total_revenue_bdt']) }}</h2>
        <span style="font-size:11px;color:#107e46">Last {{ $days }} days: {{ bdt($stats['period_revenue_bdt']) }}</span>
      </div>

      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
        <span style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">STUDENTS ENROLLED</span>
        <h2 style="font-size:26px;color:#082c50;margin:6px 0 2px;font-weight:800">{{ bn_number($stats['paid_enrollments']) }}</h2>
        <span style="font-size:11px;color:#64748b">{{ bn_number($stats['active_students']) }} active students</span>
      </div>

      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
        <span style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase">COURSES & LESSONS</span>
        <h2 style="font-size:26px;color:#082c50;margin:6px 0 2px;font-weight:800">{{ bn_number($stats['published_courses']) }} / {{ bn_number($stats['total_courses']) }}</h2>
        <span style="font-size:11px;color:#64748b">{{ bn_number($stats['total_lessons']) }} published lessons</span>
      </div>

      <div style="background:#fff;border:1px solid {{ $stats['pending_verifications'] > 0 ? '#f59e0b' : '#e2e8f0' }};border-radius:12px;padding:18px 20px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
        <span style="font-size:11px;color:{{ $stats['pending_verifications'] > 0 ? '#b45309' : '#64748b' }};font-weight:700;text-transform:uppercase">PENDING VERIFICATIONS</span>
        <h2 style="font-size:26px;color:{{ $stats['pending_verifications'] > 0 ? '#d97706' : '#082c50' }};margin:6px 0 2px;font-weight:800">{{ bn_number($stats['pending_verifications']) }}</h2>
        <a href="{{ route('admin.verifications.index') }}" style="font-size:11px;color:#0868bd;font-weight:700;text-decoration:underline">Review receipts now →</a>
      </div>
    </div>

    <!-- Highlight: Payment Verification Banner -->
    <div style="background:linear-gradient(135deg, #082c50 0%, #0868bd 100%);color:#fff;border-radius:16px;padding:26px 30px;margin-bottom:32px;display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;box-shadow:0 8px 24px rgba(8,104,189,0.18)">
      <div>
        <span style="background:rgba(255,255,255,0.2);color:#fff;padding:4px 10px;border-radius:6px;font-size:11px;font-weight:800;letter-spacing:.05em;text-transform:uppercase">
          {{ __('CORE PAYMENT WORKFLOW') }}
        </span>
        <h2 style="color:#fff;margin:8px 0 6px;font-size:24px">{{ __('Manual Payment Verification') }} (bKash / Nagad / Bank)</h2>
        <p style="color:#dbeafe;margin:0;font-size:14px;max-width:560px">
          Review uploaded student payment receipts, verify bank/mobile statement credits, and activate course enrollments immediately.
        </p>
      </div>
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <a class="btn" style="background:#f6c84a;color:#082c50;padding:12px 22px;font-weight:800" href="{{ route('admin.verifications.index') }}">
          {{ __('Verifications') }} ({{ bn_number($stats['pending_verifications']) }}) →
        </a>
        <a class="btn" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);padding:12px 18px" href="{{ route('admin.payment-settings.edit') }}">
          ⚙️ Payment Details
        </a>
      </div>
    </div>

    <!-- Management Modules Grid -->
    <h3 style="font-size:18px;color:#082c50;margin:0 0 16px">Management Modules</h3>
    <div class="resource-grid" style="grid-template-columns:repeat(4, minmax(0, 1fr));gap:16px;margin-bottom:32px">
      
      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#e0f2fe;color:#0369a1">🔍</div>
          <div>
            <h3 style="margin:0;font-size:15px">{{ __('Verifications') }}</h3>
            <span style="font-size:11px;color:#64748b">Student receipts</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Review submitted bKash, Nagad, and bank receipts with full audit trails.</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.verifications.index') }}">Review verifications →</a>
      </article>

      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#e4f8ed;color:#078451">📚</div>
          <div>
            <h3 style="margin:0;font-size:15px">{{ __('Courses') }}</h3>
            <span style="font-size:11px;color:#64748b">Curriculum & Lessons</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Create and manage courses, curriculum, videos, and preview settings.</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.courses.index') }}">Manage courses →</a>
      </article>

      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#fef3c7;color:#b45309">👥</div>
          <div>
            <h3 style="margin:0;font-size:15px">Users & Roles</h3>
            <span style="font-size:11px;color:#64748b">Students, Teachers & Admins</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Manage student accounts, role permissions (Super Admin, Teacher, Moderator).</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.users.index') }}">Manage users →</a>
      </article>

      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#f3e8ff;color:#7e22ce">🧾</div>
          <div>
            <h3 style="margin:0;font-size:15px">Orders & Payments</h3>
            <span style="font-size:11px;color:#64748b">Transactions</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Inspect orders, transaction references, revenue in BDT, and enrollment access.</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.orders.index') }}">Manage orders →</a>
      </article>

      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#e0f2fe;color:#0284c7">🏷️</div>
          <div>
            <h3 style="margin:0;font-size:15px">{{ __('Categories') }}</h3>
            <span style="font-size:11px;color:#64748b">SSC, HSC & Admissions</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Manage categories: SSC Biology, HSC Biology, Medical & Dental Admission.</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.categories.index') }}">Manage categories →</a>
      </article>

      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#ecfdf5;color:#059669">🔴</div>
          <div>
            <h3 style="margin:0;font-size:15px">{{ __('Live Classes') }}</h3>
            <span style="font-size:11px;color:#64748b">Asia/Dhaka Scheduling</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Schedule live interactive classes with Google Meet / Zoom meeting links.</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.live-classes.index') }}">Schedule classes →</a>
      </article>

      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#fffbeb;color:#d97706">🎟️</div>
          <div>
            <h3 style="margin:0;font-size:15px">Vouchers & Coupons</h3>
            <span style="font-size:11px;color:#64748b">Discounts & Promos</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Create promo codes, percentage or fixed discounts, and usage limits.</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.vouchers.index') }}">Manage vouchers →</a>
      </article>

      <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;background:#fff;border:1px solid #e2e8f0;border-radius:14px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
          <div class="resource-icon" style="background:#fee2e2;color:#b91c1c">🔒</div>
          <div>
            <h3 style="margin:0;font-size:15px">Audit Log</h3>
            <span style="font-size:11px;color:#64748b">Security & History</span>
          </div>
        </div>
        <p style="color:#64748b;font-size:12px;line-height:1.5;margin-bottom:14px">Review immutable administrative actions, verifications, and role updates.</p>
        <a class="mini-btn" style="text-align:center" href="{{ route('admin.audit.index') }}">View audit log →</a>
      </article>

    </div>

    <!-- Recent Tables (2 Columns) -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px" class="recent-tables-grid">
      <!-- Recent Orders Table -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <h3 style="font-size:16px;margin:0;color:#082c50">Recent Orders</h3>
          <a href="{{ route('admin.orders.index') }}" style="font-size:12px;color:#0868bd;font-weight:700">View all →</a>
        </div>
        @if($recentOrders->isNotEmpty())
          <table style="width:100%;font-size:12px;border-collapse:collapse;color:#334155">
            <thead>
              <tr style="border-bottom:1px solid #e2e8f0;color:#64748b;text-align:left">
                <th style="padding:6px 0">Order</th>
                <th style="padding:6px 0">Student</th>
                <th style="padding:6px 0">Amount</th>
                <th style="padding:6px 0">Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentOrders as $ro)
                <tr style="border-bottom:1px solid #f8fafc">
                  <td style="padding:8px 0;font-family:monospace;font-weight:700">
                    <a href="{{ route('admin.orders.show', $ro) }}" style="color:#0868bd">{{ $ro->order_number }}</a>
                  </td>
                  <td style="padding:8px 0">{{ $ro->user?->name ?? 'Guest' }}</td>
                  <td style="padding:8px 0;font-weight:700">{{ bdt($ro->amount_paise, true) }}</td>
                  <td style="padding:8px 0">
                    <span style="padding:2px 6px;border-radius:4px;font-size:10px;font-weight:700;color:#fff;background:{{ $ro->status_badge_color }}">
                      {{ $ro->status_label }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <p style="color:#64748b;font-size:12px;margin:10px 0">No orders recorded yet.</p>
        @endif
      </div>

      <!-- Upcoming Live Classes Table -->
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
          <h3 style="font-size:16px;margin:0;color:#082c50">{{ __('Upcoming Live Classes') }}</h3>
          <a href="{{ route('admin.live-classes.index') }}" style="font-size:12px;color:#0868bd;font-weight:700">Manage →</a>
        </div>
        @if($upcomingLiveClasses->isNotEmpty())
          <table style="width:100%;font-size:12px;border-collapse:collapse;color:#334155">
            <thead>
              <tr style="border-bottom:1px solid #e2e8f0;color:#64748b;text-align:left">
                <th style="padding:6px 0">Class Title</th>
                <th style="padding:6px 0">Course</th>
                <th style="padding:6px 0">Date & Time</th>
                <th style="padding:6px 0">Link</th>
              </tr>
            </thead>
            <tbody>
              @foreach($upcomingLiveClasses as $lc)
                <tr style="border-bottom:1px solid #f8fafc">
                  <td style="padding:8px 0;font-weight:700">{{ $lc->title }}</td>
                  <td style="padding:8px 0">{{ $lc->course?->title }}</td>
                  <td style="padding:8px 0">{{ bangla_date($lc->start_time, 'd M, h:i A') }}</td>
                  <td style="padding:8px 0">
                    <a href="{{ $lc->meeting_link }}" target="_blank" class="mini-btn">Join</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <p style="color:#64748b;font-size:12px;margin:10px 0">No upcoming live classes scheduled yet.</p>
          <a href="{{ route('admin.live-classes.create') }}" class="btn" style="padding:7px 14px;font-size:11px">Schedule Live Class +</a>
        @endif
      </div>
    </div>

  </div>
</section>

<style>
@media(max-width:1024px){
  .stats-grid{grid-template-columns:repeat(2, 1fr) !important;}
  .recent-tables-grid{grid-template-columns:1fr !important;}
}
@media(max-width:640px){
  .stats-grid{grid-template-columns:1fr !important;}
}
</style>
@endsection
