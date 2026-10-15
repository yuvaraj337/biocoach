<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Course;
use App\Models\LiveClass;
use App\Models\User;
use Illuminate\Http\Request;

class LiveClassController extends Controller
{
    public function index()
    {
        $classes = LiveClass::with(['course', 'teacher'])->latest('start_time')->paginate(15);
        return view('admin.live_classes.index', compact('classes'));
    }

    public function create()
    {
        $courses = Course::where('is_published', true)->get();
        $teachers = User::whereIn('role', ['teacher', 'admin', 'super_admin'])->get();
        return view('admin.live_classes.create', compact('courses', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'meeting_link' => ['required', 'url', 'max:255'],
            'platform' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
        ]);

        $validated['teacher_id'] = $validated['teacher_id'] ?? auth()->id();
        $liveClass = LiveClass::create($validated);

        AdminAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action' => 'live_class_scheduled',
            'subject_type' => LiveClass::class,
            'subject_id' => $liveClass->id,
            'reason' => "Scheduled live class '{$liveClass->title}' for " . $liveClass->start_time->format('d M Y, h:i A'),
        ]);

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class scheduled successfully.');
    }

    public function edit(LiveClass $liveClass)
    {
        $courses = Course::where('is_published', true)->get();
        $teachers = User::whereIn('role', ['teacher', 'admin', 'super_admin'])->get();
        return view('admin.live_classes.edit', compact('liveClass', 'courses', 'teachers'));
    }

    public function update(Request $request, LiveClass $liveClass)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'meeting_link' => ['required', 'url', 'max:255'],
            'platform' => ['required', 'string', 'max:50'],
            'start_time' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:300'],
            'status' => ['required', 'string', 'in:scheduled,live,completed,cancelled'],
        ]);

        $liveClass->update($validated);

        return redirect()->route('admin.live-classes.index')->with('success', 'Live class updated successfully.');
    }

    public function destroy(LiveClass $liveClass)
    {
        $liveClass->delete();
        return redirect()->route('admin.live-classes.index')->with('success', 'Live class cancelled and deleted.');
    }
}
