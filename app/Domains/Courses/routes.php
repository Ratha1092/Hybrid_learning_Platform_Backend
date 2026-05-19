<?php

use Illuminate\Support\Facades\Route;

use App\Domains\Courses\Controllers\CourseController;
use App\Domains\Courses\Controllers\InstructorCourseController;
use App\Domains\Courses\Controllers\InstructorSectionController;
use App\Domains\Courses\Controllers\InstructorLessonController;

//Public Courses
Route::middleware('throttle:courses')
    ->prefix('courses')
    ->group(function () {
        //Course Listing
        Route::get('/',[CourseController::class, 'index']);
        //Course Details
        Route::get('/{slug}',[CourseController::class, 'show']);
    });
//Instructor Course Management
Route::middleware(['auth:sanctum','verified_instructor','throttle:courses',])
    ->prefix('instructor/courses')
    ->group(function () {
        //Courses
        Route::get('/',[InstructorCourseController::class, 'index']);
        Route::post('/',[InstructorCourseController::class, 'store']);
        Route::get('/{id}',[InstructorCourseController::class, 'show']);
        Route::put('/{id}',[InstructorCourseController::class, 'update']);
        Route::delete('/{id}',[InstructorCourseController::class, 'destroy']);

        //Submit For Review
        Route::post('/{id}/submit-review',[InstructorCourseController::class, 'submitForReview']);
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
                    });
            });
    });