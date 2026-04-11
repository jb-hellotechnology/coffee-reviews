<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoasterController;

// Public routes
Route::get('/', [VenueController::class, 'index'])->name('venues.index');
Route::get('/map', [VenueController::class, 'map'])->name('venues.map');
Route::get('/coffee-shops/{city}', [VenueController::class, 'city'])->name('venues.city');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/sitemap.xml', function () {
    $venues   = App\Models\Venue::all();
    $cities   = App\Models\Venue::select('city')->distinct()->pluck('city');
    $roasters = App\Models\Roaster::all();

    return response()->view('sitemap', compact('venues', 'cities', 'roasters'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::get('/roasters', [RoasterController::class, 'index'])->name('roasters.index');
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

// Auth required
Route::middleware('auth')->group(function () {
    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Venues — create must come before {venue} to avoid slug conflict
    Route::get('/venues/create', [VenueController::class, 'create'])->name('venues.create');
    Route::get('/venues/{venue}/edit', [VenueController::class, 'edit'])->name('venues.edit');
    Route::patch('/venues/{venue}', [VenueController::class, 'update'])->name('venues.update');

    // Reviews
    Route::get('/venues/{venue}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::get('/my-reviews', [ReviewController::class, 'myReviews'])->name('my.reviews');

    // Roasters
    Route::get('/roasters/create', [RoasterController::class, 'create'])->name('roasters.create');
    Route::get('/roasters/{roaster}/edit', [RoasterController::class, 'edit'])->name('roasters.edit');
    Route::patch('/roasters/{roaster}', [RoasterController::class, 'update'])->name('roasters.update');
});

Route::get('/roasters/{roaster}', [RoasterController::class, 'show'])->name('roasters.show');
Route::get('/venues/{venue}', [VenueController::class, 'show'])->name('venues.show');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::get('/venues', [AdminController::class, 'venues'])->name('venues');
    Route::post('/venues/{venue}/verify', [AdminController::class, 'verifyVenue'])->name('venues.verify');
    Route::delete('/venues/{venue}', [AdminController::class, 'deleteVenue'])->name('venues.delete');

    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{review}/approve', [AdminController::class, 'approveReview'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [AdminController::class, 'deleteReview'])->name('reviews.delete');

    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}', [AdminController::class, 'banUser'])->name('users.ban');

    Route::get('/roasters', [AdminController::class, 'roasters'])->name('roasters');
});

require __DIR__.'/auth.php';
