<?php

use App\Http\Controllers\AddressSubmissionController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\CoinOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeclarationAttachmentController;
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

Route::get('agenda', [PageController::class, 'agenda'])->name('agenda');
// Agenda routes
Route::get('api/agenda/items', [AgendaController::class, 'getItems'])->name('agenda.items');

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

    // Declaration attachment routes
    Route::get('declarations/{declaration}/attachments/{attachment}/download', [DeclarationAttachmentController::class, 'download'])
        ->name('declarations.attachments.download');

    // Address Submission Routes
    Route::get('address-submissions/create', [AddressSubmissionController::class, 'create'])->name('address-submissions.create');
    Route::post('address-submissions', [AddressSubmissionController::class, 'store'])->name('address-submissions.store');
    Route::get('address-submissions/{submission}', [AddressSubmissionController::class, 'show'])->name('address-submissions.show');
    Route::get('address-submissions', [AddressSubmissionController::class, 'index'])->name('address-submissions.index');

    // Coin Order Routes
    Route::get('coin-orders/create', [CoinOrderController::class, 'create'])->name('coin-orders.create');
    Route::post('coin-orders', [CoinOrderController::class, 'store'])->name('coin-orders.store');
    Route::get('coin-orders/{coinOrder}/success', [CoinOrderController::class, 'success'])->name('coin-orders.success');
    Route::post('coin-orders/webhook', [CoinOrderController::class, 'webhook'])->name('coin-orders.webhook');
    Route::get('coin-orders', [CoinOrderController::class, 'index'])->name('coin-orders.index');
    Route::get('coin-orders/{coinOrder}/download', [CoinOrderController::class, 'download'])->name('coin-orders.download');
});

// this is the last route to catch all page slugs
Route::get('{slug}', [PageController::class, 'show'])->name('page.show');
