@extends('layouts.app')
@section('title', __('Edit Live Class') . ' - ' . $liveClass->title . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container" style="max-width:700px">
    <div style="margin-bottom:20px">
        <a class="btn btn-outline" style="padding:6px 12px;font-size:12px;margin-bottom:10px;display:inline-block" href="{{ route('admin.live-classes.index') }}">← {{ __('Back to Live Classes') }}</a>
        <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
        <h1>{{ __('Edit Live Class') }}</h1>
    </div>

    @if($errors->any())
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div style="background:#fff;border:1px solid #e3eaf2;border-radius:14px;padding:24px">
        <form method="POST" action="{{ route('admin.live-classes.update', $liveClass) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Course') }} *</label>
                <select name="course_id" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $liveClass->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Class Title') }} *</label>
                <input type="text" name="title" value="{{ old('title', $liveClass->title) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Teacher / Instructor') }}</label>
                <select name="teacher_id" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $liveClass->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }} ({{ ucfirst($teacher->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Meeting Link') }} (Google Meet / Zoom) *</label>
                    <input type="url" name="meeting_link" value="{{ old('meeting_link', $liveClass->meeting_link) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Platform') }} *</label>
                    <select name="platform" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                        <option value="Google Meet" {{ old('platform', $liveClass->platform) === 'Google Meet' ? 'selected' : '' }}>Google Meet</option>
                        <option value="Zoom" {{ old('platform', $liveClass->platform) === 'Zoom' ? 'selected' : '' }}>Zoom</option>
                        <option value="YouTube Live" {{ old('platform', $liveClass->platform) === 'YouTube Live' ? 'selected' : '' }}>YouTube Live</option>
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Start Time') }} *</label>
                    <input type="datetime-local" name="start_time" value="{{ old('start_time', $liveClass->start_time ? $liveClass->start_time->format('Y-m-d\TH:i') : '') }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Duration (Minutes)') }} *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $liveClass->duration_minutes ?? 60) }}" min="15" max="300" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Status') }} *</label>
                    <select name="status" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                        <option value="scheduled" {{ old('status', $liveClass->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="live" {{ old('status', $liveClass->status) === 'live' ? 'selected' : '' }}>Live Now</option>
                        <option value="completed" {{ old('status', $liveClass->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $liveClass->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:24px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Description & Preparation Notes') }}</label>
                <textarea name="description" rows="3" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">{{ old('description', $liveClass->description) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="padding:12px 24px">{{ __('Update Class') }}</button>
        </form>
    </div>
</div></section>
@endsection
