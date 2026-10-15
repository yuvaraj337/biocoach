<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('category')->where('is_published', true);

        // Category filter
        $selectedCategory = $request->query('category');
        if ($selectedCategory && $selectedCategory !== 'all') {
            $query->whereHas('category', function ($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory);
            });
        }

        // Search filter (title, summary, description, or category name)
        if ($request->filled('q')) {
            $term = trim((string)$request->query('q'));
            $query->where(function ($sub) use ($term) {
                $sub->where('title', 'like', "%{$term}%")
                    ->orWhere('summary', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('category', fn ($cat) => $cat->where('name', 'like', "%{$term}%"));
            });
        }

        // Price range filter (in Rupees)
        $minPrice = $request->filled('min_price') ? max(0, (int)$request->query('min_price')) : 0;
        $maxPrice = $request->filled('max_price') ? max(0, (int)$request->query('max_price')) : 25000;
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereBetween('price_paise', [$minPrice * 100, $maxPrice * 100]);
        }

        // Level filter (Beginner, Intermediate, Advanced)
        $selectedLevels = (array)$request->query('levels', []);
        $selectedLevels = array_values(array_filter($selectedLevels));
        if (!empty($selectedLevels)) {
            $query->whereIn('level', $selectedLevels);
        }

        // Sorting
        $sort = $request->query('sort', 'popularity');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price_paise', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_paise', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'popularity':
            default:
                $query->orderByRaw("CASE slug 
                    WHEN 'neet-biology-complete-course' THEN 1 
                    WHEN 'class-12-biology-full-course' THEN 2 
                    WHEN 'class-11-biology-full-course' THEN 3 
                    WHEN 'biology-test-series' THEN 4 
                    WHEN 'genetics-special-module' THEN 5 
                    WHEN 'human-physiology' THEN 6 
                    ELSE 99 END ASC")
                    ->orderByDesc('review_count')
                    ->orderByDesc('rating');
                break;
        }

        $courses = $query->paginate(12)->withQueryString();

        // Preset categories ordered matching Reference Image 2
        $orderedSlugs = [
            'neet-biology',
            'class-11-biology',
            'class-12-biology',
            'foundation-courses',
            'crash-courses',
            'test-series'
        ];

        $categories = Category::whereIn('slug', $orderedSlugs)
            ->get()
            ->sortBy(function ($cat) use ($orderedSlugs) {
                return array_search($cat->slug, $orderedSlugs);
            });

        return view('courses.index', compact(
            'courses',
            'categories',
            'selectedCategory',
            'selectedLevels',
            'minPrice',
            'maxPrice',
            'sort'
        ));
    }

    public function show(Course $course)
    {
        abort_unless($course->is_published, 404);
        return view('courses.show', compact('course'));
    }

    public function myCourses()
    {
        return redirect()->route('student.courses');
    }
}
