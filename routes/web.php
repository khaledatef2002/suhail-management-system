<?php

use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\Front\InternShipController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require_once __DIR__ . '/dashboard.php';

Route::prefix(LaravelLocalization::setLocale())->middleware(['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'])->group(function(){
    require_once __DIR__ . '/auth.php';
    Route::name('front.')->group(function(){
        Route::get('internship-apply', [InternShipController::class, 'index'])->name('internship-apply');
        Route::post('internship-apply', [InternShipController::class, 'store'])->name('internship-apply');
    });
});