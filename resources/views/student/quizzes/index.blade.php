@extends('layouts.app')
@section('title','Course Quizzes | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container"><a class="text-link" href="{{ route('student.courses') }}">← My courses</a><p class="eyebrow" style="margin-top:18px">PRACTICE & PROGRESS</p><h1 style="font-size:36px">{{ $course->title }} quizzes</h1><p class="section-subtitle">Practice with timed assessments and get your score immediately after submitting.</p><div class="resource-grid">@forelse($quizzes as $quiz)<article class="resource"><div class="resource-icon">📝</div><div><h3>{{ $quiz->title }}</h3><p>{{ $quiz->description ?: 'Multiple-choice assessment' }}<br>{{ $quiz->questions_count }} questions · {{ $quiz->duration_minutes }} min · Pass {{ $quiz->passing_percentage }}%</p><a class="mini-btn" href="{{ route('student.quizzes.show',[$course,$quiz]) }}">Open quiz →</a></div></article>@empty<p>No published quizzes are available yet.</p>@endforelse</div></div></section>
@endsection
