<?php

use App\Http\Controllers\AddressSubmissionController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ChurchAdministrationContactController;
use App\Http\Controllers\CoinOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeclarationAttachmentController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicDeclarationController;
use App\Http\Controllers\RoomReservationController;
use App\Http\Controllers\PrivacyConsentController;
use App\Http\Controllers\SolidarityFundAuthorizationController;
use App\Http\Controllers\ZaaierAuthorizationController;
use App\Http\Controllers\YouTubeVideoController;
use Inertia\Inertia;
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

Route::middleware(['auth', 'user.not.blocked'])->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// auth routes

// Add this route after any other specific routes to catch all page slugs
Route::get('nieuws', [NewsController::class, 'index'])->name('news.index');
Route::get('nieuws/{news}', [NewsController::class, 'show'])->name('news.show');

Route::get('agenda', [PageController::class, 'agenda'])->name('agenda');
Route::get('live', [PageController::class, 'live'])->name('live');
Route::get('audio/{service}', [PageController::class, 'streamAudio'])->name('audio.stream');

// Download YouTube video (admin only)
Route::middleware(['auth', 'user.not.blocked'])->group(function () {
    Route::post('services/{service}/download-video', [PageController::class, 'downloadVideo'])->name('services.download-video');
});

// Public Declaration Routes
Route::get('declaratie', [PublicDeclarationController::class, 'create'])->name('public-declarations.create');
Route::post('declaratie', [PublicDeclarationController::class, 'store'])->name('public-declarations.store');
Route::get('declaratie/bedankt', [PublicDeclarationController::class, 'success'])->name('public-declarations.success');

// Contact Routes
Route::get('contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('contact', [ContactController::class, 'store'])->name('contact.store');

// Church Administration Contact Routes
Route::get('kerkelijke-administratie', [ChurchAdministrationContactController::class, 'show'])->name('church-administration-contact.show');
Route::post('kerkelijke-administratie', [ChurchAdministrationContactController::class, 'store'])->name('church-administration-contact.store');

// YouTube OAuth callback route
Route::get('youtube/oauth/callback', function () {
    $code = request('code');
    $error = request('error');

    if ($error) {
        return response()->json(['error' => 'OAuth authorization failed: ' . $error], 400);
    }

    if (!$code) {
        return response()->json(['error' => 'No authorization code provided'], 400);
    }

    try {
        $youtubeService = app(\App\Services\YouTubeService::class);
        $token = $youtubeService->completeOAuthFlow($code);

        return response()->json([
            'message' => 'YouTube OAuth authentication successful!',
            'token_saved' => true
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Authentication failed: ' . $e->getMessage()], 500);
    }
})->name('youtube.oauth.callback');
// Agenda routes
Route::get('api/agenda/items', [AgendaController::class, 'getItems'])->name('agenda.items');

require __DIR__ . '/auth.php';

// auth routes
Route::middleware(['auth', 'verified', 'user.not.blocked'])->group(function () {
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

    // Room Reservation Routes
    Route::get('room-reservations', [RoomReservationController::class, 'index'])->name('room-reservations.index');
    Route::get('room-reservations/create', [RoomReservationController::class, 'create'])->name('room-reservations.create');
    Route::post('room-reservations', [RoomReservationController::class, 'store'])->name('room-reservations.store');
    Route::get('room-reservations/{roomReservation}', [RoomReservationController::class, 'show'])->name('room-reservations.show');
    Route::get('room-reservations/{roomReservation}/edit', [RoomReservationController::class, 'edit'])->name('room-reservations.edit');
    Route::put('room-reservations/{roomReservation}', [RoomReservationController::class, 'update'])->name('room-reservations.update');
    Route::delete('room-reservations/{roomReservation}', [RoomReservationController::class, 'destroy'])->name('room-reservations.destroy');
    Route::get('api/room-reservations/available-rooms', [RoomReservationController::class, 'getAvailableRooms'])->name('room-reservations.available-rooms');

    // Solidarity Fund Authorization Routes
    Route::get('solidarity-fund-authorizations', [SolidarityFundAuthorizationController::class, 'index'])->name('solidarity-fund-authorizations.index');
    Route::get('solidarity-fund-authorizations/create', [SolidarityFundAuthorizationController::class, 'create'])->name('solidarity-fund-authorizations.create');
    Route::post('solidarity-fund-authorizations', [SolidarityFundAuthorizationController::class, 'store'])->name('solidarity-fund-authorizations.store');
    Route::get('solidarity-fund-authorizations/{solidarityFundAuthorization}', [SolidarityFundAuthorizationController::class, 'show'])->name('solidarity-fund-authorizations.show');

    // Zaaier Authorization Routes
    Route::get('zaaier-authorizations', [ZaaierAuthorizationController::class, 'index'])->name('zaaier-authorizations.index');
    Route::get('zaaier-authorizations/create', [ZaaierAuthorizationController::class, 'create'])->name('zaaier-authorizations.create');
    Route::post('zaaier-authorizations', [ZaaierAuthorizationController::class, 'store'])->name('zaaier-authorizations.store');
    Route::get('zaaier-authorizations/{zaaierAuthorization}', [ZaaierAuthorizationController::class, 'show'])->name('zaaier-authorizations.show');

    // Privacy Consent Routes
    Route::get('privacy-consents', [PrivacyConsentController::class, 'index'])->name('privacy-consents.index');
    Route::get('privacy-consents/create', [PrivacyConsentController::class, 'create'])->name('privacy-consents.create');
    Route::post('privacy-consents', [PrivacyConsentController::class, 'store'])->name('privacy-consents.store');
    Route::get('privacy-consents/{privacyConsent}', [PrivacyConsentController::class, 'show'])->name('privacy-consents.show');
});

// Public route for cancellation (with signed URL)
Route::get('room-reservations/{roomReservation}/cancel', [RoomReservationController::class, 'cancel'])
    ->middleware('signed')
    ->name('room-reservations.cancel');

// this is the last route to catch all page slugs
Route::get('{slug}', [PageController::class, 'show'])->name('page.show');
