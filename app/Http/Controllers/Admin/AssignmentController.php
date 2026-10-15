<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Course $course)
    {
        $assignments = $course->assignments()->withCount('submissions')->latest()->paginate(15);
        return view('admin.assignments.index', compact('course', 'assignments'));
    }

    public function create(Course $course)
    {
        $lessons = $course->lessons()->orderBy('sort_order')->get();
        return view('admin.assignments.create', compact('course', 'lessons'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'total_marks' => ['required', 'integer', 'min:5', 'max:500'],
            'due_date' => ['nullable', 'date'],
            'is_published' => ['boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published', true);
        $assignment = $course->assignments()->create($validated);

        AdminAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action' => 'assignment_created',
            'subject_type' => Assignment::class,
            'subject_id' => $assignment->id,
            'reason' => "Assignment '{$assignment->title}' created for course '{$course->title}'",
        ]);

        return redirect()->route('admin.courses.assignments.index', $course)->with('success', 'Assignment created successfully.');
    }

    public function show(Course $course, Assignment $assignment)
    {
        $submissions = $assignment->submissions()->with('user')->latest('submitted_at')->paginate(20);
        return view('admin.assignments.show', compact('course', 'assignment', 'submissions'));
    }

    public function edit(Course $course, Assignment $assignment)
    {
        $lessons = $course->lessons()->orderBy('sort_order')->get();
        return view('admin.assignments.edit', compact('course', 'assignment', 'lessons'));
    }

    public function update(Request $request, Course $course, Assignment $assignment)
    {
        $validated = $request->validate([
            'lesson_id' => ['nullable', 'exists:lessons,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'total_marks' => ['required', 'integer', 'min:5', 'max:500'],
            'due_date' => ['nullable', 'date'],
            'is_published' => ['boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published', true);
        $assignment->update($validated);

        return redirect()->route('admin.courses.assignments.index', $course)->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('admin.courses.assignments.index', $course)->with('success', 'Assignment deleted.');
    }

    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'marks_obtained' => ['required', 'integer', 'min:0', 'max:' . $assignment->total_marks],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'marks_obtained' => $validated['marks_obtained'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        AdminAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action' => 'assignment_graded',
            'subject_type' => AssignmentSubmission::class,
            'subject_id' => $submission->id,
            'reason' => "Graded student '{$submission->user->name}' with {$validated['marks_obtained']}/{$assignment->total_marks} marks",
        ]);

        return back()->with('success', 'Submission graded successfully.');
    }
}
