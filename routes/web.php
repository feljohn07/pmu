<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Constructions;
use App\Livewire\ConstructionView;
use App\Livewire\Repairs;
use App\Livewire\Fabrications;
use App\Livewire\PMUStaff;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/constructions', Constructions::class)->middleware(['auth', 'verified'])->name('constructions');
Route::get('/constructions/view', ConstructionView::class)->middleware(['auth', 'verified'])->name('view-constructions');
Route::get('/repairs', Repairs::class)->middleware(['auth', 'verified'])->name('repairs');
Route::get('/fabrications', Fabrications::class)->middleware(['auth', 'verified'])->name('fabrications');
Route::get('/pmu-staffs', PMUStaff::class)->middleware(['auth', 'verified'])->name('pmu-staffs');

require __DIR__ . '/auth.php';
