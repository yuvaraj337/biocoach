@extends('layouts.app')
@section('title', ($course->seo_title ?: $course->title).' | Jerin’s Coaching')
@section('meta_description', $course->seo_description ?: $course->summary)
@section('content')
<section class="section"><div class="container" style="max-width:820px">
    <a href="{{ route('courses.index') }}">← All courses</a><p><span class="pill">{{ $course->category?->name ?? 'Biology' }}</span></p>
    <h1 style="font-size:clamp(2.2rem,5vw,3.5rem)">{{ $course->title }}</h1>
    <p class="lead">{{ $course->summary }}</p><h2>{{ bdt($course->price_paise, true) }}</h2>
    <div style="line-height:1.8;margin:28px 0">{!! nl2br(e($course->description)) !!}</div>
    @php($previewLessons = $course->lessons()->where('is_published', true)->orderBy('sort_order')->get())
    @if($previewLessons->isNotEmpty())<section style="margin:28px 0;padding:20px;border:1px solid #e3eaf2;border-radius:14px;background:#fff"><h2>{{ __('Course curriculum') }}</h2><p class="muted">{{ $previewLessons->count() }} published lessons · preview lessons are available before enrolment.</p><div style="display:grid;gap:10px;margin-top:14px">@foreach($previewLessons as $lesson)<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px;background:#f7fbff;border-radius:9px"><span><strong>{{ $loop->iteration }}. {{ $lesson->title }}</strong>@if($lesson->summary)<small style="display:block;color:#5d6b7d;margin-top:3px">{{ $lesson->summary }}</small>@endif</span>@if($lesson->is_preview && auth()->check())<a class="mini-btn" href="{{ route('student.lessons.show',[$course,$lesson]) }}">{{ __('Watch preview') }}</a>@elseif($lesson->is_preview)<a class="mini-btn" href="{{ route('login') }}">{{ __('Watch preview') }}</a>@else<span class="pill">{{ __('Enrolled students') }}</span>@endif</div>@endforeach</div></section>@endif
    @auth
      @php($isEnrolled = auth()->user()->enrollments()->where('course_id', $course->id)->where('status', 'active')->exists())
      @if($isEnrolled)
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
          <span style="display:inline-block;padding:8px 14px;background:#d1fae5;color:#065f46;border-radius:8px;font-weight:700">{{ __('You are actively enrolled in this course') }}</span>
          <a class="btn" href="{{ route('student.courses') }}">{{ __('Go to Learning Portal →') }}</a>
        </div>
      @else
        <form method="POST" action="{{ route('checkout.start', $course) }}">
          @csrf
          <button class="btn" type="submit" style="padding:14px 28px;font-size:16px">
            {{ __('Enroll Now') }} — {{ bdt($course->price_paise, true) }} →
          </button>
        </form>
      @endif
    @else
      <a class="btn" href="{{ route('login') }}" style="padding:14px 28px;font-size:16px">Login to Enroll Now →</a>
    @endauth
</div></section>
@endsection
