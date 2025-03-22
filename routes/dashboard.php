<?php

use App\Helpers\select2;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\InternShipRequestsController;
use App\Http\Controllers\Dashboard\NotificationsController;
use App\Http\Controllers\Dashboard\SystemSettingsController;
use App\Http\Controllers\Dashboard\TaskMessagesController;
use App\Http\Controllers\Dashboard\TasksController;
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


    Route::controller(SystemSettingsController::class)->group(function(){
        Route::get('/system-settings', 'edit')->name('system-settings');
        Route::put('/system-settings', 'update')->name('system-settings');
    });

    Route::resource('tasks', TasksController::class);

    Route::prefix('/select2')->controller(select2::class)->name('select2.')->group(function(){
        Route::get('/users', 'users')->name('users');
    });

    Route::put('/tasks/{task}/update-status', [TasksController::class, 'set_status'])->name('tasks.update-status');

    Route::resource('task-messages', TaskMessagesController::class);
    
    Route::get('/notification/{notification}', [NotificationsController::class, 'display'])->name('notification.display');
});