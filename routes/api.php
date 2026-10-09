<?php

use App\Http\Controllers\Api\V1\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Api\V1\Admin\ContactMessageController as AdminContactMessageController;
use App\Http\Controllers\Api\V1\Admin\EducationController as AdminEducationController;
use App\Http\Controllers\Api\V1\Admin\ExperienceController as AdminExperienceController;
use App\Http\Controllers\Api\V1\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Api\V1\Admin\ProfileCvController;
use App\Http\Controllers\Api\V1\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Api\V1\Admin\ProjectImageController;
use App\Http\Controllers\Api\V1\Admin\ProjectUpdateController as AdminProjectUpdateController;
use App\Http\Controllers\Api\V1\Admin\SkillController as AdminSkillController;
use App\Http\Controllers\Api\V1\Admin\TechnologyController as AdminTechnologyController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\GoogleAuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\ChangePasswordController;
use App\Http\Controllers\Api\V1\ContactMessageController;
use App\Http\Controllers\Api\V1\EducationController;
use App\Http\Controllers\Api\V1\ExperienceController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProjectAccessController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectFollowController;
use App\Http\Controllers\Api\V1\ProjectUpdateController;
use App\Http\Controllers\Api\V1\SavedTemplateController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Controllers\Api\V1\TechnologyController;
use App\Http\Controllers\Api\V1\UserDashboardController;
use App\Http\Controllers\Api\V1\UserProjectUpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'status' => 'ok',
            'project' => 'SystemSeed',
            'version' => 'v1',
        ]);
    });

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}/demo', [ProjectAccessController::class, 'demo']);
    Route::get('/projects/{project}/download', [ProjectAccessController::class, 'download']);
    Route::get('/projects/{project}/source-code', [ProjectAccessController::class, 'sourceCode']);
    Route::get('/projects/{project}/updates', [ProjectUpdateController::class, 'index']);
    Route::get('/projects/{slug}', [ProjectController::class, 'show']);
    Route::get('/technologies', [TechnologyController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show']);
    Route::post('/contact', [ContactMessageController::class, 'store'])->middleware('throttle:contact');
    Route::get('/experiences', [ExperienceController::class, 'index']);
    Route::get('/education', [EducationController::class, 'index']);
    Route::get('/skills', [SkillController::class, 'index']);

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
        Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])->middleware('throttle:password-reset');
        Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:password-reset');
        Route::get('/google/redirect', [GoogleAuthController::class, 'redirect']);
        Route::get('/google/callback', [GoogleAuthController::class, 'callback']);

        Route::middleware(['auth:sanctum', 'active'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
                ->middleware(['signed', 'throttle:verification'])
                ->name('verification.verify');
            Route::post('/email/verification-notification', [EmailVerificationController::class, 'notification'])
                ->middleware('throttle:verification')
                ->name('verification.send');
            Route::get('/email/verification-status', [EmailVerificationController::class, 'status']);
        });
    });

    Route::prefix('admin')->middleware(['auth:sanctum', 'active', 'admin'])->group(function () {
        Route::get('/dashboard', function (Request $request) {
            return response()->json([
                'message' => 'Panel administrador',
                'user' => $request->user(),
            ]);
        });

        Route::apiResource('projects', AdminProjectController::class);
        Route::apiResource('technologies', AdminTechnologyController::class);
        Route::get('/profile', [AdminProfileController::class, 'show']);
        Route::match(['put', 'patch'], '/profile', [AdminProfileController::class, 'update']);
        Route::post('/profile/cv', [ProfileCvController::class, 'store']);
        Route::delete('/profile/cv', [ProfileCvController::class, 'destroy']);
        Route::apiResource('certificates', AdminCertificateController::class);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::patch('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
        Route::get('/messages', [AdminContactMessageController::class, 'index']);
        Route::get('/messages/{message}', [AdminContactMessageController::class, 'show']);
        Route::patch('/messages/{message}/read', [AdminContactMessageController::class, 'read']);
        Route::patch('/messages/{message}/unread', [AdminContactMessageController::class, 'unread']);
        Route::delete('/messages/{message}', [AdminContactMessageController::class, 'destroy']);
        Route::apiResource('experiences', AdminExperienceController::class);
        Route::apiResource('education', AdminEducationController::class);
        Route::apiResource('skills', AdminSkillController::class);
        Route::post('/projects/{project}/images', [ProjectImageController::class, 'store']);
        Route::delete('/projects/{project}/images/{image}', [ProjectImageController::class, 'destroy']);
        Route::get('/projects/{project}/updates', [AdminProjectUpdateController::class, 'index']);
        Route::post('/projects/{project}/updates', [AdminProjectUpdateController::class, 'store']);
        Route::get('/projects/{project}/updates/{update}', [AdminProjectUpdateController::class, 'show']);
        Route::patch('/projects/{project}/updates/{update}', [AdminProjectUpdateController::class, 'update']);
        Route::delete('/projects/{project}/updates/{update}', [AdminProjectUpdateController::class, 'destroy']);
    });

    Route::prefix('user')->middleware(['auth:sanctum', 'active'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'show']);
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{project}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{project}', [FavoriteController::class, 'destroy']);
        Route::get('/templates', [SavedTemplateController::class, 'index']);
        Route::post('/templates/{project}', [SavedTemplateController::class, 'store']);
        Route::delete('/templates/{project}', [SavedTemplateController::class, 'destroy']);
        Route::get('/following', [ProjectFollowController::class, 'index']);
        Route::post('/following/{project}', [ProjectFollowController::class, 'store']);
        Route::delete('/following/{project}', [ProjectFollowController::class, 'destroy']);
        Route::get('/project-updates', [UserProjectUpdateController::class, 'index']);
        Route::patch('/password', [ChangePasswordController::class, 'update']);
    });
});
