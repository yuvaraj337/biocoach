<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LiveClass;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->query('period', 30);
        $periodStart = now()->subDays($days);

        $stats = [
            'total_students' => User::where('role', 'student')->orWhereNull('role')->count(),
            'active_students' => Enrollment::where('status', 'active')->distinct('user_id')->count('user_id'),
            'total_courses' => Course::count(),
            'published_courses' => Course::where('is_published', true)->count(),
            'total_lessons' => Lesson::where('is_published', true)->count(),
            'paid_enrollments' => Enrollment::where('status', 'active')->count(),
            'pending_verifications' => Order::where('payment_status', 'verification_pending')->count(),
            'total_revenue_bdt' => Order::where('status', 'paid')->sum('amount_paise') / 100,
            'period_revenue_bdt' => Order::where('status', 'paid')->where('created_at', '>=', $periodStart)->sum('amount_paise') / 100,
            'period_orders_count' => Order::where('created_at', '>=', $periodStart)->count(),
        ];

        $recentStudents = User::where('role', 'student')->orWhereNull('role')->latest()->take(5)->get();
        $recentOrders = Order::with(['user', 'course'])->latest()->take(6)->get();
        $pendingOrders = Order::with(['user', 'course'])->where('payment_status', 'verification_pending')->latest()->take(5)->get();
        $upcomingLiveClasses = LiveClass::with(['course', 'teacher'])->where('start_time', '>=', now()->subHours(1))->orderBy('start_time')->take(4)->get();
        $recentAuditLogs = AdminAuditLog::with('admin')->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentStudents',
            'recentOrders',
            'pendingOrders',
            'upcomingLiveClasses',
            'recentAuditLogs',
            'days'
        ));
    }
}
