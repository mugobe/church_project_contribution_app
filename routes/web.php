<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ContributionController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\PublicController;



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));
  Route::get('/projects', [PublicController::class, 'projectWall'])->name('public.projects');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Auth::routes(['register' => false]);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('members', MemberController::class);

    Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/generate-allocations', [ProjectController::class, 'generateAllocations'])->name('projects.generate-allocations');
    Route::post('projects/{project}/update-allocation', [ProjectController::class, 'updateAllocation'])->name('projects.update-allocation');
    Route::post('projects/{project}/import-allocations', [ProjectController::class, 'importAllocations'])->name('projects.import-allocations');

    Route::resource('contributions', ContributionController::class)->except(['edit', 'update']);
  

    Route::get('allocation-hint', function (Request $request) {
        $allocation = \App\Models\Allocation::where('member_id', $request->member_id)
            ->where('project_id', $request->project_id)
            ->first();

        $paid = $allocation ? \App\Models\Contribution::where('member_id', $request->member_id)
            ->where('project_id', $request->project_id)
            ->sum('amount') : 0;

        return response()->json([
            'allocation' => $allocation,
            'paid'       => $paid,
            'balance'    => $allocation ? max(0, $allocation->allocated_amount - $paid) : 0,
        ]);
    })->name('allocation-hint');
});

/*
|--------------------------------------------------------------------------
| Member Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('member')->name('member.')->middleware(['auth', 'member'])->group(function () {
    Route::get('/dashboard',     [MemberDashboardController::class, 'index'])->name('dashboard');
    Route::get('/projects',      [MemberDashboardController::class, 'projects'])->name('projects');
    Route::get('/contributions', [MemberDashboardController::class, 'contributions'])->name('contributions');
});