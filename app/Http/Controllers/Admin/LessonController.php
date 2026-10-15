<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LessonController extends Controller
{
    public function index(Course $course) { $lessons=$course->lessons()->withCount('resources')->paginate(20); return view('admin.lessons.index',compact('course','lessons')); }
    public function create(Course $course) { $lesson=new Lesson(); return view('admin.lessons.form',compact('course','lesson')); }
    public function store(Request $request, Course $course) { $data=$this->validated($request,$course); $data['course_id']=$course->id; $data['slug']=$this->slug($data['slug'] ?: $data['title'],$course); $data['is_preview']=$request->boolean('is_preview'); $data['is_published']=$request->boolean('is_published'); $lesson=Lesson::create($data); return redirect()->route('admin.courses.lessons.edit',[$course,$lesson])->with('success','Lesson created. Add notes or downloadable resources below.'); }
    public function edit(Course $course, Lesson $lesson) { abort_unless($lesson->course_id===$course->id,404); $lesson->load('resources'); return view('admin.lessons.form',compact('course','lesson')); }
    public function update(Request $request, Course $course, Lesson $lesson) { abort_unless($lesson->course_id===$course->id,404); $data=$this->validated($request,$course,$lesson); $data['slug']=$this->slug($data['slug'] ?: $data['title'],$course,$lesson); $data['is_preview']=$request->boolean('is_preview'); $data['is_published']=$request->boolean('is_published'); $lesson->update($data); return back()->with('success','Lesson updated.'); }
    public function destroy(Course $course, Lesson $lesson) { abort_unless($lesson->course_id===$course->id,404); foreach($lesson->resources as $resource){ \Illuminate\Support\Facades\Storage::disk('local')->delete($resource->file_path); } $lesson->delete(); return redirect()->route('admin.courses.lessons.index',$course)->with('success','Lesson deleted.'); }
    private function validated(Request $request, Course $course, ?Lesson $lesson=null): array { return $request->validate(['title'=>['required','string','max:190'],'slug'=>['nullable','string','max:190','alpha_dash',Rule::unique('lessons','slug')->where('course_id',$course->id)->ignore($lesson?->id)],'summary'=>['nullable','string','max:500'],'video_url'=>['nullable','url','max:1000','regex:/^https:\/\/(www\.)?(youtube\.com|youtu\.be|vimeo\.com)\//i'],'content'=>['nullable','string','max:100000'],'sort_order'=>['nullable','integer','min:0','max:100000'],'is_preview'=>['nullable','boolean'],'is_published'=>['nullable','boolean']]); }
    private function slug(string $value, Course $course, ?Lesson $lesson=null): string { $base=Str::slug($value) ?: 'lesson'; $slug=$base; $i=2; while(Lesson::where('course_id',$course->id)->where('slug',$slug)->when($lesson,fn($q)=>$q->where('id','!=',$lesson->id))->exists()) $slug=$base.'-'.$i++; return $slug; }
}
