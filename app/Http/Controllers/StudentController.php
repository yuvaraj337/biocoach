<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function courses(Request $request)
    {
        $enrollments = $request->user()->enrollments()
            ->with('course')
            ->where('status', 'active')
            ->latest()
            ->get();

        $pendingOrders = $request->user()->orders()
            ->with('course')
            ->whereIn('payment_status', ['pending', 'verification_pending', 'rejected'])
            ->latest()
            ->get();

        return view('student.courses', compact('enrollments', 'pendingOrders'));
    }

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with('course')->latest()->paginate(15);
        return view('student.orders', compact('orders'));
    }

    public function liveClasses(Request $request)
    {
        $enrolledCourseIds = $request->user()->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        $upcomingClasses = \App\Models\LiveClass::with(['course', 'teacher'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '>=', now()->subHours(2))
            ->orderBy('start_time')
            ->get();

        $pastClasses = \App\Models\LiveClass::with(['course', 'teacher'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('start_time', '<', now()->subHours(2))
            ->latest('start_time')
            ->take(10)
            ->get();

        return view('student.live_classes', compact('upcomingClasses', 'pastClasses'));
    }

    public function assignments(Request $request, \App\Models\Course $course)
    {
        $isEnrolled = $request->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();
        abort_unless($isEnrolled, 403, 'Enrolled students only.');

        $assignments = $course->assignments()
            ->with(['submissions' => fn($q) => $q->where('user_id', $request->user()->id)])
            ->where('is_published', true)
            ->latest()
            ->get();

        return view('student.assignments.index', compact('course', 'assignments'));
    }

    public function showAssignment(Request $request, \App\Models\Course $course, \App\Models\Assignment $assignment)
    {
        $isEnrolled = $request->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();
        abort_unless($isEnrolled, 403, 'Enrolled students only.');

        $submission = $assignment->submissions()->where('user_id', $request->user()->id)->first();
        return view('student.assignments.show', compact('course', 'assignment', 'submission'));
    }

    public function submitAssignment(Request $request, \App\Models\Course $course, \App\Models\Assignment $assignment)
    {
        $isEnrolled = $request->user()->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->exists();
        abort_unless($isEnrolled, 403, 'Enrolled students only.');

        $validated = $request->validate([
            'submission_text' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,png,zip', 'max:10240'], // 10MB
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('assignments', 'local');
        }

        \App\Models\AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $request->user()->id,
            ],
            [
                'submission_text' => $validated['submission_text'] ?? null,
                'attachment_path' => $path ?? ($assignment->userSubmission()?->attachment_path),
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );

        return redirect()->route('student.assignments.show', [$course, $assignment])
            ->with('success', 'Assignment submitted successfully for review!');
    }
}
