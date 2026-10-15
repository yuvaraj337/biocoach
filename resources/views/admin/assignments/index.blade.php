@extends('layouts.app')
@section('title', __('Manage Assignments') . ' - ' . $course->title . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <p class="eyebrow">{{ __('COURSE CURRICULUM') }}</p>
            <h1>{{ __('Assignments') }}: {{ $course->title }}</h1>
            <p class="section-subtitle">{{ __('Create, review, and grade homework and written Biology assignments.') }}</p>
        </div>
        <div style="display:flex;gap:10px">
            <a class="btn btn-outline" href="{{ route('admin.courses.index') }}">← {{ __('Back to Courses') }}</a>
            <a class="btn btn-primary" href="{{ route('admin.courses.assignments.create', $course) }}">+ {{ __('Add Assignment') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div role="status" style="margin:16px 0;padding:12px;border-radius:9px;background:#e4f8ed;color:#11633d">{{ session('success') }}</div>
    @endif

    <div style="overflow-x:auto;margin-top:20px;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
        <table style="width:100%;border-collapse:collapse;min-width:700px">
            <thead>
                <tr style="background:#f5f8fc;text-align:left">
                    <th style="padding:13px">{{ __('Assignment Title') }}</th>
                    <th style="padding:13px">{{ __('Total Marks') }}</th>
                    <th style="padding:13px">{{ __('Due Date') }}</th>
                    <th style="padding:13px">{{ __('Submissions') }}</th>
                    <th style="padding:13px">{{ __('Status') }}</th>
                    <th style="padding:13px;text-align:right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    <tr style="border-top:1px solid #edf1f6">
                        <td style="padding:13px">
                            <strong>{{ $assignment->title }}</strong>
                        </td>
                        <td style="padding:13px;font-weight:700">{{ bn_number($assignment->total_marks) }}</td>
                        <td style="padding:13px;font-size:12px;color:#64748b">
                            {{ $assignment->due_date ? bangla_date($assignment->due_date) : __('No deadline') }}
                        </td>
                        <td style="padding:13px">
                            <a href="{{ route('admin.courses.assignments.show', [$course, $assignment]) }}" style="font-weight:700;color:#0868bd">
                                {{ bn_number($assignment->submissions_count) }} {{ __('submissions') }} →
                            </a>
                        </td>
                        <td style="padding:13px">
                            <span class="pill" style="{{ $assignment->is_published ? 'background:#e4f8ed;color:#078451' : 'background:#f1f5f9;color:#64748b' }}">
                                {{ $assignment->is_published ? __('Published') : __('Draft') }}
                            </span>
                        </td>
                        <td style="padding:13px;text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.courses.assignments.show', [$course, $assignment]) }}">{{ __('Review & Grade') }}</a> · 
                            <a href="{{ route('admin.courses.assignments.edit', [$course, $assignment]) }}">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.courses.assignments.destroy', [$course, $assignment]) }}" style="display:inline" onsubmit="return confirm('Delete this assignment?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="border:0;background:none;color:#c52c4b;cursor:pointer;margin-left:10px">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:24px;text-align:center">{{ __('No assignments added to this course yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px">{{ $assignments->links() }}</div>
</div></section>
@endsection
