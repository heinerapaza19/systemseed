<?php

use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\Admin\UploadController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\FrontendController;
use App\Http\Controllers\Web\GoogleController;
use App\Http\Controllers\Web\PasswordController;
use App\Http\Controllers\Web\SeoController;
use App\Http\Controllers\Web\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'home'])->name('home');
Route::get('/proyectos', [FrontendController::class, 'projects'])->name('projects.index');
Route::get('/proyectos/{slug}', [FrontendController::class, 'project'])->name('projects.show');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

// Enlace del correo de verificación (no requiere sesión abierta)
Route::get('/verificar-correo/{id}/{hash}', VerifyEmailController::class)
    ->middleware('throttle:verification')
    ->name('verification.web');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::get('/registro', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->middleware('throttle:auth')->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->middleware('throttle:auth')->name('google.callback');
    Route::get('/recuperar-contrasena', [PasswordController::class, 'forgotForm'])->name('password.request');
    Route::post('/recuperar-contrasena', [PasswordController::class, 'forgot'])->middleware('throttle:password-reset')->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [PasswordController::class, 'resetForm'])->name('password.reset');
    Route::post('/restablecer-contrasena', [PasswordController::class, 'reset'])->middleware('throttle:password-reset')->name('password.update');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('mi-cuenta')->name('user.')->group(function () {
        Route::get('/', [FrontendController::class, 'dashboard'])->name('dashboard');
        Route::get('/favoritos', [FrontendController::class, 'favorites'])->name('favorites');
        Route::get('/plantillas', [FrontendController::class, 'templates'])->name('templates');
        Route::get('/siguiendo', [FrontendController::class, 'following'])->name('following');
        Route::get('/novedades', [FrontendController::class, 'updates'])->name('updates');
        Route::get('/perfil', [FrontendController::class, 'profile'])->name('profile');
        Route::patch('/perfil', [AccountController::class, 'update'])->name('profile.update');
        Route::view('/explorar', 'user.explore')->name('explore');
        Route::view('/seguridad', 'user.security')->name('security');
        Route::view('/contacto', 'user.contact')->name('contact');
    });
});

Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [FrontendController::class, 'admin'])->name('dashboard');
    Route::get('/mensajes', [FrontendController::class, 'adminMessages'])->name('messages');
    Route::get('/configuracion', [FrontendController::class, 'profile'])->name('settings');
    Route::post('/uploads', [UploadController::class, 'store'])->middleware('throttle:30,1')->name('uploads.store');

    // Gestión visual del contenido (las vistas consumen /api/v1/admin/*)
    Route::view('/proyectos', 'admin.projects')->name('projects');
    Route::view('/novedades', 'admin.updates')->name('updates');
    Route::view('/tecnologias', 'admin.technologies')->name('technologies');
    Route::view('/certificados', 'admin.certificates')->name('certificates');
    Route::view('/perfil', 'admin.profile')->name('profile');
    Route::view('/experiencia', 'admin.experiences')->name('experiences');
    Route::view('/educacion', 'admin.education')->name('education');
    Route::view('/skills', 'admin.skills')->name('skills');
    Route::view('/usuarios', 'admin.users')->name('users');
});
