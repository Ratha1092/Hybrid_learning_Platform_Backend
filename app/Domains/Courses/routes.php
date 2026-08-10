<?php

use Illuminate\Support\Facades\Route;

use App\Domains\Courses\Controllers\CourseController;
use App\Domains\Courses\Controllers\CategoryController;
use App\Domains\Courses\Controllers\SearchController;
use App\Domains\Courses\Controllers\InstructorCourseController;
use App\Domains\Courses\Controllers\InstructorSectionController;
use App\Domains\Courses\Controllers\InstructorLessonController;
use App\Domains\Courses\Controllers\InstructorDashboardController;
use App\Domains\Courses\Controllers\InstructorLessonResourceController;
use App\Domains\Courses\Controllers\InstructorStandaloneSectionController;
use App\Domains\Learning\Controllers\ReviewController;

// Global search
Route::middleware('throttle:courses')
    ->get('/search', SearchController::class);

// Public Categories
Route::middleware('throttle:courses')
    ->prefix('categories')
    ->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{slug}', [CategoryController::class, 'show']);
    });

// Public Courses
Route::middleware('throttle:courses')
    ->prefix('courses')
    ->group(function () {
        Route::get('/',[CourseController::class, 'index']);
        Route::get('/{slug}',[CourseController::class, 'show']);
        Route::get('/{courseId}/reviews', [ReviewController::class, 'index'])->where('courseId', '[0-9]+');
    });

// Student review submission
Route::middleware(['auth:sanctum', 'throttle:courses'])
    ->post('/courses/{courseId}/reviews', [ReviewController::class, 'store'])
    ->where('courseId', '[0-9]+');
// Instructor Dashboard
Route::middleware(['auth:sanctum','verified_instructor','throttle:courses',])
    ->prefix('instructor')
    ->group(function () {
        Route::get('/dashboard', [InstructorDashboardController::class, 'index']);
        Route::get('/students', [InstructorDashboardController::class, 'students']);
    });

// Instructor Standalone Sections
Route::middleware(['auth:sanctum', 'verified_instructor', 'throttle:courses'])
    ->prefix('instructor/sections')
    ->group(function () {
        Route::post('/', [InstructorStandaloneSectionController::class, 'store']);
        Route::get('/standalone', [InstructorStandaloneSectionController::class, 'standalone']);
        Route::put('/{id}', [InstructorStandaloneSectionController::class, 'update']);
        Route::delete('/{id}', [InstructorStandaloneSectionController::class, 'destroy']);
    });

// Instructor Course Management
Route::middleware(['auth:sanctum','verified_instructor','throttle:courses',])
    ->prefix('instructor/courses')
    ->group(function () {
        //Courses
        Route::get('/',[InstructorCourseController::class, 'index']);
        Route::post('/',[InstructorCourseController::class, 'store']);
        Route::get('/{id}',[InstructorCourseController::class, 'show']);
        Route::put('/{id}',[InstructorCourseController::class, 'update']);
        Route::post('/{id}',[InstructorCourseController::class, 'update']);
        Route::delete('/{id}',[InstructorCourseController::class, 'destroy']);

        //Submit For Review
        Route::post('/{id}/submit-review',[InstructorCourseController::class, 'submitForReview']);

        //Attach standalone sections to a course
        Route::post('/{id}/attach-sections', [InstructorCourseController::class, 'attachSections']);
        Route::prefix('{courseId}/sections')->group(function () {
                Route::get('/',[InstructorSectionController::class, 'index']);
                Route::post('/',[InstructorSectionController::class, 'store']);
                Route::put('/{sectionId}',[InstructorSectionController::class, 'update']);
                Route::delete('/{sectionId}',[InstructorSectionController::class, 'destroy']);

                //Lessons
                Route::prefix('{sectionId}/lessons')->group(function () {
                        Route::get('/',[InstructorLessonController::class, 'index']);
                        Route::post('/',[InstructorLessonController::class, 'store']);
                        Route::put('/{lessonId}',[InstructorLessonController::class, 'update']);
                        Route::delete('/{lessonId}',[InstructorLessonController::class, 'destroy']);
                        Route::post('/{lessonId}/upload-video',[InstructorLessonController::class, 'uploadVideo']);

                        // Upload Resources
                        Route::prefix('{lessonId}/resources')->group(function () {
                            Route::get('/', [InstructorLessonResourceController::class, 'index']);
                            Route::post('/', [InstructorLessonResourceController::class, 'store']);
                            Route::delete('/{resourceId}', [InstructorLessonResourceController::class, 'destroy']);
                        });
                    });
            });
    });