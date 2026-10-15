<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonResourceController extends Controller
{
    public function store(Request $request, Course $course, Lesson $lesson) { abort_unless($lesson->course_id===$course->id,404); $data=$request->validate(['title'=>['required','string','max:190'],'file'=>['required','file','mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,jpg,jpeg,png','max:20480']]); $file=$request->file('file'); $path=$file->store('lesson-resources/'.$course->id.'/'.$lesson->id,'local'); $lesson->resources()->create(['title'=>$data['title'],'file_path'=>$path,'original_name'=>$file->getClientOriginalName(),'mime_type'=>$file->getMimeType(),'file_size'=>$file->getSize()]); return back()->with('success','Downloadable resource uploaded to private storage.'); }
    public function destroy(Course $course, Lesson $lesson, LessonResource $resource) { abort_unless($lesson->course_id===$course->id && $resource->lesson_id===$lesson->id,404); Storage::disk('local')->delete($resource->file_path); $resource->delete(); return back()->with('success','Resource deleted.'); }
}
