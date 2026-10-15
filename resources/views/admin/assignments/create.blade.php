@extends('layouts.app')
@section('title', __('Add Assignment') . ' - ' . $course->title . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container" style="max-width:700px">
    <div style="margin-bottom:20px">
        <a class="btn btn-outline" style="padding:6px 12px;font-size:12px;margin-bottom:10px;display:inline-block" href="{{ route('admin.courses.assignments.index', $course) }}">← {{ __('Back to Assignments') }}</a>
        <p class="eyebrow">{{ $course->title }}</p>
        <h1>{{ __('Add New Assignment') }}</h1>
    </div>

    @if($errors->any())
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div style="background:#fff;border:1px solid #e3eaf2;border-radius:14px;padding:24px">
        <form method="POST" action="{{ route('admin.courses.assignments.store', $course) }}">
            @csrf

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Assignment Title') }} *</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Draw and label the stages of Meiosis I" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Linked Lesson') }} ({{ __('Optional') }})</label>
                <select name="lesson_id" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                    <option value="">{{ __('-- None / General Course Assignment --') }}</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Total Marks') }} *</label>
                    <input type="number" name="total_marks" value="{{ old('total_marks', 100) }}" min="5" max="500" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Due Date') }} ({{ __('Optional') }})</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Instructions / Questions') }}</label>
                <textarea name="description" rows="5" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px" placeholder="Provide full assignment requirements, formatting notes, and rubrics...">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:24px">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
                    {{ __('Published (Visible to enrolled students)') }}
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="padding:12px 24px">{{ __('Create Assignment') }}</button>
        </form>
    </div>
</div></section>
@endsection
