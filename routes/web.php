<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $landingPage = \App\Models\LandingPage::first();
    return view('welcome', compact('landingPage'));
});

Route::get('/assessment/{token}', \App\Livewire\AssessmentForm::class)->name('assessment.form');

Route::get('/login', function () {
    return redirect('/koperasi/login');
})->name('login');

Route::get('/register', function () {
    return redirect('/koperasi/register');
})->name('register');
