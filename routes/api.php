<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OfficerController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\InfoController;
use App\Http\Controllers\Api\InternController;
use App\Http\Controllers\Api\EvaluationController;

Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('officers', OfficerController::class);
Route::get('students/code/{student_code}', [StudentController::class, 'getByStudentCode']);
Route::apiResource('students', StudentController::class);
Route::apiResource('companies', CompanyController::class);
Route::apiResource('infos', InfoController::class);
Route::apiResource('interns', InternController::class);
Route::apiResource('evaluations', EvaluationController::class);

// Daily work logs routes
Route::get('daily-logs', [\App\Http\Controllers\Api\DailyLogController::class, 'index']);
Route::post('daily-logs', [\App\Http\Controllers\Api\DailyLogController::class, 'store']);
Route::put('daily-logs/{id}', [\App\Http\Controllers\Api\DailyLogController::class, 'update']);
Route::delete('daily-logs/{id}', [\App\Http\Controllers\Api\DailyLogController::class, 'destroy']);
Route::get('daily-logs/summary', [\App\Http\Controllers\Api\DailyLogController::class, 'summary']);

// Internship criteria routes
Route::get('criteria/{student_code}', [\App\Http\Controllers\Api\CriteriaController::class, 'getCriteria']);
Route::post('criteria/{student_code}', [\App\Http\Controllers\Api\CriteriaController::class, 'upsertCriteria']);
Route::get('criteria/{student_code}/eligibility', [\App\Http\Controllers\Api\CriteriaController::class, 'checkEligibility']);

Route::apiResource('assessments', \App\Http\Controllers\Api\AssessmentController::class);
Route::apiResource('documents', \App\Http\Controllers\Api\DocumentController::class);

// Internship approval workflow routes
Route::get('interns/student/{student_code}', [InternController::class, 'getByStudentCode']);
Route::post('interns/{id}/approve-officer', [InternController::class, 'approveByOfficer']);
Route::post('interns/{id}/approve-dean', [InternController::class, 'approveByDean']);
Route::post('interns/{id}/reject', [InternController::class, 'reject']);
Route::get('interns/{id}/download-pdf', [InternController::class, 'downloadPdf']);

// Training evidence routes
Route::post('interns/{id}/training-evidence', [InternController::class, 'submitTrainingEvidence']);
Route::get('interns/{id}/training-evidence', [InternController::class, 'getTrainingEvidence']);
Route::post('interns/{id}/final-report', [InternController::class, 'submitReport']);
// Mentor routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('mentor/students', [\App\Http\Controllers\Api\MentorController::class, 'myStudents']);
    Route::get('supervisor/students', [\App\Http\Controllers\Api\SupervisorController::class, 'myStudents']);
    // Officer routes
    Route::get('officer/reports', [\App\Http\Controllers\Api\ReportController::class, 'index']);
    Route::get('officer/monitor', [\App\Http\Controllers\Api\MonitorController::class, 'index']);
    Route::get('officer/monitor/{id}', [\App\Http\Controllers\Api\MonitorController::class, 'show']);
    // Student Management
    Route::get('officer/students', [\App\Http\Controllers\Api\OfficerStudentController::class, 'index']);
    Route::put('officer/students/{id}/password-expiry', [\App\Http\Controllers\Api\OfficerStudentController::class, 'updatePasswordExpiry']);
});
