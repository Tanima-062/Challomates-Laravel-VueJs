<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ForgotPasswordController;





//Employee
Route::get('mobile/password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('mobile.password.reset');
Route::post('mobile/password/reset', [ForgotPasswordController::class, 'reset'])->name('mobile.password.update');
