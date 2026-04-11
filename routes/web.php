<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\ShowSearch;
use App\Livewire\ShowDetail;

Route::get('/', Dashboard::class)->name('home');
Route::get('/search', ShowSearch::class)->name('search');
Route::get('/show/{show}', ShowDetail::class)->name('show');