<?php

use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::name('dashboard.')->prefix(LaravelLocalization::setLocale() . '/dashboard')->middleware(['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','auth'])->group(function(){
    Route::get('/', [HomeController::class, 'index'])->name('index');

    Route::resource('users', UserController::class)->except('show');

    // Route::resource('roles', RolesController::class)->except('show');

    // Route::resource('tenants', TenantsController::class)->except('show');
});