<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CATEGORIES ===" . PHP_EOL;
foreach (\App\Models\Category::all() as $cat) {
    echo "ID: {$cat->id} | Name: {$cat->name} | Slug: {$cat->slug}" . PHP_EOL;
}

echo PHP_EOL . "=== COURSES ===" . PHP_EOL;
foreach (\App\Models\Course::with('category')->get() as $course) {
    echo "ID: {$course->id} | Title: {$course->title} | Slug: {$course->slug} | Price: " . ($course->price_paise / 100) . " | Category: " . ($course->category?->name ?? 'None') . " | Thumbnail: {$course->thumbnail} | Level: {$course->level}" . PHP_EOL;
}
