@extends('layouts.app')
@section('title', __('Live Classes Management') . ' | ' . __("Jerin's Coaching"))

@section('content')
<section class="section"><div class="container">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div>
            <p class="eyebrow">{{ __('ADMINISTRATION') }}</p>
            <h1>{{ __('Live Classes & Masterclasses') }}</h1>
            <p class="section-subtitle">{{ __('Schedule interactive Biology live classes (Asia/Dhaka BST) with Google Meet / Zoom meeting links.') }}</p>
        </div>
        <div style="display:flex;gap:10px">
            <a class="btn btn-outline" href="{{ route('admin.dashboard') }}">← {{ __('Dashboard') }}</a>
            <a class="btn btn-primary" href="{{ route('admin.live-classes.create') }}">+ {{ __('Schedule Live Class') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div role="status" style="margin:16px 0;padding:12px;border-radius:9px;background:#e4f8ed;color:#11633d">{{ session('success') }}</div>
    @endif

    <div style="overflow-x:auto;margin-top:20px;background:#fff;border:1px solid #e3eaf2;border-radius:14px">
        <table style="width:100%;border-collapse:collapse;min-width:750px">
            <thead>
                <tr style="background:#f5f8fc;text-align:left">
                    <th style="padding:13px">{{ __('Class Title') }}</th>
                    <th style="padding:13px">{{ __('Course') }}</th>
                    <th style="padding:13px">{{ __('Teacher') }}</th>
                    <th style="padding:13px">{{ __('Scheduled Time (BST)') }}</th>
                    <th style="padding:13px">{{ __('Status') }}</th>
                    <th style="padding:13px;text-align:right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $c)
                    <tr style="border-top:1px solid #edf1f6">
                        <td style="padding:13px">
                            <strong>{{ $c->title }}</strong>
                            <div style="font-size:12px;color:#0868bd">
                                <a href="{{ $c->meeting_link }}" target="_blank" rel="noopener">🔗 Join Link ↗</a>
                            </div>
                        </td>
                        <td style="padding:13px">{{ $c->course->title }}</td>
                        <td style="padding:13px">{{ $c->teacher->name ?? 'Dr. Tanjina Jerin' }}</td>
                        <td style="padding:13px;font-size:13px;color:#334155">
                            {{ bangla_date($c->start_time) }}
                            <br><small class="muted">{{ bn_number($c->duration_minutes ?? 60) }} {{ __('min') }}</small>
                        </td>
                        <td style="padding:13px">
                            <span class="pill" style="{{ $c->status === 'live' ? 'background:#fee2e2;color:#991b1b' : ($c->status === 'completed' ? 'background:#f1f5f9;color:#64748b' : 'background:#e0f2fe;color:#0369a1') }}">
                                {{ ucfirst($c->status ?? 'scheduled') }}
                            </span>
                        </td>
                        <td style="padding:13px;text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.live-classes.edit', $c) }}">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.live-classes.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Cancel and delete this live class?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="border:0;background:none;color:#c52c4b;cursor:pointer;margin-left:10px">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:24px;text-align:center">{{ __('No live classes scheduled.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:20px">{{ $classes->links() }}</div>
</div></section>
@endsection
