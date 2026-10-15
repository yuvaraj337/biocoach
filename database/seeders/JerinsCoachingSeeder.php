<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Certificate;

class JerinsCoachingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@jerinscoaching.com'],
            [
                'name' => 'Dr. Tanjina Jerin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_admin' => true,
                'phone' => '+91 98765 43210',
            ]
        );

        // 2. Student User
        $student = User::firstOrCreate(
            ['email' => 'student@jerinscoaching.com'],
            [
                'name' => 'Aarav Sharma',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'is_admin' => false,
                'phone' => '+91 98765 12345',
            ]
        );

        // 3. Categories
        $catNeet = Category::firstOrCreate(['slug' => 'neet-biology'], [
            'name' => 'NEET Biology',
            'description' => 'Comprehensive NCERT and competitive exam prep.',
            'is_active' => true,
        ]);

        $cat12 = Category::firstOrCreate(['slug' => 'class-12-biology'], [
            'name' => 'Class 12 Biology',
            'description' => 'Board exam and CBSE curriculum focus.',
            'is_active' => true,
        ]);

        $cat11 = Category::firstOrCreate(['slug' => 'class-11-biology'], [
            'name' => 'Class 11 Biology',
            'description' => 'NCERT based core concepts and foundations.',
            'is_active' => true,
        ]);

        $catPhys = Category::firstOrCreate(['slug' => 'human-physiology'], [
            'name' => 'Human Physiology',
            'description' => 'Deep dive into human body systems.',
            'is_active' => true,
        ]);

        $catFound = Category::firstOrCreate(['slug' => 'foundation-courses'], [
            'name' => 'Foundation Courses',
            'description' => 'Solid fundamentals for early medical and science aspirants.',
            'is_active' => true,
        ]);

        $catCrash = Category::firstOrCreate(['slug' => 'crash-courses'], [
            'name' => 'Crash Courses',
            'description' => 'Fast-track high-yield revision modules.',
            'is_active' => true,
        ]);

        $catTest = Category::firstOrCreate(['slug' => 'test-series'], [
            'name' => 'Test Series',
            'description' => 'All-India simulated biology mock tests.',
            'is_active' => true,
        ]);

        // 4. Courses (matching Image 2)
        $c1 = Course::updateOrCreate(['slug' => 'neet-biology-complete-course'], [
            'category_id' => $catNeet->id,
            'title' => 'NEET Biology Complete Course',
            'summary' => 'Comprehensive NEET Biology preparation with 80+ structured lessons, interactive diagrams, and chapter-wise tests.',
            'description' => "Welcome to the NEET Biology Complete Course at Jerin’s Coaching!\n\nThis course is meticulously designed for medical aspirants aiming for a 340+ score in NEET Biology. Dr. Tanjina Jerin guides you through every single concept in Botany and Zoology with clarity, memory techniques, and high-yield questions.\n\nIncluded Features:\n• 80+ Video & Conceptual Lessons\n• NCERT Line-by-Line Breakdown\n• Chapter Quizzes & Practice Tests\n• Certificate of Completion upon finishing all lessons\n• Private Doubt Clearance Support",
            'price_paise' => 299900,
            'rating' => 4.9,
            'review_count' => 600,
            'level' => 'Advanced',
            'thumbnail' => 'images/courses/neet-biology.jpg',
            'is_published' => true,
            'seo_title' => 'NEET Biology Complete Course | Jerin’s Coaching',
            'seo_description' => 'Master NEET Biology with Dr. Tanjina Jerin. 80+ lessons, NCERT notes, tests.',
        ]);

        $c2 = Course::updateOrCreate(['slug' => 'class-12-biology-full-course'], [
            'category_id' => $cat12->id,
            'title' => 'Class 12 Biology Full Course',
            'summary' => 'Complete coverage of CBSE Class 12 Biology syllabus for Board Exams.',
            'description' => "Designed for CBSE and State Board Class 12 students. Master Genetics, Evolution, Biotechnology, and Ecology with structured study resources.",
            'price_paise' => 149900,
            'rating' => 4.8,
            'review_count' => 300,
            'level' => 'Intermediate',
            'thumbnail' => 'images/courses/class-12-biology.jpg',
            'is_published' => true,
            'seo_title' => 'Class 12 Biology Full Course | Jerin’s Coaching',
            'seo_description' => 'Excel in your Class 12 Biology board exams with structured guidance.',
        ]);

        $c3 = Course::updateOrCreate(['slug' => 'class-11-biology-full-course'], [
            'category_id' => $cat11->id,
            'title' => 'Class 11 Biology Full Course',
            'summary' => 'Build rock-solid foundations in Cell Biology, Plant Physiology, and Diversity of Living Organisms.',
            'description' => "Class 11 forms 50% of the NEET Biology syllabus. Gain strong conceptual clarity so you never feel intimidated by complex topics.",
            'price_paise' => 149900,
            'rating' => 4.6,
            'review_count' => 300,
            'level' => 'Beginner',
            'thumbnail' => 'images/courses/class-11-biology.jpg',
            'is_published' => true,
            'seo_title' => 'Class 11 Biology Full Course | Jerin’s Coaching',
            'seo_description' => 'NCERT based Class 11 Biology conceptual learning.',
        ]);

        $c4 = Course::updateOrCreate(['slug' => 'biology-test-series'], [
            'category_id' => $catTest->id,
            'title' => 'Biology Test Series',
            'summary' => 'Comprehensive all-India simulated test series with in-depth analytics and answer explanations.',
            'description' => "High-yield chapter-wise and full-syllabus mock tests designed according to the latest NTA NEET examination pattern.\n\nIncludes detailed rank analysis, time management metrics, and solution breakdown for each question.",
            'price_paise' => 99900,
            'rating' => 4.7,
            'review_count' => 100,
            'level' => 'Advanced',
            'thumbnail' => 'images/courses/biology-test-series.jpg',
            'is_published' => true,
            'seo_title' => 'Biology Test Series | Jerin’s Coaching',
            'seo_description' => 'Comprehensive NEET Biology test series with detailed solutions.',
        ]);

        $c5 = Course::updateOrCreate(['slug' => 'genetics-special-module'], [
            'category_id' => $catCrash->id,
            'title' => 'Genetics Special Module',
            'summary' => 'Master Mendelian genetics, molecular basis of inheritance, and pedigree analysis with ease.',
            'description' => "Master one of NEET Biology's highest-weightage topics with Dr. Tanjina Jerin.\n\nIncludes Principles of Inheritance and Variation, Molecular Basis of Inheritance, DNA replication, transcription, translation, and genetic disorders.",
            'price_paise' => 79900,
            'rating' => 4.8,
            'review_count' => 100,
            'level' => 'Intermediate',
            'thumbnail' => 'images/courses/genetics-module.jpg',
            'is_published' => true,
            'seo_title' => 'Genetics Special Module | Jerin’s Coaching',
            'seo_description' => 'High yield genetics and molecular biology crash module.',
        ]);

        $c6 = Course::updateOrCreate(['slug' => 'human-physiology'], [
            'category_id' => $catFound->id,
            'title' => 'Human Physiology',
            'summary' => 'Detailed 3D anatomical walkthroughs of neural control, endocrine systems, circulation, and digestion.',
            'description' => "Visual and concept-first deep dive into human organ systems.\n\nCovers Digestion & Absorption, Breathing & Exchange of Gases, Body Fluids & Circulation, Excretory Products, Locomotion & Movement, Neural Control & Coordination, and Chemical Coordination.",
            'price_paise' => 89900,
            'rating' => 4.6,
            'review_count' => 100,
            'level' => 'Beginner',
            'thumbnail' => 'images/courses/human-physiology.jpg',
            'is_published' => true,
            'seo_title' => 'Human Physiology | Jerin’s Coaching',
            'seo_description' => 'Understand human body mechanisms with Dr. Jerin.',
        ]);

        // Unpublish legacy duplicate if present
        Course::where('slug', 'human-physiology-mastery')->update(['is_published' => false]);

        // 5. Lessons for Course 1
        $l1 = Lesson::firstOrCreate(['course_id' => $c1->id, 'slug' => 'cell-structure-and-organelles'], [
            'title' => 'Cell Structure and Organelles',
            'summary' => 'Explore the architecture of plant and animal cells, membranes, and organelles.',
            'video_url' => 'https://www.youtube.com/watch?v=URUJD5NEXC8',
            'content' => "## Chapter 1: The Cell — Unit of Life\n\nThe cell is the basic structural and functional unit of all living organisms. In this lesson, we explore:\n\n1. Prokaryotic vs. Eukaryotic Cells\n2. Plasma Membrane Structure (Fluid Mosaic Model)\n3. Endomembrane System: ER, Golgi Apparatus, Lysosomes, and Vacuoles\n4. Mitochondria and Chloroplasts as semi-autonomous organelles\n5. Nucleus and Chromosome Morphology",
            'sort_order' => 1,
            'is_preview' => true,
            'is_published' => true,
        ]);

        $l2 = Lesson::firstOrCreate(['course_id' => $c1->id, 'slug' => 'dna-structure-and-replication'], [
            'title' => 'DNA Structure and Semi-Conservative Replication',
            'summary' => 'Watson-Crick double helix, nitrogenous base pairing, and Meselson-Stahl experiment.',
            'video_url' => 'https://www.youtube.com/watch?v=8kK2zwjRV0M',
            'content' => "## Molecular Basis of Inheritance\n\nDNA consists of two polynucleotide chains antiparallel in direction. Key points:\n\n- Purines (Adenine, Guanine) pair with Pyrimidines (Thymine, Cytosine)\n- 10 base pairs per helical turn (3.4 nm pitch)\n- DNA Polymerase III synthesis in 5' to 3' direction\n- Okazaki fragments on the lagging strand joined by DNA ligase",
            'sort_order' => 2,
            'is_preview' => false,
            'is_published' => true,
        ]);

        // 6. Quizzes
        $quiz = Quiz::firstOrCreate(['course_id' => $c1->id, 'slug' => 'cell-biology-mastery-quiz'], [
            'title' => 'Cell Biology Mastery Quiz',
            'description' => 'Test your grasp of cell organelles, membrane transport, and cell division.',
            'duration_minutes' => 15,
            'passing_percentage' => 60,
            'attempts_allowed' => 3,
            'is_published' => true,
        ]);

        QuizQuestion::firstOrCreate(['quiz_id' => $quiz->id, 'prompt' => 'Which organelle contains its own circular DNA and 70S ribosomes?'], [
            'options' => ['Endoplasmic Reticulum', 'Mitochondria', 'Golgi apparatus', 'Lysosome'],
            'correct_option' => 1,
            'points' => 1,
            'sort_order' => 1,
        ]);

        QuizQuestion::firstOrCreate(['quiz_id' => $quiz->id, 'prompt' => 'Who proposed the Fluid Mosaic Model of plasma membrane structure?'], [
            'options' => ['Singer and Nicolson', 'Watson and Crick', 'Robert Brown', 'Rudolf Virchow'],
            'correct_option' => 0,
            'points' => 1,
            'sort_order' => 2,
        ]);

        QuizQuestion::firstOrCreate(['quiz_id' => $quiz->id, 'prompt' => 'Which of the following is NOT part of the endomembrane system?'], [
            'options' => ['Endoplasmic reticulum', 'Golgi apparatus', 'Peroxisome', 'Lysosome'],
            'correct_option' => 2,
            'points' => 1,
            'sort_order' => 3,
        ]);

        // 7. Student enrollment & progress in Course 1
        $order = Order::firstOrCreate(['order_number' => 'ORD-DEMO-1001'], [
            'user_id' => $student->id,
            'course_id' => $c1->id,
            'amount_paise' => $c1->price_paise,
            'status' => 'paid',
            'payment_status' => 'paid',
            'payment_reference' => 'pay_mock_123456',
            'paid_at' => now(),
        ]);

        $enrollment = Enrollment::firstOrCreate(['user_id' => $student->id, 'course_id' => $c1->id], [
            'order_id' => $order->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        LessonProgress::firstOrCreate(['user_id' => $student->id, 'lesson_id' => $l1->id], [
            'completed_at' => now(),
        ]);

        LessonProgress::firstOrCreate(['user_id' => $student->id, 'lesson_id' => $l2->id], [
            'completed_at' => now(),
        ]);

        Certificate::firstOrCreate(['certificate_number' => 'JC-2026-NEET-001'], [
            'user_id' => $student->id,
            'course_id' => $c1->id,
            'enrollment_id' => $enrollment->id,
            'issued_at' => now(),
        ]);
    }
}
