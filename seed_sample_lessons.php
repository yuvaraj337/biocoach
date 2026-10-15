<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use App\Models\Lesson;

$courses = [
    'class-12-biology-full-course' => [
        ['title' => 'Principles of Inheritance and Variation', 'slug' => 'principles-of-inheritance', 'summary' => 'Mendel laws of inheritance, chromosomal theory, sex determination.'],
        ['title' => 'Molecular Basis of Inheritance', 'slug' => 'molecular-basis-inheritance', 'summary' => 'DNA packaging, replication, transcription, and genetic code.'],
    ],
    'class-11-biology-full-course' => [
        ['title' => 'The Living World & Biological Classification', 'slug' => 'the-living-world', 'summary' => 'Taxonomic categories, five kingdom classification, virus and viroids.'],
        ['title' => 'Plant Kingdom & Plant Morphology', 'slug' => 'plant-kingdom-morphology', 'summary' => 'Algae, bryophytes, pteridophytes, gymnosperms, angiosperms.'],
    ],
    'biology-test-series' => [
        ['title' => 'NEET Full Syllabus Mock Exam 1 & Video Analysis', 'slug' => 'neet-mock-exam-1', 'summary' => '90 high-yield questions with line-by-line NCERT references.'],
        ['title' => 'Cell Biology & Genetics Unit Speed Test', 'slug' => 'cell-genetics-speed-test', 'summary' => 'Timed test to improve accuracy and eliminate negative marking.'],
    ],
    'genetics-special-module' => [
        ['title' => 'Mendelian Genetics & Pedigree Analysis Mastery', 'slug' => 'pedigree-analysis-mastery', 'summary' => 'Techniques to solve complex autosomal and sex-linked pedigrees in under 60 seconds.'],
        ['title' => 'Gene Regulation: The Lac Operon Mechanism', 'slug' => 'lac-operon-mechanism', 'summary' => 'Jacob and Monod model of inducible and repressible operons.'],
    ],
    'human-physiology' => [
        ['title' => 'Neural Control & Central Nervous System', 'slug' => 'neural-control-cns', 'summary' => 'Action potential generation, synaptic transmission, reflex arc.'],
        ['title' => 'Chemical Coordination: Endocrine Masterclass', 'slug' => 'chemical-coordination-endocrine', 'summary' => 'Pituitary, thyroid, adrenal hormones and feedback loops.'],
    ],
];

foreach ($courses as $cSlug => $lessons) {
    $c = Course::where('slug', $cSlug)->first();
    if ($c) {
        foreach ($lessons as $i => $lData) {
            Lesson::firstOrCreate([
                'course_id' => $c->id,
                'slug' => $lData['slug'],
            ], [
                'title' => $lData['title'],
                'summary' => $lData['summary'],
                'video_url' => 'https://www.youtube.com/watch?v=URUJD5NEXC8',
                'content' => "## {$lData['title']}\n\nComprehensive conceptual overview and high-yield notes prepared by Dr. Tanjina Jerin.",
                'sort_order' => $i + 1,
                'is_preview' => $i === 0,
                'is_published' => true,
            ]);
        }
    }
}

echo "Added sample curriculum lessons to all courses successfully!" . PHP_EOL;
