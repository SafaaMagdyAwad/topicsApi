<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\MessageController;
use App\Http\Controllers\Api\Admin\TestimonialController;
use App\Http\Controllers\Api\Admin\TopicController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\PublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(PublicController::class)->group(function () {
    Route::get('index', 'index')->name('index');
    Route::get('testimonials', 'testimonials')->name('testimonials');
    Route::get('topics-listing', 'topicsListing')->name('topicsListing');
    Route::get('topics-detail/{id}', 'topicsDetail')->name('topicsDetail');
    Route::get('contact', 'contact')->name('contact');
    Route::post('contact', 'sendContactMessage')->name('sendContactMessage');
    Route::post('search', 'search')->name('search'); //search by category to get topics
    Route::put('readTopic/{id}', 'readTopic')->name('readTopic');
    Route::post('newsletter', 'newsletter')->name('newsletter');
});
Route::post('register',[RegisterController::class,'register']);
Route::post('login',[LoginController::class,'login']);

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::post('logout',[LogoutController::class,'logout']);
    Route::resource('category', CategoryController::class)->except(['show']);
    Route::prefix('message')->name('message')->group(function () {
        Route::controller(MessageController::class)->group(function () {
            Route::get('index', 'index')->name('.index');
            Route::put('{message}/read', 'read')->name('.read');
            Route::delete('{message}/destroy', 'destroy')->name('.destroy');
        });
    });
    Route::resource('user', UserController::class)->except(['show']);
    Route::resource('topic', TopicController::class);
    Route::resource('testimonial', TestimonialController::class);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
