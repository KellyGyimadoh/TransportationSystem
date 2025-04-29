<?php

use App\Livewire\Bookings;
use App\Livewire\PaymentsPage;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\TicketsPage;
use App\Livewire\UserBookings;
use App\Livewire\WelcomePage;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomePage::class)->name('home');

Route::get('/about',function(){
    return view('about.index');
});
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    //bookings
    Route::get('/bookings', Bookings::class)->name('bookings');
    Route::get('/mybookings', UserBookings::class)->name('mybookings');
    Route::get('/pay/{bookings}', PaymentsPage::class)->name('payment');
    Route::get('/tickets/{bookings}',TicketsPage::class)->name('tickets');
});

require __DIR__.'/auth.php';
