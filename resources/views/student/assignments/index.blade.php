@extends('layouts.app')
@section('title', __('Course Assignments') . ' - ' . $course->title . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section" style="background:#f8fafc;padding:36px 0 60px">
  <div class="container">
    
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px">
      <div>
        <p class="eyebrow">{{ __('STUDENT ASSIGNMENTS') }}</p>
        <h1 style="margin:4px 0 8px;font-size:28px">{{ $course->title }}</h1>
        <p class="section-subtitle">{{ __('Submit written biology assignments, diagrams, and review teacher marks and feedback.') }}</p>
      </div>
      <div>
        <a class="btn btn-outline" href="{{ route('student.courses') }}">← {{ __('Back to My Courses') }}</a>
      </div>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:22px;font-weight:600">
        ✓ {{ session('success') }}
      </div>
    @endif

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
      <table style="width:100%;border-collapse:collapse;font-size:13px;color:#334155">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;text-align:left">
            <th style="padding:14px 18px">{{ __('Assignment') }}</th>
            <th style="padding:14px 18px">{{ __('Due Date') }}</th>
            <th style="padding:14px 18px">{{ __('Total Marks') }}</th>
            <th style="padding:14px 18px">{{ __('Status') }}</th>
            <th style="padding:14px 18px">{{ __('Marks / Feedback') }}</th>
            <th style="padding:14px 18px;text-align:right">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assignments as $assignment)
            @php($sub = $assignment->submissions->first())
            <tr style="border-bottom:1px solid #f1f5f9">
              <td style="padding:14px 18px">
                <strong style="color:var(--navy);font-size:14px;display:block">{{ $assignment->title }}</strong>
                @if($assignment->description)
                  <span style="color:#64748b;font-size:12px">{{ Str::limit($assignment->description, 70) }}</span>
                @endif
              </td>
              <td style="padding:14px 18px;color:#64748b">
                {{ $assignment->due_at ? bangla_date($assignment->due_at) : __('No deadline') }}
              </td>
              <td style="padding:14px 18px;font-weight:600">
                {{ bn_number($assignment->total_marks) }}
              </td>
              <td style="padding:14px 18px">
                @if(!$sub)
                  <span style="display:inline-block;padding:3px 8px;background:#fef3c7;color:#b45309;border-radius:4px;font-size:11px;font-weight:700">
                    {{ __('Not Submitted') }}
                  </span>
                @elseif($sub->graded_at)
                  <span style="display:inline-block;padding:3px 8px;background:#e4f8ed;color:#078451;border-radius:4px;font-size:11px;font-weight:700">
                    {{ __('Graded') }}
                  </span>
                @else
                  <span style="display:inline-block;padding:3px 8px;background:#e0f2fe;color:#0369a1;border-radius:4px;font-size:11px;font-weight:700">
                    {{ __('Submitted — Pending Review') }}
                  </span>
                @endif
              </td>
              <td style="padding:14px 18px">
                @if($sub && $sub->graded_at)
                  <strong style="color:#078451">{{ bn_number($sub->marks) }} / {{ bn_number($assignment->total_marks) }}</strong>
                  @if($sub->feedback)
                    <div style="font-size:11px;color:#64748b;margin-top:2px">{{ Str::limit($sub->feedback, 40) }}</div>
                  @endif
                @else
                  <span style="color:#94a3b8">—</span>
                @endif
              </td>
              <td style="padding:14px 18px;text-align:right">
                <a class="mini-btn" href="{{ route('student.assignments.show', [$course, $assignment]) }}">
                  {{ $sub ? __('View Submission') : __('Submit Now') }} →
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="padding:36px;text-align:center;color:#64748b">
                {{ __('No assignments published for this course yet.') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</section>
@endsection
