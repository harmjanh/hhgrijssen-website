<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

$redirects = [
    'home' => '/',
];

foreach ($redirects as $old => $new) {
    Route::get($old, function () use ($new) {
        return redirect($new);
    });
}

Route::get('/', [PageController::class, 'home'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// auth routes

// Add this route after any other specific routes to catch all page slugs
Route::get('nieuws', [NewsController::class, 'index'])->name('news.index');
Route::get('nieuws/{news}', [NewsController::class, 'show'])->name('news.show');

require __DIR__ . '/auth.php';

// auth routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Declaration routes
    Route::get('declarations', [DeclarationController::class, 'index'])->name('declarations.index');
    Route::get('declarations/create', [DeclarationController::class, 'create'])->name('declarations.create');
    Route::post('declarations', [DeclarationController::class, 'store'])->name('declarations.store');
    Route::get('declarations/{declaration}', [DeclarationController::class, 'show'])->name('declarations.show');
});

// this is the last route to catch all page slugs
Route::get('{slug}', [PageController::class, 'show'])->name('page.show');
