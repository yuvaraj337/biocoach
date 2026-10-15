@extends('layouts.app')
@section('title', __('Manage Categories') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
            <h1>{{ __('Manage Categories') }}</h1>
            <p class="section-subtitle">{{ __('Configure course categories: SSC Biology, HSC Biology, Medical Admission, Dental Admission.') }}</p>
        </div>
        <div style="display:flex;gap:10px">
            <a class="btn btn-outline" href="{{ route('admin.dashboard') }}">← {{ __('Dashboard') }}</a>
            <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">+ {{ __('Add Category') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div role="status" style="margin:16px 0;padding:12px;border-radius:9px;background:#e4f8ed;color:#11633d">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div role="alert" style="margin:16px 0;padding:12px;border-radius:9px;background:#fff0f2;color:#a61b38">{{ session('error') }}</div>
    @endif

    <div style="overflow-x:auto;margin-top:20px;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
        <table style="width:100%;border-collapse:collapse;min-width:650px">
            <thead>
                <tr style="background:#f5f8fc;text-align:left">
                    <th style="padding:13px">{{ __('Category Name') }}</th>
                    <th style="padding:13px">{{ __('Slug') }}</th>
                    <th style="padding:13px">{{ __('Courses Count') }}</th>
                    <th style="padding:13px">{{ __('Status') }}</th>
                    <th style="padding:13px;text-align:right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr style="border-top:1px solid #edf1f6">
                        <td style="padding:13px">
                            <strong>{{ $category->name }}</strong>
                            @if($category->description)
                                <br><small class="muted">{{ Str::limit($category->description, 60) }}</small>
                            @endif
                        </td>
                        <td style="padding:13px"><code>{{ $category->slug }}</code></td>
                        <td style="padding:13px;font-weight:600">{{ bn_number($category->courses_count) }}</td>
                        <td style="padding:13px">
                            <span class="pill" style="{{ $category->is_active ? 'background:#e4f8ed;color:#078451' : 'background:#f1f5f9;color:#64748b' }}">
                                {{ $category->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td style="padding:13px;text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.categories.edit', $category) }}">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" style="display:inline" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="border:0;background:none;color:#c52c4b;cursor:pointer;margin-left:10px">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="padding:24px;text-align:center">{{ __('No categories found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px">{{ $categories->links() }}</div>
</div></section>
@endsection
