<?php
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\LessonController as AdminLessonController;
use App\Http\Controllers\Admin\LessonResourceController as AdminLessonResourceController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\OrderProofController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\LearningController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Payments\RazorpayController;
use App\Http\Controllers\StudentController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/',[HomeController::class,'index'])->name('home');
Route::middleware('guest')->group(function(){
 Route::get('/login',[AuthController::class,'showLogin'])->name('login');
 Route::post('/login',[AuthController::class,'login'])->name('login.store');
 Route::get('/register',[AuthController::class,'showRegister'])->name('register');
 Route::post('/register',[AuthController::class,'register'])->name('register.store');
});
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');
Route::get('/courses',[CourseController::class,'index'])->name('courses.index');
Route::get('/courses/{course:slug}',[CourseController::class,'show'])->name('courses.show');
Route::post('/checkout/{course}',[CheckoutController::class,'start'])->middleware('auth')->name('checkout.start');
Route::get('/checkout/{order}',[CheckoutController::class,'show'])->middleware('auth')->name('checkout.show');
Route::post('/checkout/{order}/proof',[CheckoutController::class,'submitProof'])->middleware('auth')->name('checkout.proof');
Route::get('/orders/{order}/proof',[OrderProofController::class,'show'])->middleware('auth')->name('orders.proof');
Route::view('/about','about')->name('about');
// Language Switcher
Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['bn', 'en'], true)) {
        session(['locale' => $locale]);
        cookie()->queue('locale', $locale, 60 * 24 * 365);
        if (auth()->check()) {
            auth()->user()->forceFill(['locale' => $locale])->saveQuietly();
        }
    }
    return redirect()->back();
})->name('language.switch');

Route::post('/checkout/{order}/voucher', [CheckoutController::class, 'applyVoucher'])->middleware('auth')->name('checkout.voucher');

// SSLCommerz Bangladesh Gateway
Route::middleware('auth')->prefix('payments/sslcommerz')->name('payments.sslcommerz.')->group(function(){
    Route::post('/initiate/{order}', [\App\Http\Controllers\Payments\SSLCommerzController::class, 'initiate'])->name('initiate');
});
Route::post('/payments/sslcommerz/success', [\App\Http\Controllers\Payments\SSLCommerzController::class, 'success'])->name('payments.sslcommerz.success');
Route::post('/payments/sslcommerz/fail', [\App\Http\Controllers\Payments\SSLCommerzController::class, 'fail'])->name('payments.sslcommerz.fail');
Route::post('/payments/sslcommerz/cancel', [\App\Http\Controllers\Payments\SSLCommerzController::class, 'cancel'])->name('payments.sslcommerz.cancel');
Route::post('/payments/sslcommerz/ipn', [\App\Http\Controllers\Payments\SSLCommerzController::class, 'ipn'])->name('payments.sslcommerz.ipn');

