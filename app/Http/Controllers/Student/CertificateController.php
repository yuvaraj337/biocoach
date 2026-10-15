<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class CertificateController extends Controller {
 public function index(Request $request){ $certificates=$request->user()->certificates()->with('course')->latest('issued_at')->get(); return view('student.certificates',compact('certificates')); }
 public function issue(Request $request, Course $course){
  $user=$request->user(); $enrollment=$user->enrollments()->where('course_id',$course->id)->where('status','active')->firstOrFail();
  $ids=$course->lessons()->where('is_published',true)->pluck('id'); abort_if($ids->isEmpty(),422,'This course has no published lessons yet.');
  $done=$user->lessonProgress()->whereNotNull('completed_at')->whereIn('lesson_id',$ids)->count(); abort_unless($done >= $ids->count(),422,'Complete all published lessons before requesting your certificate.');
  $certificate=Certificate::firstOrCreate(['user_id'=>$user->id,'course_id'=>$course->id],['enrollment_id'=>$enrollment->id,'certificate_number'=>'JC-'.now()->format('Y').'-'.strtoupper(Str::random(10)),'issued_at'=>now()]);
  return redirect()->route('student.certificates.show',$certificate);
 }
 public function show(Request $request, Certificate $certificate){ abort_unless($certificate->user_id===$request->user()->id,404); $certificate->load(['user','course']); return view('student.certificate',compact('certificate')); }
 public function verify(string $number){ $certificate=Certificate::with(['user:id,name','course:id,title'])->where('certificate_number',$number)->firstOrFail(); return view('certificates.verify',compact('certificate')); }
}
