<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FacilityImageController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\PaymentPerMonthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyFacilityController;
use App\Http\Controllers\PropertyImageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('landing.index');
});


Route::get('/login', function () {
    return view('auth.login')->name('login');
});
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register')->name('register');
});

// routes/web.php
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::post('/submit-feedback', [FeedbackController::class, 'submit'])->name('feedback.submit');
Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

Auth::routes();

Route::get('/', [LandingController::class, 'index'])->name('home.index');
Route::get('/properties/home', [LandingController::class, 'home'])->name('home.properties'); // aa
Route::get('/properties/show/{id}', [LandingController::class, 'show'])->name('home.show');


Route::middleware(['auth', 'role:tenant|admin|super_admin'])->group(function () {

    Route::get('users/profile/{user}', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/user/update/{user}', [UserController::class, 'update'])->name('user.update');

    Route::get('/user/history/{user}', [UserController::class, 'history'])->name('user.history');
    // profile change
    Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('profile.changePassword');

});


// Route::middleware(['auth', 'role:admin|super_admin'])->group(function () {
//     //facility image controller
//     Route::resource('facility_images', FacilityImageController::class);
//     //property images
//     Route::resource('property_images', PropertyImageController::class);
//     // facilities
//     Route::resource('facilities', FacilityController::class);
//     Route::resource('properties', PropertyController::class);
//     // dashboard
//     Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
//     // profile change
//     Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('profile.changePassword');
//     //users
//     Route::get('/users', [UserController::class, 'index'])->name('user.index');
//     Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
//     Route::get('users/show/{user}', [UserController::class, 'show'])->name('user.show');
// });


Route::middleware(['auth', 'role:admin|super_admin'])->group(function () {
    // properties add leader
    Route::post('/properties/addPropertyLeader',  [PropertyController::class, 'addPropertyLeader'])->name('properties.addPropertyLeader');
    Route::post('/properties/{property}/editPropertyLeader',  [PropertyController::class, 'editPropertyLeader'])->name('properties.editPropertyLeader');
    // property_facilities
    Route::resource('property_facilities', PropertyFacilityController::class);
    // leases done
    Route::put('/leases/{lease}/done', [LeaseController::class, 'done'])->name('leases.done');
    // leases
    Route::resource('leases', LeaseController::class);
    // payments
    Route::resource('payments', PaymentPerMonthController::class);
    // users

    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/accept/{user}', [UserController::class, 'accept'])->name('user.accept');
    Route::post('/user/reject/{user}', [UserController::class, 'reject'])->name('user.reject');
    Route::post('/user/dismissHeadLease/{user}', [UserController::class, 'deletePropertyLeader'])->name('user.dismissHeadLease');
    //instance
    Route::resource('instance', InstanceController::class);

    //facility image controller
    Route::resource('facility_images', FacilityImageController::class);
    //property images
    Route::resource('property_images', PropertyImageController::class);
    // facilities
    Route::resource('facilities', FacilityController::class);
    Route::resource('properties', PropertyController::class);
    // dashboard
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
    // profile change
    //users
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('users/show/{user}', [UserController::class, 'show'])->name('user.show');

    Route::delete('/property-images/delete-selected', [PropertyImageController::class, 'destroySelected'])->name('property_images.destroySelected');
    Route::delete('/facility-images/destroy-selected', [FacilityImageController::class, 'destroySelected'])->name('facility_images.destroySelected');
});

// require_once __DIR__ . '/chandra.php';
// require_once __DIR__ . '/juhan.php';
// require_once __DIR__ . '/ridoq.php';
// require_once __DIR__ . '/sano.php';
// require_once __DIR__ . '/mugni.php';