Route::middleware('auth')->prefix('student')->name('student.')->group(function(){
 Route::get('/courses',[StudentController::class,'courses'])->name('courses');
 Route::get('/orders',[StudentController::class,'orders'])->name('orders');
 Route::get('/live-classes',[StudentController::class,'liveClasses'])->name('live-classes');
 Route::get('/courses/{course:slug}/assignments',[StudentController::class,'assignments'])->name('assignments.index');
 Route::get('/courses/{course:slug}/assignments/{assignment}',[StudentController::class,'showAssignment'])->name('assignments.show');
 Route::post('/courses/{course:slug}/assignments/{assignment}/submit',[StudentController::class,'submitAssignment'])->name('assignments.submit');
 Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');
 Route::put('/profile',[ProfileController::class,'update'])->name('profile.update');
 Route::get('/certificates',[CertificateController::class,'index'])->name('certificates.index');
 Route::post('/courses/{course:slug}/certificate',[CertificateController::class,'issue'])->name('certificates.issue');
 Route::get('/certificates/{certificate}',[CertificateController::class,'show'])->name('certificates.show');
 Route::get('/courses/{course:slug}/quizzes',[StudentQuizController::class,'index'])->name('quizzes.index');
 Route::get('/courses/{course:slug}/quizzes/{quiz:slug}',[StudentQuizController::class,'show'])->name('quizzes.show');
 Route::post('/courses/{course:slug}/quizzes/{quiz:slug}/start',[StudentQuizController::class,'start'])->name('quizzes.start');
 Route::get('/courses/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}',[StudentQuizController::class,'take'])->name('quizzes.take');
 Route::post('/courses/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}',[StudentQuizController::class,'submit'])->name('quizzes.submit');
 Route::get('/courses/{course:slug}/quizzes/{quiz:slug}/attempts/{attempt}/result',[StudentQuizController::class,'result'])->name('quizzes.result');
 Route::get('/courses/{course:slug}/lessons/{lesson:slug}',[LearningController::class,'show'])->name('lessons.show');
 Route::post('/courses/{course:slug}/lessons/{lesson:slug}/complete',[LearningController::class,'complete'])->name('lessons.complete');
 Route::get('/courses/{course:slug}/lessons/{lesson:slug}/resources/{resource}/download',[LearningController::class,'download'])->name('lessons.resources.download');
});
Route::middleware('auth')->prefix('payments')->name('payments.')->group(function(){
 Route::post('/orders/{order}/create',[RazorpayController::class,'createOrder'])->name('create');
 Route::post('/orders/{order}/verify',[RazorpayController::class,'verify'])->name('verify');
});
Route::post('/payments/razorpay/webhook',[RazorpayController::class,'webhook'])->name('payments.webhook');
Route::get('/verify-certificate/{number}',[CertificateController::class,'verify'])->name('certificates.verify');
Route::prefix('admin')->name('admin.')->middleware(['auth',EnsureUserIsAdmin::class])->group(function(){
 Route::get('/',[\App\Http\Controllers\Admin\DashboardController::class,'index'])->name('dashboard');
 Route::resource('courses',AdminCourseController::class)->except(['show']);
 Route::resource('categories',\App\Http\Controllers\Admin\CategoryController::class);
 Route::resource('users',\App\Http\Controllers\Admin\UserController::class);
 Route::resource('live-classes',\App\Http\Controllers\Admin\LiveClassController::class);
 Route::resource('vouchers',\App\Http\Controllers\Admin\VoucherController::class);
 Route::resource('courses.assignments',\App\Http\Controllers\Admin\AssignmentController::class);
 Route::post('/assignments/{assignment}/submissions/{submission}/grade',[\App\Http\Controllers\Admin\AssignmentController::class,'grade'])->name('assignments.grade');
 Route::get('/courses/{course}/quizzes',[AdminQuizController::class,'index'])->name('courses.quizzes.index');
 Route::get('/courses/{course}/quizzes/create',[AdminQuizController::class,'create'])->name('courses.quizzes.create');
 Route::post('/courses/{course}/quizzes',[AdminQuizController::class,'store'])->name('courses.quizzes.store');
 Route::get('/courses/{course}/quizzes/{quiz}/edit',[AdminQuizController::class,'edit'])->name('courses.quizzes.edit');
 Route::put('/courses/{course}/quizzes/{quiz}',[AdminQuizController::class,'update'])->name('courses.quizzes.update');
 Route::delete('/courses/{course}/quizzes/{quiz}',[AdminQuizController::class,'destroy'])->name('courses.quizzes.destroy');
 Route::post('/courses/{course}/quizzes/{quiz}/questions',[AdminQuizController::class,'storeQuestion'])->name('courses.quizzes.questions.store');
 Route::delete('/courses/{course}/quizzes/{quiz}/questions/{question}',[AdminQuizController::class,'destroyQuestion'])->name('courses.quizzes.questions.destroy');
 Route::get('/courses/{course}/lessons',[AdminLessonController::class,'index'])->name('courses.lessons.index');
 Route::get('/courses/{course}/lessons/create',[AdminLessonController::class,'create'])->name('courses.lessons.create');
 Route::post('/courses/{course}/lessons',[AdminLessonController::class,'store'])->name('courses.lessons.store');
 Route::get('/courses/{course}/lessons/{lesson}/edit',[AdminLessonController::class,'edit'])->name('courses.lessons.edit');
 Route::put('/courses/{course}/lessons/{lesson}',[AdminLessonController::class,'update'])->name('courses.lessons.update');
 Route::delete('/courses/{course}/lessons/{lesson}',[AdminLessonController::class,'destroy'])->name('courses.lessons.destroy');
 Route::post('/courses/{course}/lessons/{lesson}/resources',[AdminLessonResourceController::class,'store'])->name('courses.lessons.resources.store');
 Route::delete('/courses/{course}/lessons/{lesson}/resources/{resource}',[AdminLessonResourceController::class,'destroy'])->name('courses.lessons.resources.destroy');
 Route::get('/verifications',[PaymentVerificationController::class,'index'])->name('verifications.index');
 Route::get('/verifications/{order}',[PaymentVerificationController::class,'show'])->name('verifications.show');
 Route::post('/verifications/{order}/verify',[PaymentVerificationController::class,'verify'])->name('verifications.verify');
 Route::post('/verifications/{order}/reject',[PaymentVerificationController::class,'reject'])->name('verifications.reject');
 Route::get('/settings/payment',[PaymentSettingController::class,'edit'])->name('payment-settings.edit');
 Route::post('/settings/payment',[PaymentSettingController::class,'update'])->name('payment-settings.update');
 Route::get('/orders',[AdminOrderController::class,'index'])->name('orders.index');
 Route::get('/orders/{order}',[AdminOrderController::class,'show'])->name('orders.show');
 Route::post('/orders/{order}/mark-failed',[AdminOrderController::class,'markFailed'])->name('orders.fail');
 Route::post('/orders/{order}/revoke-enrollment',[AdminOrderController::class,'revokeEnrollment'])->name('orders.revoke');
 Route::get('/students',[AdminStudentController::class,'index'])->name('students.index');
 Route::get('/students/{student}',[AdminStudentController::class,'show'])->name('students.show');
 Route::patch('/students/{student}/enrollments/{enrollment}',[AdminStudentController::class,'updateEnrollment'])->name('students.enrollment.update');
 Route::get('/audit-log',[AdminAuditLogController::class,'index'])->name('audit.index');
});
