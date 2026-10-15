@extends('layouts.app')
@section('title', 'Manage Courses | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div><p class="eyebrow">ADMINISTRATION</p><h1>Manage courses</h1><p class="section-subtitle">Create, edit, publish or unpublish course listings.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.courses.create') }}">+ Add course</a>
    </div>
    @if(session('success'))<div role="status" style="margin:16px 0;padding:12px;border-radius:9px;background:#e4f8ed;color:#11633d">{{ session('success') }}</div>@endif
    @if($errors->any())<div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <div style="overflow-x:auto;margin-top:20px;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
    <table style="width:100%;border-collapse:collapse;min-width:700px">
        <thead><tr style="background:#f5f8fc;text-align:left"><th style="padding:13px">Course</th><th style="padding:13px">Category</th><th style="padding:13px">Price</th><th style="padding:13px">Status</th><th style="padding:13px">Actions</th></tr></thead>
        <tbody>
        @forelse($courses as $course)
        <tr style="border-top:1px solid #edf1f6">
            <td style="padding:13px"><strong>{{ $course->title }}</strong><br><small class="muted">/{{ $course->slug }}</small></td>
            <td style="padding:13px">{{ $course->category?->name ?? 'Uncategorised' }}</td>
            <td style="padding:13px">{{ bdt($course->price_cents, true) }}</td>
            <td style="padding:13px"><span class="pill">{{ $course->is_published ? 'Published' : 'Draft' }}</span></td>
            <td style="padding:13px;white-space:nowrap"><a href="{{ route('admin.courses.edit', $course) }}">Edit</a> · <a href="{{ route('admin.courses.lessons.index', $course) }}">Lessons</a> · <a href="{{ route('admin.courses.quizzes.index', $course) }}">Quizzes</a> · <a href="{{ route('admin.courses.assignments.index', $course) }}">Assignments</a> @if($course->is_published) · <a href="{{ route('courses.show', $course) }}" target="_blank" rel="noopener">View</a>@endif
                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" style="display:inline" onsubmit="return confirm('Delete this course?')">@csrf @method('DELETE')<button type="submit" style="border:0;background:none;color:#c52c4b;cursor:pointer;margin-left:8px">Delete</button></form>
            </td>
        </tr>
        @empty <tr><td colspan="5" style="padding:24px;text-align:center">No courses yet. Add your first course to get started.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:20px">{{ $courses->links() }}</div>
</div></section>
@endsection
