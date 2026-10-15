@extends('layouts.app')
@section('title', __('Edit Category') . ' - ' . $category->name . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container" style="max-width:650px">
    <div style="margin-bottom:20px">
        <a class="btn btn-outline" style="padding:6px 12px;font-size:12px;margin-bottom:10px;display:inline-block" href="{{ route('admin.categories.index') }}">← {{ __('Back to Categories') }}</a>
        <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
        <h1>{{ __('Edit Category') }}</h1>
    </div>

    @if($errors->any())
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <div style="background:#fff;border:1px solid #e3eaf2;border-radius:14px;padding:24px">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Category Name') }} *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Slug') }} *</label>
                <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" required style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:600;margin-bottom:6px">{{ __('Description') }}</label>
                <textarea name="description" rows="3" style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:10px;font-size:14px">{{ old('description', $category->description) }}</textarea>
            </div>

            <div style="margin-bottom:24px">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:600">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    {{ __('Active Category (Visible in course filters and creation)') }}
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="padding:12px 24px">{{ __('Update Category') }}</button>
        </form>
    </div>
</div></section>
@endsection
