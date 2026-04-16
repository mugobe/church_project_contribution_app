<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MemberController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::resource('members', MemberController::class);
});

Route::prefix('member')->name('member.')->middleware(['auth', 'member'])->group(function () {
    Route::get('/dashboard', fn() => view('member.dashboard'))->name('dashboard');
    Route::get('/projects', fn() => view('member.projects'))->name('projects');
    Route::get('/contributions', fn() => view('member.contributions'))->name('contributions');
});

Route::get('/projects', fn() => view('public.project-wall'))->name('public.projects');