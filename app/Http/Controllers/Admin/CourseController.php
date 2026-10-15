<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('category')->latest()->paginate(15);
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['price_paise'] = (int) round(((float) $data['price_rupees']) * 100);
        $data['is_published'] = $request->boolean('is_published');
        unset($data['price_rupees']);

        Course::create($data);
        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $this->validatedData($request, $course);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title'], $course->id);
        $data['price_paise'] = (int) round(((float) $data['price_rupees']) * 100);
        $data['is_published'] = $request->boolean('is_published');
        unset($data['price_rupees']);

        $course->update($data);
        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->orders()->exists()) {
            return back()->withErrors(['course' => 'This course has orders and cannot be deleted. Unpublish it instead.']);
        }

        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted.');
    }

    private function validatedData(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'alpha_dash', Rule::unique('courses', 'slug')->ignore($course?->id)],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:50000'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'price_rupees' => ['required', 'numeric', 'min:0', 'max:10000000'],
            'seo_title' => ['nullable', 'string', 'max:190'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'course';
        $slug = $base;
        $counter = 2;

        while (Course::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
