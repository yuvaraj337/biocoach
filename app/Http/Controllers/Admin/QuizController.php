<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class QuizController extends Controller
{
    public function index(Course $course) { $quizzes=$course->quizzes()->withCount('questions')->withCount('attempts')->latest()->paginate(15); return view('admin.quizzes.index',compact('course','quizzes')); }
    public function create(Course $course) { $quiz=new Quiz(['duration_minutes'=>20,'passing_percentage'=>50,'attempts_allowed'=>3]); return view('admin.quizzes.form',compact('course','quiz')); }
    public function store(Request $request, Course $course) { $data=$this->validated($request,$course); $data['course_id']=$course->id; $data['slug']=$this->uniqueSlug($course,$data['slug'] ?: $data['title']); $data['is_published']=$request->boolean('is_published'); $quiz=Quiz::create($data); return redirect()->route('admin.courses.quizzes.edit',[$course,$quiz])->with('success','Quiz created. Add questions below.'); }
    public function edit(Course $course, Quiz $quiz) { abort_unless($quiz->course_id===$course->id,404); $quiz->load('questions'); return view('admin.quizzes.form',compact('course','quiz')); }
    public function update(Request $request, Course $course, Quiz $quiz) { abort_unless($quiz->course_id===$course->id,404); $data=$this->validated($request,$course,$quiz); $data['slug']=$this->uniqueSlug($course,$data['slug'] ?: $data['title'],$quiz); $data['is_published']=$request->boolean('is_published'); $quiz->update($data); return back()->with('success','Quiz settings updated.'); }
    public function destroy(Course $course, Quiz $quiz) { abort_unless($quiz->course_id===$course->id,404); $quiz->delete(); return redirect()->route('admin.courses.quizzes.index',$course)->with('success','Quiz deleted.'); }
    public function storeQuestion(Request $request, Course $course, Quiz $quiz) { abort_unless($quiz->course_id===$course->id,404); $data=$request->validate(['prompt'=>'required|string|max:5000','option_a'=>'required|string|max:500','option_b'=>'required|string|max:500','option_c'=>'required|string|max:500','option_d'=>'required|string|max:500','correct_option'=>'required|integer|between:0,3','points'=>'required|integer|between:1,100','sort_order'=>'nullable|integer|min:0|max:100000']); QuizQuestion::create(['quiz_id'=>$quiz->id,'prompt'=>$data['prompt'],'options'=>[$data['option_a'],$data['option_b'],$data['option_c'],$data['option_d']],'correct_option'=>(int)$data['correct_option'],'points'=>(int)$data['points'],'sort_order'=>$data['sort_order']??0]); return back()->with('success','Question added.'); }
    public function destroyQuestion(Course $course, Quiz $quiz, QuizQuestion $question) { abort_unless($quiz->course_id===$course->id && $question->quiz_id===$quiz->id,404); $question->delete(); return back()->with('success','Question deleted.'); }
    private function validated(Request $request, Course $course, ?Quiz $quiz=null): array { return $request->validate(['title'=>'required|string|max:190','slug'=>['nullable','string','max:190','alpha_dash',Rule::unique('quizzes','slug')->where('course_id',$course->id)->ignore($quiz?->id)],'description'=>'nullable|string|max:5000','duration_minutes'=>'required|integer|between:1,240','passing_percentage'=>'required|integer|between:1,100','attempts_allowed'=>'required|integer|between:1,20','is_published'=>'nullable|boolean']); }
    private function uniqueSlug(Course $course,string $value,?Quiz $quiz=null): string { $base=Str::slug($value) ?: 'quiz'; $slug=$base; $i=2; while(Quiz::where('course_id',$course->id)->where('slug',$slug)->when($quiz,fn($q)=>$q->where('id','!=',$quiz->id))->exists()) $slug=$base.'-'.$i++; return $slug; }
}
