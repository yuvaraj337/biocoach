<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->withCount(['enrollments', 'orders'])->latest();

        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $query->where(function ($sub) use ($term) {
                $sub->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%');
            });
        }

        $students = $query->paginate(25)->withQueryString();
        return view('admin.students.index', compact('students'));
    }

    public function show(User $student)
    {
        $student->load([
            'enrollments.course',
            'orders.course',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function updateEnrollment(Request $request, User $student, Enrollment $enrollment)
    {
        abort_unless($enrollment->user_id === $student->id, 404);

        $data = $request->validate([
            'status' => ['required', 'in:active,revoked'],
            'reason' => ['required', 'string', 'min:8', 'max:500'],
        ]);

        $before = ['status' => $enrollment->status];
        DB::transaction(function () use ($request, $student, $enrollment, $data, $before) {
            $enrollment->update(['status' => $data['status']]);

            AdminAuditLog::create([
                'admin_user_id' => $request->user()->id,
                'action' => 'enrollment.status_changed',
                'subject_type' => Enrollment::class,
                'subject_id' => $enrollment->id,
                'reason' => $data['reason'],
                'before_json' => $before,
                'after_json' => ['status' => $data['status'], 'student_id' => $student->id, 'course_id' => $enrollment->course_id],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('admin.students.show', $student)->with('success', 'Enrolment status updated and recorded in the audit log.');
    }
}
