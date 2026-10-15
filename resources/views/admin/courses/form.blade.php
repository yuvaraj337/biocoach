@extends('layouts.app')
@section('title', $course->exists ? 'Edit Course' : 'Create Course')
@section('content')
<section class="section"><div class="container" style="max-width:760px"><h1>{{ $course->exists ? 'Edit course' : 'Create course' }}</h1>
<form method="POST" action="{{ $course->exists ? route('admin.courses.update', $course) : route('admin.courses.store') }}">@csrf @if($course->exists) @method('PUT') @endif
<label>Title<br><input required name="title" value="{{ old('title', $course->title) }}"></label><br><br>
<label>Category<br><select name="category_id" style="padding:12px;width:100%;border-radius:10px;border:1px solid #d8dee5"><option value="">Select category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $course->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select></label><br><br>
<label>Summary<br><input name="summary" value="{{ old('summary', $course->summary) }}"></label><br><br>
<label>Description<br><textarea name="description" rows="7" style="width:100%;padding:12px;border:1px solid #d8dee5;border-radius:10px">{{ old('description', $course->description) }}</textarea></label><br><br>
<label>Price (₹)<br><input required type="number" min="0" step="0.01" name="price_rupees" value="{{ old('price_rupees', $course->exists ? $course->price_paise/100 : '0.00') }}"></label><br><br>
<label><input type="checkbox" name="is_published" value="1" style="width:auto" @checked(old('is_published', $course->is_published))> Publish course</label><br><br>
<button class="btn" type="submit">Save course</button></form></div></section>
@endsection
