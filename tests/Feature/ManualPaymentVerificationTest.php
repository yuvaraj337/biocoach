<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\PaymentSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManualPaymentVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $student;
    protected User $otherStudent;
    protected User $admin;
    protected Course $course;
    protected Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->student = User::create([
            'name' => 'Alice Student',
            'email' => 'alice@student.test',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_admin' => false,
        ]);

        $this->otherStudent = User::create([
            'name' => 'Bob Student',
            'email' => 'bob@student.test',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_admin' => false,
        ]);

        $this->admin = User::create([
            'name' => 'Dr. Tanjina Jerin',
            'email' => 'jerin@teacher.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->course = Course::create([
            'title' => 'NEET Biology Mastery 2026',
            'slug' => 'neet-biology-mastery-2026',
            'summary' => 'Comprehensive masterclass for NEET aspirants.',
            'description' => 'Complete syllabus coverage.',
            'price_paise' => 499900,
            'is_published' => true,
        ]);

        $this->lesson = Lesson::create([
            'course_id' => $this->course->id,
            'title' => 'Cell Structure & Function',
            'slug' => 'cell-structure-function',
            'summary' => 'Introduction to eukaryotic cell anatomy.',
            'sort_order' => 1,
            'is_published' => true,
            'is_preview' => false, // Paid lesson
        ]);

        PaymentSetting::set('upi_id', 'jerin@upi');
        PaymentSetting::set('business_name', "Jerin's Coaching");
        PaymentSetting::set('bank_name', 'State Bank of India');
        PaymentSetting::set('account_number', '3982001002345');
        PaymentSetting::set('ifsc_code', 'SBIN0001234');
        PaymentSetting::set('payment_instructions', 'Transfer amount and upload receipt.');
    }

    public function test_student_can_start_checkout_and_order_is_created()
    {
        $response = $this->actingAs($this->student)
            ->post(route('checkout.start', $this->course));

        $order = Order::where('user_id', $this->student->id)
            ->where('course_id', $this->course->id)
            ->first();

        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals(499900, $order->amount_paise);
        $response->assertRedirect(route('checkout.show', $order));
    }

    public function test_checkout_page_displays_configured_payment_details()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-001',
            'amount_paise' => 499900,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->student)
            ->get(route('checkout.show', $order));

        $response->assertOk();
        $response->assertSee('jerin@upi');
        $response->assertSee('State Bank of India');
        $response->assertSee('3982001002345');
        $response->assertSee('SBIN0001234');
        $response->assertSee('Payment Not Submitted');
    }

    public function test_student_can_submit_transaction_id_and_proof()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-002',
            'amount_paise' => 499900,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $file = UploadedFile::fake()->create('receipt.png', 500, 'image/png');

        $response = $this->actingAs($this->student)
            ->post(route('checkout.proof', $order), [
                'payment_method' => 'manual_upi',
                'transaction_id' => 'UTR123456789012',
                'payment_proof' => $file,
                'verification_notes' => 'Transferred from GPay',
            ]);

        $response->assertRedirect(route('checkout.show', $order));

        $order->refresh();
        $this->assertEquals('verification_pending', $order->payment_status);
        $this->assertEquals('UTR123456789012', $order->transaction_id);
        $this->assertEquals('receipt.png', $order->payment_proof_original_name);
        $this->assertNotNull($order->payment_proof_path);
        Storage::disk('local')->assertExists($order->payment_proof_path);

        // Crucial requirement: uploading proof MUST NOT automatically grant enrollment
        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);
    }

    public function test_unverified_student_cannot_access_paid_lessons()
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.lessons.show', [$this->course, $this->lesson]));

        $response->assertStatus(403);
    }

    public function test_other_student_cannot_access_or_view_payment_proof()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-003',
            'amount_paise' => 499900,
            'status' => 'verification_pending',
            'payment_status' => 'verification_pending',
            'payment_proof_path' => 'payment_proofs/test.png',
            'payment_proof_original_name' => 'test.png',
        ]);

        Storage::disk('local')->put('payment_proofs/test.png', 'fake image bytes');

        $response = $this->actingAs($this->otherStudent)
            ->get(route('orders.proof', $order));

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_access_admin_verifications()
    {
        $response = $this->actingAs($this->student)
            ->get(route('admin.verifications.index'));

        $response->assertStatus(403);
    }

    public function test_pending_payment_appears_in_admin_verification_list()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-004',
            'amount_paise' => 499900,
            'status' => 'verification_pending',
            'payment_status' => 'verification_pending',
            'transaction_id' => 'UTR998877665544',
            'proof_submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.verifications.index', ['status' => 'awaiting']));

        $response->assertOk();
        $response->assertSee($order->order_number);
        $response->assertSee('Alice Student');
        $response->assertSee('UTR998877665544');
    }

    public function test_admin_can_verify_payment_and_enrollment_is_activated()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-005',
            'amount_paise' => 499900,
            'status' => 'verification_pending',
            'payment_status' => 'verification_pending',
            'transaction_id' => 'UTR123456789012',
            'payment_proof_path' => 'payment_proofs/test.png',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.verifications.verify', $order), [
                'admin_note' => 'Confirmed in SBI bank statement',
            ]);

        $response->assertRedirect(route('admin.verifications.show', $order));

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('paid', $order->status);
        $this->assertEquals($this->admin->id, $order->verified_by);
        $this->assertNotNull($order->verified_at);

        // Enrollment must be created and active
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);

        // Audit log must be recorded
        $this->assertDatabaseHas('admin_audit_logs', [
            'admin_user_id' => $this->admin->id,
            'action' => 'payment.manually_verified',
            'subject_id' => $order->id,
        ]);

        // Student now has access to the lesson
        $lessonResponse = $this->actingAs($this->student)
            ->get(route('student.lessons.show', [$this->course, $this->lesson]));
        $lessonResponse->assertOk();

        // Bob still has NO access
        $bobResponse = $this->actingAs($this->otherStudent)
            ->get(route('student.lessons.show', [$this->course, $this->lesson]));
        $bobResponse->assertStatus(403);
    }

    public function test_duplicate_verification_is_prevented()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-006',
            'amount_paise' => 499900,
            'status' => 'paid',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.verifications.verify', $order));

        $response->assertSessionHasErrors(['order']);
    }

    public function test_admin_can_reject_payment_with_reason()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-007',
            'amount_paise' => 499900,
            'status' => 'verification_pending',
            'payment_status' => 'verification_pending',
            'transaction_id' => 'INVALID_UTR',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.verifications.reject', $order), [
                'rejection_reason' => 'Transaction ID not found in bank statement. Please verify with your bank.',
            ]);

        $response->assertRedirect(route('admin.verifications.show', $order));

        $order->refresh();
        $this->assertEquals('rejected', $order->payment_status);
        $this->assertEquals('Transaction ID not found in bank statement. Please verify with your bank.', $order->rejection_reason);

        // Enrollment must NOT be active
        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);

        // Student should see rejection reason and resubmit option
        $studentView = $this->actingAs($this->student)
            ->get(route('checkout.show', $order));
        $studentView->assertOk();
        $studentView->assertSee('Transaction ID not found in bank statement');
        $studentView->assertSee('Rejected — Action Required');
    }

    public function test_rejected_student_can_resubmit_payment_proof()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-008',
            'amount_paise' => 499900,
            'status' => 'rejected',
            'payment_status' => 'rejected',
            'rejection_reason' => 'Invalid screenshot',
        ]);

        $file = UploadedFile::fake()->create('new_receipt.png', 400, 'image/png');

        $response = $this->actingAs($this->student)
            ->post(route('checkout.proof', $order), [
                'payment_method' => 'manual_upi',
                'transaction_id' => 'CORRECT_UTR_9999',
                'payment_proof' => $file,
            ]);

        $response->assertRedirect(route('checkout.show', $order));

        $order->refresh();
        $this->assertEquals('verification_pending', $order->payment_status);
        $this->assertEquals('CORRECT_UTR_9999', $order->transaction_id);
        $this->assertNull($order->rejection_reason); // Reset rejection reason on resubmission
    }

    public function test_admin_can_update_payment_settings()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.payment-settings.update'), [
                'business_name' => "Jerin's Biology Academy",
                'upi_id' => 'drjerin.official@upi',
                'account_holder' => 'Dr. Tanjina Jerin',
                'bank_name' => 'HDFC Bank',
                'account_number' => '50100234567890',
                'ifsc_code' => 'HDFC0001234',
                'payment_instructions' => 'Send via UPI and upload screenshot.',
                'manual_upi_enabled' => '1',
                'manual_bank_enabled' => '1',
            ]);

        $response->assertRedirect(route('admin.payment-settings.edit'));

        $this->assertEquals('drjerin.official@upi', PaymentSetting::get('upi_id'));
        $this->assertEquals('HDFC Bank', PaymentSetting::get('bank_name'));

        $this->assertDatabaseHas('admin_audit_logs', [
            'admin_user_id' => $this->admin->id,
            'action' => 'payment_settings.updated',
        ]);
    }

    public function test_invalid_submission_is_rejected_by_validation()
    {
        $order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-TEST-009',
            'amount_paise' => 499900,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Test with invalid file extension (e.g. .exe or .txt) and too short transaction_id
        $badFile = UploadedFile::fake()->create('script.txt', 10, 'text/plain');

        $response = $this->actingAs($this->student)
            ->post(route('checkout.proof', $order), [
                'payment_method' => 'manual_upi',
                'transaction_id' => 'abc', // less than min 4 chars
                'payment_proof' => $badFile,
            ]);

        $response->assertSessionHasErrors(['transaction_id', 'payment_proof']);
    }
}
