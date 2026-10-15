<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseAdminController extends Controller
{
    public function index()
    {
        return view('admin.courses.index', ['courses' => Course::with('category')->latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.courses.form', ['course' => new Course(), 'categories' => Category::where('is_active', true)->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price_rupees' => ['required', 'numeric', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        $data['price_paise'] = (int) round(((float) $data['price_rupees']) * 100);
        unset($data['price_rupees']);
        $data['is_published'] = $request->boolean('is_published');
        Course::create($data);
        return redirect()->route('admin.courses.index')->with('status', 'Course created.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.form', ['course' => $course, 'categories' => Category::where('is_active', true)->get()]);
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price_rupees' => ['required', 'numeric', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['slug'] = Str::slug($data['title']);
        $data['price_paise'] = (int) round(((float) $data['price_rupees']) * 100);
        unset($data['price_rupees']);
        $data['is_published'] = $request->boolean('is_published');
        $course->update($data);
        return redirect()->route('admin.courses.index')->with('status', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('status', 'Course deleted.');
    }
}
