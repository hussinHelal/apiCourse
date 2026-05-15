<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PersonalTokenController;

// Route::get('/', function () {
//     return redirect('/api/user');
// });
Route::get('/', function() { return view('welcome');})->middleware('guest');

// Auth::routes();

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes
Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Password Confirmation Routes
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/personal-tokens', [PersonalTokenController::class, 'index'])->name('tokens.index');
    Route::get('/authorized-clients', [PersonalTokenController::class, 'authorizedClients'])->name('authorizedClients');
    Route::get('/authorized-clients/{tokenId}', [PersonalTokenController::class, 'revokeClient'])->name('authorizedClients.destroy');
    Route::post('/personal-tokens', [PersonalTokenController::class, 'store'])->name('tokens.store');
    Route::delete('/personal-tokens/{tokenId}', [PersonalTokenController::class, 'destroy'])->name('tokens.destroy');

    Route::get('/list-clients', [PersonalTokenController::class, 'clients'])->name('clients-list');
    Route::post('/store-clients', [PersonalTokenController::class, 'storeClient'])->name('clients-store');
    Route::put('/update-clients/{clientId}', [PersonalTokenController::class, 'updateClient'])->name('clients-update');
    Route::delete('/destroy-clients/{clientId}', [PersonalTokenController::class, 'destroyClient'])->name('clients-destroy');
    Route::put('/secret-clients/{clientId}/secret', [PersonalTokenController::class, 'regenerateSecret'])->name('clients-secret');

    Route::get('/callback',function(){
        return view('callback');
    })->name('callback');
});
