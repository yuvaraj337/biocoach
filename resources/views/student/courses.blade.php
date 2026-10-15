@extends('layouts.app')
@section('title', 'My Enrolled Courses | Jerin’s Coaching')

@section('content')
<section class="section" style="background:#f8fafc;padding:36px 0 60px">
  <div class="container">
    
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px">
      <div>
        <p class="eyebrow">STUDENT LEARNING PORTAL</p>
        <h1 style="margin:4px 0 8px;font-size:30px">My Enrolled Courses</h1>
        <p class="section-subtitle">Access your active Biology lessons, chapter quizzes, study resources, and progress tracking.</p>
      </div>
      <div>
        <a class="btn btn-outline" href="{{ route('student.orders') }}">View Order History</a>
      </div>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:22px;font-weight:600">
        ✓ {{ session('success') }}
      </div>
    @endif

    <!-- Pending / Awaiting Verification Orders Banner -->
    @if(isset($pendingOrders) && $pendingOrders->count() > 0)
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:30px;box-shadow:0 2px 8px rgba(0,0,0,0.02)">
        <h3 style="margin:0 0 12px;font-size:16px;color:var(--navy);display:flex;align-items:center;gap:8px">
          <span>⏳</span> Pending Enrolment Verifications
        </h3>
        <div style="display:grid;gap:12px">
          @foreach($pendingOrders as $order)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;padding:12px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px">
              <div>
                <strong style="color:#0f172a;display:block">{{ $order->course->title }}</strong>
                <div style="font-size:12px;color:#64748b;margin-top:2px">
                  Order: <code>{{ $order->order_number }}</code> • 
                  Status: 
                  @if($order->isAwaitingVerification())
                    <span style="font-weight:700;color:#b45309">Awaiting Teacher Verification</span>
                  @elseif($order->isRejected())
                    <span style="font-weight:700;color:#dc2626">Rejected — Action Required</span>
                  @else
                    <span style="font-weight:700;color:#475569">Payment Not Submitted</span>
                  @endif
                </div>
              </div>
              <div>
                @if($order->isRejected())
                  <a href="{{ route('checkout.show', $order) }}" class="btn" style="padding:7px 14px;font-size:12px;background:#dc2626">
                    Resubmit Proof →
                  </a>
                @elseif($order->isAwaitingVerification())
                  <a href="{{ route('checkout.show', $order) }}" class="btn btn-outline" style="padding:7px 14px;font-size:12px">
                    Review Status →
                  </a>
                @else
                  <a href="{{ route('checkout.show', $order) }}" class="btn" style="padding:7px 14px;font-size:12px">
                    Complete Payment →
                  </a>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Active Enrolled Courses Grid -->
    <h3 style="margin:0 0 16px;font-size:18px;color:var(--navy)">Active Courses</h3>
    
    <div class="resource-grid" style="margin-top:0">
      @forelse($enrollments as $enrollment)
        <article class="resource" style="flex-direction:column;align-items:stretch;padding:20px;border-radius:14px;border:1px solid #dcece3;background:#fff">
          <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:14px">
            <div class="resource-icon" style="background:#e4f8ed;color:#078451">📘</div>
            <div style="flex:1">
              <span style="display:inline-block;padding:2px 7px;background:#d1fae5;color:#065f46;border-radius:4px;font-size:10px;font-weight:800;text-transform:uppercase;margin-bottom:6px">
                Enrolled
              </span>
              <h3 style="margin:0 0 4px;font-size:16px;color:var(--navy)">{{ $enrollment->course->title }}</h3>
              <p style="margin:0;font-size:12px;color:#64748b;line-height:1.4">{{ Str::limit($enrollment->course->summary, 90) }}</p>
            </div>
          </div>

          <div style="border-top:1px solid #f1f5f9;padding-top:14px;margin-top:auto;display:flex;gap:10px;flex-wrap:wrap">
            @php($firstLesson = $enrollment->course->lessons()->where('is_published', true)->orderBy('sort_order')->first())
            @if($firstLesson)
              <a class="btn" style="flex:1;padding:10px;font-size:12px;text-align:center" href="{{ route('student.lessons.show', [$enrollment->course, $firstLesson]) }}">
                Start Learning →
              </a>
            @else
              <span class="btn btn-outline" style="flex:1;padding:10px;font-size:12px;text-align:center;color:#64748b">
                Lessons Soon
              </span>
            @endif
            <a class="btn btn-outline" style="padding:10px 14px;font-size:12px" href="{{ route('student.quizzes.index', $enrollment->course) }}">
              Quizzes
            </a>
          </div>
        </article>
      @empty
        <div style="padding:40px;background:#fff;border:1px solid #e3eaf2;border-radius:14px;grid-column:1/-1;text-align:center">
          <div style="font-size:44px;margin-bottom:12px">📚</div>
          <h3 style="margin:0 0 6px;color:#1e293b;font-size:20px">You haven't enrolled in any courses yet.</h3>
          <p style="color:#64748b;margin:0 0 20px;font-size:14px">Explore Dr. Jerin’s biology masterclasses and start your learning journey without fear.</p>
          <a class="btn" style="padding:12px 24px" href="{{ route('courses.index') }}">Browse Biology Courses →</a>
        </div>
      @endforelse
    </div>

  </div>
</section>
@endsection
