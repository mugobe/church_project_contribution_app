<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ProjectController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect()->route('login'));
Route::get('/projects', fn() => view('public.project-wall'))->name('public.projects');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Auth::routes(['register' => false]); // disable self-registration, admin creates members

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    // Members
    Route::resource('members', MemberController::class);

    // Projects (controller coming soon)
     Route::resource('projects', ProjectController::class);
    Route::post('projects/{project}/generate-allocations', [ProjectController::class, 'generateAllocations'])->name('projects.generate-allocations');
    Route::post('projects/{project}/update-allocation', [ProjectController::class, 'updateAllocation'])->name('projects.update-allocation');
    Route::post('projects/{project}/import-allocations', [ProjectController::class, 'importAllocations'])->name('projects.import-allocations');

    // Contributions (controller coming soon)
  Route::resource('contributions', ContributionController::class)->except(['edit', 'update']);

    // Reports (controller coming soon)
    // Route::get('reports', [ReportController::class, 'index'])->name('reports.index');


    // inside admin group
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
    Route::get('/dashboard', fn() => view('member.dashboard'))->name('dashboard');
    Route::get('/projects', fn() => view('member.projects'))->name('projects');
    Route::get('/contributions', fn() => view('member.contributions'))->name('contributions');
});