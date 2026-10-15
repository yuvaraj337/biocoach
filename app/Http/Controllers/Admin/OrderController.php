<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\AdminAuditLog;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user','course'])->latest();
        if ($request->filled('status') && in_array($request->status, ['pending','verification_pending','paid','failed','cancelled'], true)) {
            $query->where('payment_status',$request->status);
        }
        if ($request->filled('q')) {
            $term = trim((string)$request->q);
            $query->where(function($sub) use ($term) {
                $sub->where('order_number','like','%'.$term.'%')
                    ->orWhereHas('user',fn($q)=>$q->where('name','like','%'.$term.'%')->orWhere('email','like','%'.$term.'%'))
                    ->orWhereHas('course',fn($q)=>$q->where('title','like','%'.$term.'%'));
            });
        }
        $orders = $query->paginate(20)->withQueryString();
        $stats = [
            'all'=>Order::count(),
            'pending'=>Order::whereIn('payment_status',['pending','verification_pending'])->count(),
            'paid'=>Order::where('payment_status','paid')->count(),
            'revenue_paise'=>(int)Order::where('payment_status','paid')->sum('amount_paise'),
        ];
        return view('admin.orders.index',compact('orders','stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user','course','paymentAttempts']);
        return view('admin.orders.show',compact('order'));
    }

    public function markFailed(Request $request, Order $order)
    {
        $data = $request->validate(['admin_note'=>['required','string','min:8','max:500']]);
        if ($order->payment_status === 'paid') {
            return back()->withErrors(['order'=>'Paid orders cannot be marked failed here. Use a dedicated refund workflow.']);
        }
        $before = ['status' => $order->status, 'payment_status' => $order->payment_status];
        $order->update(['status'=>'failed','payment_status'=>'failed']);
        $order->paymentAttempts()->whereIn('status',['created','signature_verified'])->update(['status'=>'failed_reviewed']);
        AdminAuditLog::create([
            'admin_user_id' => $request->user()->id,
            'action' => 'order.marked_failed',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'reason' => $data['admin_note'],
            'before_json' => $before,
            'after_json' => ['status' => $order->status, 'payment_status' => $order->payment_status],
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('admin.orders.show',$order)->with('success','Order marked failed and recorded in audit log.');
    }

    public function revokeEnrollment(Request $request, Order $order)
    {
        $data = $request->validate(['admin_note'=>['required','string','min:8','max:500']]);
        $enrollments = Enrollment::where('user_id',$order->user_id)->where('course_id',$order->course_id)->get();
        $before = $enrollments->map(fn ($item) => ['enrollment_id' => $item->id, 'status' => $item->status])->all();
        Enrollment::where('user_id',$order->user_id)->where('course_id',$order->course_id)->update(['status'=>'revoked']);
        AdminAuditLog::create([
            'admin_user_id' => $request->user()->id,
            'action' => 'enrollment.revoked_from_order',
            'subject_type' => Order::class,
            'subject_id' => $order->id,
            'reason' => $data['admin_note'],
            'before_json' => ['enrollments' => $before],
            'after_json' => ['status' => 'revoked'],
            'ip_address' => $request->ip(),
        ]);
        return redirect()->route('admin.orders.show',$order)->with('success','Enrollment access revoked and recorded in audit log.');
    }
}
