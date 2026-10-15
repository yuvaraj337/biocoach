@extends('layouts.app')
@section('title', __('Live Classes') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section" style="background:#f8fafc;padding:36px 0 60px">
  <div class="container">
    
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px">
      <div>
        <p class="eyebrow">{{ __('STUDENT LEARNING PORTAL') }}</p>
        <h1 style="margin:4px 0 8px;font-size:30px">{{ __('Live Classes') }}</h1>
        <p class="section-subtitle">{{ __('Join Dr. Jerin’s interactive live Biology sessions, ask questions, and review past classes.') }}</p>
      </div>
      <div>
        <a class="btn btn-outline" href="{{ route('student.courses') }}">{{ __('My Enrolled Courses') }}</a>
      </div>
    </div>

    <!-- Upcoming & Today's Live Classes -->
    <h3 style="margin:0 0 16px;font-size:18px;color:var(--navy);display:flex;align-items:center;gap:8px">
      <span style="color:#ef4444;animation:pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite">🔴</span> {{ __('Upcoming & Today\'s Live Classes') }}
    </h3>

    <div class="resource-grid" style="margin-top:0;margin-bottom:36px">
      @forelse($upcomingClasses as $class)
        <article class="resource" style="flex-direction:column;align-items:stretch;padding:22px;border-radius:14px;border:1px solid #dcece3;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px">
            <span style="display:inline-block;padding:3px 9px;background:#fee2e2;color:#991b1b;border-radius:6px;font-size:11px;font-weight:800;text-transform:uppercase">
              {{ $class->status === 'live' ? '🔴 LIVE NOW' : 'SCHEDULED' }}
            </span>
            <span style="font-size:12px;color:#0868bd;font-weight:700">
              {{ $class->course->title }}
            </span>
          </div>

          <h3 style="margin:0 0 8px;font-size:18px;color:var(--navy)">{{ $class->title }}</h3>
          @if($class->description)
            <p style="margin:0 0 14px;font-size:13px;color:#64748b;line-height:1.5">{{ Str::limit($class->description, 120) }}</p>
          @endif

          <div style="background:#f8fafc;border-radius:8px;padding:12px;margin-bottom:16px;font-size:12px;color:#334155;display:grid;gap:6px">
            <div><strong>{{ __('Instructor') }}:</strong> {{ $class->teacher->name ?? "Dr. Tanjina Jerin" }}</div>
            <div><strong>{{ __('Platform') }}:</strong> {{ $class->platform ?? 'Google Meet' }}</div>
            <div><strong>{{ __('Time') }} (BST):</strong> {{ bangla_date($class->start_time) }}</div>
            <div><strong>{{ __('Duration') }}:</strong> {{ bn_number($class->duration_minutes ?? 60) }} {{ __('min') }}</div>
          </div>

          <div style="border-top:1px solid #f1f5f9;padding-top:14px;margin-top:auto">
            @if($class->meeting_link)
              <a class="btn" style="width:100%;text-align:center;padding:10px 16px;font-size:13px;background:#0868bd" href="{{ $class->meeting_link }}" target="_blank" rel="noopener noreferrer">
                {{ __('Join Live Class Now') }} ↗
              </a>
            @else
              <span class="btn btn-outline" style="width:100%;text-align:center;padding:10px 16px;font-size:13px;color:#94a3b8">
                {{ __('Link Will Be Available Soon') }}
              </span>
            @endif
          </div>
        </article>
      @empty
        <div style="padding:36px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;grid-column:1/-1;text-align:center">
          <div style="font-size:36px;margin-bottom:10px">📅</div>
          <h4 style="margin:0 0 6px;color:#1e293b;font-size:17px">{{ __('No live classes scheduled at this moment.') }}</h4>
          <p style="color:#64748b;margin:0;font-size:13px">{{ __('New live masterclass schedules will be announced here for your enrolled courses.') }}</p>
        </div>
      @endforelse
    </div>

    <!-- Past Recorded Live Classes -->
    @if(isset($pastClasses) && $pastClasses->count() > 0)
      <h3 style="margin:0 0 16px;font-size:18px;color:var(--navy)">{{ __('Past Live Classes') }}</h3>
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:13px;color:#334155">
          <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;text-align:left">
              <th style="padding:12px 16px">{{ __('Class Title') }}</th>
              <th style="padding:12px 16px">{{ __('Course') }}</th>
              <th style="padding:12px 16px">{{ __('Date & Time') }}</th>
              <th style="padding:12px 16px">{{ __('Status') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pastClasses as $past)
              <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:12px 16px;font-weight:600;color:var(--navy)">{{ $past->title }}</td>
                <td style="padding:12px 16px;color:#64748b">{{ $past->course->title }}</td>
                <td style="padding:12px 16px;color:#64748b">{{ bangla_date($past->start_time) }}</td>
                <td style="padding:12px 16px"><span style="display:inline-block;padding:2px 8px;background:#f1f5f9;color:#64748b;border-radius:4px;font-size:11px;font-weight:700">{{ __('Completed') }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

  </div>
</section>
@endsection
