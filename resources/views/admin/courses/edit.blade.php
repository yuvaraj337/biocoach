@extends('layouts.app')
@section('title', 'Edit Course | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:920px">
    <a href="{{ route('admin.courses.index') }}">← Manage courses</a><p class="eyebrow" style="margin-top:18px">ADMINISTRATION</p><h1>Edit course</h1><p class="section-subtitle">{{ $course->title }}</p>
    <form method="POST" action="{{ route('admin.courses.update', $course) }}" style="margin-top:22px;padding:24px;background:#fff;border:1px solid #e3eaf2;border-radius:16px">@method('PUT') @include('admin.courses._form')</form>
</div></section>
@endsection
