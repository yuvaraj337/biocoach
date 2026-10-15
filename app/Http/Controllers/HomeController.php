<?php

namespace App\Http\Controllers;

use App\Models\Course;

class HomeController extends Controller
{
    public function index()
    {
        $courses = Course::with('category')->where('is_published', true)->latest()->take(6)->get();
        return view('home', compact('courses'));
    }
}
