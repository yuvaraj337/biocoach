@extends('layouts.app')
@section('title', $assignment->title . ' - ' . $course->title . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section" style="background:#f8fafc;padding:36px 0 60px">
  <div class="container" style="max-width:800px">
    
    <div style="margin-bottom:24px">
      <a class="btn btn-outline" style="padding:8px 16px;font-size:12px;margin-bottom:12px;display:inline-block" href="{{ route('student.assignments.index', $course) }}">
        ← {{ __('Back to Assignments') }}
      </a>
      <p class="eyebrow">{{ $course->title }}</p>
      <h1 style="margin:4px 0 8px;font-size:28px">{{ $assignment->title }}</h1>
      <div style="display:flex;gap:16px;font-size:13px;color:#64748b;flex-wrap:wrap">
        <span><strong>{{ __('Total Marks') }}:</strong> {{ bn_number($assignment->total_marks) }}</span>
        <span><strong>{{ __('Due Date') }}:</strong> {{ $assignment->due_at ? bangla_date($assignment->due_at) : __('No deadline') }}</span>
      </div>
    </div>

    @if(session('success'))
      <div role="status" style="padding:14px 18px;background:#e4f8ed;border:1px solid #95d5b2;color:#11633d;border-radius:10px;margin-bottom:22px;font-weight:600">
        ✓ {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div role="alert" style="padding:14px 18px;background:#fee2e2;border:1px solid #f87171;color:#b91c1c;border-radius:10px;margin-bottom:22px">
        <ul style="margin:0;padding-left:18px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Assignment Instructions Card -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;margin-bottom:28px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
      <h3 style="margin:0 0 12px;font-size:17px;color:var(--navy)">{{ __('Assignment Instructions') }}</h3>
      <div style="color:#334155;font-size:14px;line-height:1.7;white-space:pre-line">
        {{ $assignment->description ?? __('Follow instructions given during the lesson.') }}
      </div>

      @if($assignment->file_path)
        <div style="margin-top:18px;padding-top:16px;border-top:1px solid #f1f5f9">
          <a class="mini-btn" href="{{ Storage::url($assignment->file_path) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px">
            📥 {{ __('Download Assignment Resource / Worksheet') }}
          </a>
        </div>
      @endif
    </div>

    <!-- Submission Card -->
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.03)">
      @if($submission)
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
          <h3 style="margin:0;font-size:17px;color:var(--navy)">{{ __('Your Submission') }}</h3>
          <span style="font-size:12px;color:#64748b">
            {{ __('Submitted on') }}: {{ bangla_date($submission->submitted_at) }}
          </span>
        </div>

        @if($submission->graded_at)
          <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:16px;margin-bottom:20px">
            <div style="font-size:12px;color:#15803d;font-weight:700;text-transform:uppercase">{{ __('Teacher Evaluation') }}</div>
            <div style="font-size:24px;color:#166534;font-weight:800;margin:4px 0">
              {{ bn_number($submission->marks) }} / {{ bn_number($assignment->total_marks) }}
            </div>
            @if($submission->feedback)
              <div style="font-size:13px;color:#14532d;margin-top:8px;line-height:1.6">
                <strong>{{ __('Feedback') }}:</strong> {{ $submission->feedback }}
              </div>
            @endif
          </div>
        @else
          <div style="background:#fefce8;border:1px solid #fde047;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#854d0e">
            ⏳ {{ __('Your submission has been received and is awaiting teacher evaluation.') }}
          </div>
        @endif

        @if($submission->submission_text)
          <div style="background:#f8fafc;border-radius:8px;padding:16px;font-size:13px;color:#334155;line-height:1.6;margin-bottom:16px;white-space:pre-line">
            {{ $submission->submission_text }}
          </div>
        @endif

        @if($submission->file_path)
          <div style="margin-top:12px">
            <a class="mini-btn" href="{{ Storage::url($submission->file_path) }}" target="_blank">
              📄 {{ __('View Uploaded Document') }}
            </a>
          </div>
        @endif

      @else
        <h3 style="margin:0 0 16px;font-size:17px;color:var(--navy)">{{ __('Submit Assignment') }}</h3>

        <form action="{{ route('student.assignments.submit', [$course, $assignment]) }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div style="margin-bottom:16px">
            <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px">
              {{ __('Answer Text / Notes') }} ({{ __('Optional if uploading file') }})
            </label>
            <textarea name="submission_text" rows="5" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-family:inherit;font-size:13px" placeholder="{{ __('Type your solution, answer notes or observations here...') }}">{{ old('submission_text') }}</textarea>
          </div>

          <div style="margin-bottom:20px">
            <label style="display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px">
              {{ __('Upload Document / Diagram') }} (PDF, PNG, JPG - max 10MB)
            </label>
            <input type="file" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.zip" style="font-size:13px">
          </div>

          <button type="submit" class="btn" style="padding:12px 24px;background:#0868bd">
            {{ __('Submit Assignment') }} →
          </button>
        </form>
      @endif
    </div>

  </div>
</section>
@endsection
