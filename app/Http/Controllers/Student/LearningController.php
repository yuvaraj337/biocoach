<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningController extends Controller
{
    public function show(Request $request, Course $course, Lesson $lesson) { abort_unless($lesson->course_id===$course->id && $lesson->is_published,404); $this->authorizeAccess($request,$course,$lesson); $lessons=$course->lessons()->where('is_published',true)->with(['progress'=>fn($q)=>$q->where('user_id',$request->user()->id),'resources'])->get(); $lesson->load('resources'); $completedIds=$request->user()->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id')->all(); return view('student.lesson',compact('course','lesson','lessons','completedIds')); }
    public function complete(Request $request, Course $course, Lesson $lesson) { abort_unless($lesson->course_id===$course->id && $lesson->is_published,404); $this->authorizeAccess($request,$course,$lesson); LessonProgress::updateOrCreate(['user_id'=>$request->user()->id,'lesson_id'=>$lesson->id],['completed_at'=>now()]); return back()->with('success','Lesson marked as complete.'); }
    public function download(Request $request, Course $course, Lesson $lesson, LessonResource $resource) { abort_unless($lesson->course_id===$course->id && $resource->lesson_id===$lesson->id && $lesson->is_published,404); $this->authorizeAccess($request,$course,$lesson); abort_unless(Storage::disk('local')->exists($resource->file_path),404); return Storage::disk('local')->download($resource->file_path,$resource->original_name); }
    private function authorizeAccess(Request $request, Course $course, Lesson $lesson): void { if($lesson->is_preview) return; $active=$request->user()->enrollments()->where('course_id',$course->id)->where('status','active')->exists(); abort_unless($active,403,'Purchase this course to access this lesson.'); }
}
