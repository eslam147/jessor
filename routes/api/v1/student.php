<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiController;

Route::group(['prefix' => 'student'], function () {

  //Non Authenticated APIs
  Route::post('login', [StudentApiController::class, 'login']);
  Route::post('register', [StudentApiController::class, 'register']);
  Route::post('forgot-password', [StudentApiController::class, 'forgotPassword']);

  //Authenticated APIs
  Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('dashboard', [StudentApiController::class, 'dashboard']);
    Route::get('subjects', [StudentApiController::class, 'subjects']);
    Route::get('class-subjects', [StudentApiController::class, 'classSubjects']);
    Route::post('select-subjects', [StudentApiController::class, 'selectSubjects']);
    Route::get('parent-details', [StudentApiController::class, 'getParentDetails']);
    Route::get('timetable', [StudentApiController::class, 'getTimetable']);

    Route::get('lessons', [StudentApiController::class, 'getLessons']);
    Route::post('enroll_free_lesson', [StudentApiController::class, 'enrollFreeLesson']);
    Route::post('enroll_lesson_by_coupon', [StudentApiController::class, 'redeemCouponForLesson']);
    Route::get('enrollment_lessons', [StudentApiController::class, 'getEnrollmentLessons']);
    
    Route::prefix('teachers')->group(function () {
      Route::get('/', [StudentApiController::class, 'getTeachers']);

    });
    
    Route::get('lesson-topics', [StudentApiController::class, 'getLessonTopics']);
    Route::get('assignments', [StudentApiController::class, 'getAssignments']);
    Route::post('submit-assignment', [StudentApiController::class, 'submitAssignment']);
    Route::post('edit-assignment', [StudentApiController::class, 'editAssignmentSubmission']);
    Route::post('delete-assignment-submission', [StudentApiController::class, 'deleteAssignmentSubmission']);
    Route::get('attendance', [StudentApiController::class, 'getAttendance']);
    Route::get('announcements', [StudentApiController::class, 'getAnnouncements']);
    Route::get('get-exam-list', [StudentApiController::class, 'getExamList']); // Exam list Route
    Route::get('get-exam-details', [StudentApiController::class, 'getExamDetails']); // Exam Details Route
    Route::get('exam-marks', [StudentApiController::class, 'getExamMarks']); // Exam Details Route

    // online exam routes
    Route::get('get-online-exam-list', [StudentApiController::class, 'getOnlineExamList']); // Get Online Exam List Route
    Route::get('get-online-exam-questions', [StudentApiController::class, 'getOnlineExamQuestions']); // Get Online Exam Questions Route
    Route::post('submit-online-exam-answers', [StudentApiController::class, 'submitOnlineExamAnswers']); // Submit Online Exam Answers Details Route
    Route::get('get-online-exam-result-list', [StudentApiController::class, 'getOnlineExamResultList']); // Online exam result list Route
    Route::get('get-online-exam-result', [StudentApiController::class, 'getOnlineExamResult']); // Online exam result  Route

    //reports
    Route::get('get-online-exam-report', [StudentApiController::class, 'getOnlineExamReport']); // Online Exam Report Route
    Route::get('get-assignments-report', [StudentApiController::class, 'getAssignmentReport']); // Assignment Report Route

    // profile data
    Route::get('get-profile-data', [StudentApiController::class, 'getProfileDetails']); // Get Profile Data
    Route::get('get-notification', [StudentApiController::class, 'getNotifications']); // Get Notification Data

    Route::get('get-user-list', [StudentApiController::class, 'getChatUserList']);
    Route::post('send-message', [StudentApiController::class, 'sendMessage']);
    Route::post('get-user-message', [StudentApiController::class, 'getUserChatMessage']);
    Route::post('read-all-message', [StudentApiController::class, 'readAllMessages']);

    //fees
    Route::get('fees-details', [StudentApiController::class, 'getFeesDetails']); //Fees Details
    Route::post('add-fees-transaction', [StudentApiController::class, 'storeFeesTransaction']); //Fees Details
    Route::post('store-fees', [StudentApiController::class, 'storeFees']); //Store Fees
    Route::get('fees-paid-list', [StudentApiController::class, 'feesPaidList']); //Fees Details
    Route::get('fees-paid-receipt-pdf', [StudentApiController::class, 'feesPaidReceiptPDF']); //Fees Receipt
    Route::get('fees-transactions-list', [StudentApiController::class, 'getFeesPaymentTransactions']); //Fees Payment Transaction Details
    Route::post('fail-payment-transaction', [StudentApiController::class, 'failPaymentTransactionStatus']); // Make Payment Transaction Fail API

    //fee notification
    Route::get('send-fee-notification', [StudentApiController::class, 'sendFeeNotification']);
  });
});
