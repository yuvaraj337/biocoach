<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\PaymentAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RazorpayWebhookTest extends TestCase
{
    use DatabaseTransactions;

    protected User $student;
    protected Course $course;
    protected Order $order;
    protected PaymentAttempt $attempt;
    protected string $webhookSecret = 'test_webhook_secret_key_12345';

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.razorpay.webhook_secret', $this->webhookSecret);
        Config::set('services.razorpay.key', 'rzp_test_mock_key');
        Config::set('services.razorpay.secret', 'mock_secret_key_12345');

        $this->student = User::create([
            'name' => 'Charlie Student',
            'email' => 'charlie@student.test',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'is_admin' => false,
        ]);

        $this->course = Course::create([
            'title' => 'Biology Crash Course 2026',
            'slug' => 'biology-crash-course-2026',
            'summary' => 'Quick revision for exams',
            'price_paise' => 299900,
            'is_published' => true,
        ]);

        $this->order = Order::create([
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'order_number' => 'JC-GATEWAY-001',
            'amount_paise' => 299900,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->attempt = PaymentAttempt::create([
            'order_id' => $this->order->id,
            'provider' => 'razorpay',
            'provider_order_id' => 'order_RzpFakeOrder123',
            'status' => 'created',
            'amount_paise' => 299900,
            'currency' => 'INR',
        ]);
    }

    public function test_webhook_rejects_invalid_signature()
    {
        $payload = json_encode(['event' => 'payment.captured']);

        $response = $this->call(
            'POST',
            route('payments.webhook'),
            [],
            [],
            [],
            ['HTTP_X-Razorpay-Signature' => 'invalid_signature_hash'],
            $payload
        );

        $response->assertStatus(400);
    }

    public function test_webhook_rejects_amount_mismatch()
    {
        $payloadArray = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_FakePaymentId999',
                        'order_id' => 'order_RzpFakeOrder123',
                        'amount' => 100000, // mismatch: expected 299900
                        'currency' => 'INR',
                    ]
                ]
            ]
        ];
        $payload = json_encode($payloadArray);
        $signature = hash_hmac('sha256', $payload, $this->webhookSecret);

        $response = $this->call(
            'POST',
            route('payments.webhook'),
            [],
            [],
            [],
            ['HTTP_X-Razorpay-Signature' => $signature],
            $payload
        );

        $response->assertStatus(400);
    }

    public function test_webhook_valid_signature_captures_payment_and_activates_enrollment()
    {
        $payloadArray = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_ValidCapturedId777',
                        'order_id' => 'order_RzpFakeOrder123',
                        'amount' => 299900,
                        'currency' => 'INR',
                    ]
                ]
            ]
        ];
        $payload = json_encode($payloadArray);
        $signature = hash_hmac('sha256', $payload, $this->webhookSecret);

        $response = $this->call(
            'POST',
            route('payments.webhook'),
            [],
            [],
            [],
            ['HTTP_X-Razorpay-Signature' => $signature],
            $payload
        );

        $response->assertOk();

        $this->order->refresh();
        $this->assertEquals('paid', $this->order->payment_status);
        $this->assertEquals('paid', $this->order->status);
        $this->assertEquals('razorpay_gateway', $this->order->payment_method);
        $this->assertEquals('pay_ValidCapturedId777', $this->order->payment_reference);

        // Enrollment active
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $this->student->id,
            'course_id' => $this->course->id,
            'status' => 'active',
        ]);

        // Duplicate webhook call should be idempotent
        $duplicateResponse = $this->call(
            'POST',
            route('payments.webhook'),
            [],
            [],
            [],
            ['HTTP_X-Razorpay-Signature' => $signature],
            $payload
        );
        $duplicateResponse->assertOk();
        $this->assertEquals(1, Enrollment::where('user_id', $this->student->id)->where('course_id', $this->course->id)->count());
    }
}
