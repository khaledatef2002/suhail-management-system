<?php

use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\InternShipRequestsController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::name('dashboard.')->prefix(LaravelLocalization::setLocale() . '/dashboard')->middleware(['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','auth'])->group(function(){
    Route::get('/', [HomeController::class, 'index'])->name('index');

    Route::resource('users', UserController::class)->except('show');

    Route::resource('internship-requests', InternShipRequestsController::class)->except('show');

    Route::put('internship-request/{internship_request}/accept', [InternShipRequestsController::class, 'accept']);
    Route::put('internship-request/{internship_request}/accept_and_create', [InternShipRequestsController::class, 'accept_and_create']);
    Route::put('internship-request/{internship_request}/reject', [InternShipRequestsController::class, 'reject']);

    // Route::resource('roles', RolesController::class)->except('show');

    // Route::resource('tenants', TenantsController::class)->except('show');
});