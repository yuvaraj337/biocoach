<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use App\Models\Course;

// 1. Categories from Reference Image 2
$catNeet = Category::updateOrCreate(['slug' => 'neet-biology'], [
    'name' => 'NEET Biology',
    'description' => 'Comprehensive NCERT and competitive exam prep.',
    'is_active' => true,
]);

$cat11 = Category::updateOrCreate(['slug' => 'class-11-biology'], [
    'name' => 'Class 11 Biology',
    'description' => 'NCERT based core concepts and foundations.',
    'is_active' => true,
]);

$cat12 = Category::updateOrCreate(['slug' => 'class-12-biology'], [
    'name' => 'Class 12 Biology',
    'description' => 'Board exam and CBSE curriculum focus.',
    'is_active' => true,
]);

$catFoundation = Category::updateOrCreate(['slug' => 'foundation-courses'], [
    'name' => 'Foundation Courses',
    'description' => 'Fundamental biology modules for school and pre-medical students.',
    'is_active' => true,
]);

$catCrash = Category::updateOrCreate(['slug' => 'crash-courses'], [
    'name' => 'Crash Courses',
    'description' => 'Fast-track high-yield revision courses.',
    'is_active' => true,
]);

$catTestSeries = Category::updateOrCreate(['slug' => 'test-series'], [
    'name' => 'Test Series',
    'description' => 'Chapter-wise and full-length NEET mock tests.',
    'is_active' => true,
]);

// 2. Courses from Reference Image 2
// Course 1: NEET Biology Complete Course
Course::updateOrCreate(['slug' => 'neet-biology-complete-course'], [
    'category_id' => $catNeet->id,
    'title' => 'NEET Biology Complete Course',
    'summary' => 'Comprehensive NEET Biology preparation with 80+ structured lessons, interactive diagrams, and chapter-wise tests.',
    'description' => "Welcome to the NEET Biology Complete Course at Jerin’s Coaching!\n\nThis course is meticulously designed for medical aspirants aiming for a 340+ score in NEET Biology. Dr. Tanjina Jerin guides you through every single concept in Botany and Zoology with clarity, memory techniques, and high-yield questions.\n\nIncluded Features:\n• 80+ Video & Conceptual Lessons\n• NCERT Line-by-Line Breakdown\n• Chapter Quizzes & Practice Tests\n• Certificate of Completion upon finishing all lessons\n• Private Doubt Clearance Support",
    'price_paise' => 299900,
    'thumbnail' => 'images/courses/neet-biology.jpg',
    'level' => 'advanced',
    'rating' => 4.9,
    'review_count' => 600,
    'is_published' => true,
    'seo_title' => 'NEET Biology Complete Course | Jerin’s Coaching',
    'seo_description' => 'Master NEET Biology with Dr. Tanjina Jerin. 80+ lessons, NCERT notes, tests.',
]);

// Course 2: Class 12 Biology Full Course
Course::updateOrCreate(['slug' => 'class-12-biology-full-course'], [
    'category_id' => $cat12->id,
    'title' => 'Class 12 Biology Full Course',
    'summary' => 'Complete coverage of CBSE Class 12 Biology syllabus for Board Exams.',
    'description' => "Designed for CBSE and State Board Class 12 students. Master Genetics, Evolution, Biotechnology, and Ecology with structured study resources.",
    'price_paise' => 149900,
    'thumbnail' => 'images/courses/class-12-biology.jpg',
    'level' => 'intermediate',
    'rating' => 4.8,
    'review_count' => 300,
    'is_published' => true,
    'seo_title' => 'Class 12 Biology Full Course | Jerin’s Coaching',
    'seo_description' => 'Excel in your Class 12 Biology board exams with structured guidance.',
]);

// Course 3: Class 11 Biology Full Course
Course::updateOrCreate(['slug' => 'class-11-biology-full-course'], [
    'category_id' => $cat11->id,
    'title' => 'Class 11 Biology Full Course',
    'summary' => 'Build rock-solid foundations in Cell Biology, Plant Physiology, and Diversity of Living Organisms.',
    'description' => "Class 11 forms 50% of the NEET Biology syllabus. Gain strong conceptual clarity so you never feel intimidated by complex topics.",
    'price_paise' => 149900,
    'thumbnail' => 'images/courses/class-11-biology.jpg',
    'level' => 'beginner',
    'rating' => 4.6,
    'review_count' => 300,
    'is_published' => true,
    'seo_title' => 'Class 11 Biology Full Course | Jerin’s Coaching',
    'seo_description' => 'NCERT based Class 11 Biology conceptual learning.',
]);

// Course 4: Biology Test Series
Course::updateOrCreate(['slug' => 'biology-test-series'], [
    'category_id' => $catTestSeries->id,
    'title' => 'Biology Test Series',
    'summary' => 'Comprehensive mock exam series with chapter tests, NCERT line-by-line questions, and complete full syllabus tests with detailed video solutions.',
    'description' => "Test your real NEET exam readiness with Jerin’s Coaching Biology Test Series. Over 2,500 curated questions designed to eliminate negative marking and build speed.",
    'price_paise' => 99900,
    'thumbnail' => 'images/courses/biology-test-series.jpg',
    'level' => 'intermediate',
    'rating' => 4.7,
    'review_count' => 100,
    'is_published' => true,
    'seo_title' => 'Biology Test Series | Jerin’s Coaching',
    'seo_description' => 'Complete biology test series with mock papers and instant scoring.',
]);

// Course 5: Genetics Special Module
Course::updateOrCreate(['slug' => 'genetics-special-module'], [
    'category_id' => $catCrash->id,
    'title' => 'Genetics Special Module',
    'summary' => 'Master Mendelian genetics, dihybrid crosses, chromosomal disorders, and molecular genetics without fear.',
    'description' => "Genetics is one of the highest weighted units in NEET and 12th Board examinations. Dr. Tanjina Jerin breaks down pedigrees, linkage, replication, and transcription with easy visual frameworks.",
    'price_paise' => 79900,
    'thumbnail' => 'images/courses/genetics-module.jpg',
    'level' => 'advanced',
    'rating' => 4.8,
    'review_count' => 100,
    'is_published' => true,
    'seo_title' => 'Genetics Special Module | Jerin’s Coaching',
    'seo_description' => 'Specialized module in Mendelian and molecular genetics.',
]);

// Course 6: Human Physiology
Course::updateOrCreate(['slug' => 'human-physiology'], [
    'category_id' => $catFoundation->id,
    'title' => 'Human Physiology',
    'summary' => 'In-depth focus on circulatory, respiratory, nervous, excretory, and digestive organ systems.',
    'description' => "Master the most scoring and intricate unit in Biology: Human Physiology. Learn bodily mechanisms with clear 3D anatomical illustrations and clinical insights.",
    'price_paise' => 89900,
    'thumbnail' => 'images/courses/human-physiology.jpg',
    'level' => 'intermediate',
    'rating' => 4.6,
    'review_count' => 100,
    'is_published' => true,
    'seo_title' => 'Human Physiology | Jerin’s Coaching',
    'seo_description' => 'Understand human body mechanisms with Dr. Jerin.',
]);

// Also update legacy slug if existed
if ($oldPhys = Course::where('slug', 'human-physiology-mastery')->first()) {
    $oldPhys->update(['is_published' => false]);
}

echo "Seeded 6 Image 2 courses and categories successfully!" . PHP_EOL;
