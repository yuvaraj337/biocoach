@extends('layouts.app')
@section('title', 'Add Course | Jerin’s Coaching')
@section('content')
<section class="section"><div class="container" style="max-width:920px">
    <a href="{{ route('admin.courses.index') }}">← Manage courses</a><p class="eyebrow" style="margin-top:18px">ADMINISTRATION</p><h1>Add a course</h1>
    <form method="POST" action="{{ route('admin.courses.store') }}" style="margin-top:22px;padding:24px;background:#fff;border:1px solid #e3eaf2;border-radius:16px">@include('admin.courses._form')</form>
</div></section>
@endsection
