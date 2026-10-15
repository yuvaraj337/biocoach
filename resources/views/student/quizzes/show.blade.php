@extends('layouts.app')
@section('title',$quiz->title.' | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:850px"><a class="text-link" href="{{ route('student.quizzes.index',$course) }}">← Course quizzes</a><div style="margin-top:18px;background:white;border:1px solid var(--line);border-radius:18px;padding:clamp(20px,4vw,38px)"><p class="eyebrow">TIMED MCQ ASSESSMENT</p><h1 style="font-size:36px">{{ $quiz->title }}</h1><p class="section-subtitle">{{ $quiz->description }}</p><div class="benefit-grid" style="margin:22px 0"><div class="benefit"><strong>{{ $quiz->questions->count() }}</strong><p>Questions</p></div><div class="benefit"><strong>{{ $quiz->duration_minutes }} min</strong><p>Time limit</p></div><div class="benefit"><strong>{{ $quiz->passing_percentage }}%</strong><p>Pass mark</p></div><div class="benefit"><strong>{{ $quiz->attempts_allowed }}</strong><p>Max attempts</p></div></div><ul style="line-height:1.9;color:var(--muted)"><li>The timer begins when you start.</li><li>Your answers are automatically scored after submission or when time expires.</li><li>Correct answers are not shown during the test.</li></ul>
@if($attempts->count())
<h2 style="font-size:22px;margin-top:24px">Previous results</h2>
@foreach($attempts as $old)
<p style="padding:10px;background:#f5f9fc;border-radius:8px">Attempt {{ $old->attempt_number }} · {{ $old->percentage }}% · {{ $old->passed?'Passed':'Not passed' }} · {{ $old->submitted_at?->format('d M Y, H:i') }}</p>
@endforeach
@endif
@if($inProgress || $attempts->count() < $quiz->attempts_allowed)<form method="POST" action="{{ route('student.quizzes.start',[$course,$quiz]) }}">@csrf<button class="btn">{{ $inProgress?'Resume quiz':'Start quiz' }} →</button></form>@else<p style="color:var(--muted);margin-top:18px">You have used all allowed attempts for this quiz.</p>@endif</div></div></section>
@endsection
