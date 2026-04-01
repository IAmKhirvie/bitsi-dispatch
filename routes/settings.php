<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', function () {
        return view('settings.profile');
    })->name('profile.edit');

    // Redirect old password route to profile page (password is now on the same page)
    Route::redirect('settings/password', 'settings/profile')->name('password.edit');

    Route::get('settings/appearance', function () {
        return view('settings.appearance');
    })->name('appearance');
});
