@extends('layouts.app')
@section('title', __('Assignment Submissions') . ' - ' . $assignment->title . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px">
        <div>
            <a class="btn btn-outline" style="padding:6px 12px;font-size:12px;margin-bottom:10px;display:inline-block" href="{{ route('admin.courses.assignments.index', $course) }}">← {{ __('Back to Assignments') }}</a>
            <p class="eyebrow">{{ $course->title }}</p>
            <h1>{{ $assignment->title }} ({{ __('Max Marks') }}: {{ bn_number($assignment->total_marks) }})</h1>
        </div>
    </div>

    @if(session('success'))
        <div role="status" style="margin:16px 0;padding:12px;border-radius:9px;background:#e4f8ed;color:#11633d">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div style="overflow-x:auto;margin-top:20px;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
        <table style="width:100%;border-collapse:collapse;min-width:750px">
            <thead>
                <tr style="background:#f5f8fc;text-align:left">
                    <th style="padding:13px">{{ __('Student') }}</th>
                    <th style="padding:13px">{{ __('Submitted Date') }}</th>
                    <th style="padding:13px">{{ __('Submission Content / File') }}</th>
                    <th style="padding:13px">{{ __('Status / Score') }}</th>
                    <th style="padding:13px">{{ __('Grade & Feedback') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr style="border-top:1px solid #edf1f6;vertical-align:top">
                        <td style="padding:13px">
                            <strong>{{ $submission->user->name }}</strong>
                            <div style="font-size:12px;color:#64748b">{{ $submission->user->email }}</div>
                        </td>
                        <td style="padding:13px;font-size:12px;color:#64748b">
                            {{ bangla_date($submission->submitted_at) }}
                        </td>
                        <td style="padding:13px;max-width:280px">
                            @if($submission->submission_text)
                                <div style="font-size:12px;color:#334155;background:#f8fafc;padding:8px;border-radius:6px;margin-bottom:6px;max-height:80px;overflow-y:auto;white-space:pre-line">
                                    {{ $submission->submission_text }}
                                </div>
                            @endif
                            @if($submission->attachment_path)
                                <a href="{{ Storage::url($submission->attachment_path) }}" target="_blank" class="mini-btn" style="font-size:11px">
                                    📄 {{ __('View Student File') }} ↗
                                </a>
                            @endif
                        </td>
                        <td style="padding:13px">
                            @if($submission->isGraded())
                                <span class="pill" style="background:#e4f8ed;color:#078451;font-weight:700">
                                    {{ bn_number($submission->marks_obtained) }} / {{ bn_number($assignment->total_marks) }}
                                </span>
                                @if($submission->feedback)
                                    <div style="font-size:11px;color:#64748b;margin-top:4px">
                                        "{{ Str::limit($submission->feedback, 60) }}"
                                    </div>
                                @endif
                            @else
                                <span class="pill" style="background:#fef3c7;color:#92400e;font-weight:700">
                                    {{ __('Pending Evaluation') }}
                                </span>
                            @endif
                        </td>
                        <td style="padding:13px">
                            <form method="POST" action="{{ route('admin.assignments.grade', [$assignment, $submission]) }}" style="display:grid;gap:6px;min-width:200px">
                                @csrf
                                <div style="display:flex;gap:6px;align-items:center">
                                    <input type="number" name="marks_obtained" value="{{ old('marks_obtained', $submission->marks_obtained) }}" min="0" max="{{ $assignment->total_marks }}" required placeholder="Marks" style="width:70px;border:1px solid #cbd5e1;border-radius:6px;padding:6px;font-size:12px">
                                    <span style="font-size:12px;color:#64748b">/ {{ bn_number($assignment->total_marks) }}</span>
                                </div>
                                <input type="text" name="feedback" value="{{ old('feedback', $submission->feedback) }}" placeholder="Teacher feedback..." style="width:100%;border:1px solid #cbd5e1;border-radius:6px;padding:6px;font-size:12px">
                                <button type="submit" class="mini-btn" style="background:#0868bd;color:#fff;border:none;padding:6px">{{ __('Submit Grade') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:24px;text-align:center">{{ __('No submissions received yet for this assignment.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px">{{ $submissions->links() }}</div>
</div></section>
@endsection
