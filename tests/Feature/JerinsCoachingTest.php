<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Support\Facades\Hash;

class JerinsCoachingTest extends TestCase
{
    public function test_homepage_loads_with_brand_and_courses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee("Jerin's Coaching");
        $response->assertSee("Learn");
        $response->assertSee("Biology");
    }

    public function test_courses_index_page_loads(): void
    {
        $response = $this->get('/courses');
        $response->assertStatus(200);
        $response->assertSee('Our Courses');
        $response->assertSee('NEET Biology Complete Course');
    }

    public function test_course_detail_page_loads(): void
    {
        $response = $this->get('/courses/neet-biology-complete-course');
        $response->assertStatus(200);
        $response->assertSee('NEET Biology Complete Course');
        $response->assertSee('Cell Structure and Organelles');
    }

    public function test_certificate_verification_returns_student_name(): void
    {
        $response = $this->get('/verify-certificate/JC-2026-NEET-001');
        $response->assertStatus(200);
        $response->assertSee('Certificate verified');
        $response->assertSee('Aarav Sharma');
    }

    public function test_user_registration_and_validation(): void
    {
        $email = 'test_student_' . uniqid() . '@example.com';
        $response = $this->post('/register', [
            'name' => 'Test Learner',
            'email' => $email,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    public function test_user_login_success_and_failure(): void
    {
        // Invalid credentials
        $response = $this->post('/login', [
            'email' => 'student@jerinscoaching.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertSessionHasErrors('email');

        // Valid credentials
        $response = $this->post('/login', [
            'email' => 'student@jerinscoaching.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/');
    }

    public function test_admin_dashboard_authorization(): void
    {
        // Guest is blocked
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        // Normal student is forbidden (403)
        $student = User::where('email', 'student@jerinscoaching.com')->first();
        $response = $this->actingAs($student)->get('/admin');
        $response->assertStatus(403);

        // Admin is authorized (200)
        $admin = User::where('email', 'admin@jerinscoaching.com')->first();
        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_student_dashboard_access(): void
    {
        $student = User::where('email', 'student@jerinscoaching.com')->first();
        $response = $this->actingAs($student)->get('/student/courses');
        $response->assertStatus(200);
        $response->assertSee('NEET Biology Complete Course');
    }

    public function test_enrolled_student_can_view_lesson(): void
    {
        $student = User::where('email', 'student@jerinscoaching.com')->first();
        $response = $this->actingAs($student)->get('/student/courses/neet-biology-complete-course/lessons/cell-structure-and-organelles');
        $response->assertStatus(200);
        $response->assertSee('Cell Structure and Organelles');
    }

    public function test_enrolled_student_can_view_quiz(): void
    {
        $student = User::where('email', 'student@jerinscoaching.com')->first();
        $response = $this->actingAs($student)->get('/student/courses/neet-biology-complete-course/quizzes/cell-biology-mastery-quiz');
        $response->assertStatus(200);
        $response->assertSee('Cell Biology Mastery Quiz');
    }
}
