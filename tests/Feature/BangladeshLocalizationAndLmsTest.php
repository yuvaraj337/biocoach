<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\LiveClass;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BangladeshLocalizationAndLmsTest extends TestCase
{
    protected User $student;
    protected User $teacher;
    protected User $admin;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::firstOrCreate(
            ['email' => 'bd_student@example.com'],
            ['name' => 'Rahim Ahmed', 'password' => bcrypt('password123'), 'role' => 'student']
        );

        $this->teacher = User::firstOrCreate(
            ['email' => 'dr_tanjina@example.com'],
            ['name' => 'Dr. Tanjina Jerin', 'password' => bcrypt('password123'), 'role' => 'teacher', 'is_admin' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            ['name' => 'Super Administrator', 'password' => bcrypt('password123'), 'role' => 'super_admin', 'is_admin' => true]
        );

        $category = Category::firstOrCreate(['slug' => 'hsc-biology'], ['name' => 'HSC Biology']);

        $this->course = Course::firstOrCreate(
            ['slug' => 'test-hsc-botany'],
            [
                'title' => 'HSC Botany Masterclass 2026',
                'category_id' => $category->id,
                'price_paise' => 299900, // 2999 BDT
                'is_published' => true,
                'summary' => 'Comprehensive preparation for HSC Biology.',
                'description' => 'Detailed curriculum covering botany and genetics.',
            ]
        );
    }

    public function test_language_switcher_persists_preference(): void
    {
        $response = $this->get(route('language.switch', 'bn'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'bn');

        $responseEn = $this->get(route('language.switch', 'en'));
        $responseEn->assertRedirect();
        $responseEn->assertSessionHas('locale', 'en');
    }

    public function test_bangla_numerals_and_bdt_formatting_helper(): void
    {
        // Force conversion
        $this->assertEquals('১২৩৪৫', bn_number('12345', true));
        $this->assertEquals('০', bn_number(0, true));

        // When locale is Bangla
        app()->setLocale('bn');
        $this->assertEquals('১২৩৪৫', bn_number('12345'));
        $this->assertEquals('৳২,৯৯৯', bdt(299900, true));

        // When locale is English
        app()->setLocale('en');
        $this->assertEquals('12345', bn_number('12345'));
        $this->assertEquals('৳2,999', bdt(299900, true));
    }

    public function test_student_can_apply_valid_discount_voucher(): void
    {
        $code = 'VOUCH' . strtoupper(Str::random(10));
        $voucher = Voucher::create([
            'code' => $code,
            'discount_type' => 'fixed_paise',
            'discount_value' => 50000, // 500 BDT
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-VOUCH-' . strtoupper(Str::random(10)),
            'amount_paise' => 299900,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('checkout.voucher', $order), [
                'voucher_code' => $code,
            ]);

        $response->assertRedirect(route('checkout.show', $order));
        $order->refresh();
        $this->assertEquals(249900, $order->amount_paise); // 2999 - 500 = 2499 BDT
    }

    public function test_student_can_view_scheduled_live_classes(): void
    {
        $this->student->enrollments()->firstOrCreate(
            ['course_id' => $this->course->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );

        $liveClass = LiveClass::create([
            'course_id' => $this->course->id,
            'teacher_id' => $this->teacher->id,
            'title' => 'Cell Division Live Session',
            'meeting_link' => 'https://meet.google.com/xyz-live-test',
            'platform' => 'Google Meet',
            'start_time' => now()->addDay(),
            'duration_minutes' => 90,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('student.live-classes'));

        $response->assertOk();
        $response->assertSee('Cell Division Live Session');
        $response->assertSee('Google Meet');
        $response->assertSee('https://meet.google.com/xyz-live-test');
    }

    public function test_student_can_submit_assignment_and_teacher_can_grade(): void
    {
        Storage::fake('local');

        $this->student->enrollments()->firstOrCreate(
            ['course_id' => $this->course->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );

        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'title' => 'Genetics Cross Diagram HW 1',
            'description' => 'Draw a Punnett square for dihybrid cross.',
            'total_marks' => 50,
            'due_date' => now()->addDays(5),
            'is_published' => true,
        ]);

        $pdf = UploadedFile::fake()->create('homework.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->student)
            ->post(route('student.assignments.submit', [$this->course, $assignment]), [
                'submission_text' => 'Here is my Punnett square analysis.',
                'attachment' => $pdf,
            ]);

        $response->assertRedirect();

        $submission = $assignment->submissions()->where('user_id', $this->student->id)->first();
        $this->assertNotNull($submission);
        $this->assertEquals('submitted', $submission->status);

        // Teacher grades the assignment
        $gradeResponse = $this->actingAs($this->teacher)
            ->post(route('admin.assignments.grade', [$assignment, $submission]), [
                'marks_obtained' => 48,
                'feedback' => 'Excellent accuracy in Mendelian ratios!',
            ]);

        $gradeResponse->assertRedirect();
        $submission->refresh();
        $this->assertEquals(48, $submission->marks_obtained);
        $this->assertEquals('graded', $submission->status);
    }

    public function test_sslcommerz_initiation_creates_pending_order_flow(): void
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-SSLC-' . strtoupper(Str::random(10)),
            'amount_paise' => 299900,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->student)
            ->post(route('payments.sslcommerz.initiate', $order));

        // In sandbox with test gateway, either redirects to gateway or renders gateway attempt
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
        $order->refresh();
        $this->assertEquals('sslcommerz', $order->payment_method);
    }
}
