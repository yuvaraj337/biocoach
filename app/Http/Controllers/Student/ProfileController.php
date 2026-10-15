<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class ProfileController extends Controller {
 public function edit(Request $request){ return view('student.profile',['user'=>$request->user()]); }
 public function update(Request $request){
  $user=$request->user(); $data=$request->validate(['name'=>['required','string','max:120'],'email'=>['required','email','max:255',Rule::unique('users','email')->ignore($user->id)],'current_password'=>['nullable','current_password'],'password'=>['nullable','confirmed',Password::min(10)->letters()->numbers()]]);
  if(!empty($data['password']) && empty($data['current_password'])) return back()->withErrors(['current_password'=>'Enter your current password to change it.'])->withInput();
  $user->name=$data['name']; $user->email=$data['email']; if(!empty($data['password'])) $user->password=$data['password']; $user->save(); return back()->with('success','Profile updated successfully.');
 }
}
