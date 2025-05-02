<?php

use App\Http\Controllers\AccomplishmentReportController;
use App\Http\Controllers\GanttController;
use App\Livewire\PermissionManager;
use App\Livewire\POWFormTest;
use App\Livewire\POWReportTest;
use App\Livewire\ProjectFormTest;
use App\Livewire\ProjectManager;
use App\Livewire\ProjectReportTest;
use App\Livewire\RoleManager;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

use App\Livewire\Constructions;
use App\Livewire\ConstructionView;
use App\Livewire\Repairs;
use App\Livewire\Fabrications;
use App\Livewire\PMUStaff;


Route::get('/create-symlink', function () {
    $target = '../storage/app/public';
    $link = 'public/storage';

    // ln -s ../storage/app/public public/storage   

    if (file_exists($link)) {
        return 'The "public/storage" directory already exists.';
    }

    $output = null;
    $return_var = null;
    exec("ln -s $target $link", $output, $return_var);

    if ($return_var !== 0) {
        return 'Failed to create symbolic link.';
    }

    return 'Symbolic link created successfully.';
});


Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/pmu-staffs', PMUStaff::class)->middleware(['auth', 'verified'])->name('pmu-staffs');
// Route::view('/users', 'user-management')->middleware(['auth', 'verified'])->name('users');
Route::get('/users', function () { // <-- No need to inject Request anymore

    if (Auth::user()->hasAnyRole(['admin', 'staff'])) {
        return view('user-management');
    }
    return redirect('/');

})->middleware(['auth', 'verified'])
    ->name('users');


// Route::view('/constructions/view', 'construction-view')->middleware(['auth', 'verified'])->name('construction-view');
Route::get('/project/view', function () {
    $user = Auth::user();
    $project = Project::find(request()->query('id'));

    $hasAccess = $user && $user->projects()->where('project_id', $project->id)->exists();

    if ($hasAccess || Auth::user()->hasAnyRole(['admin', 'staff'])) {
        return view('project-view', ['project' => $project]);
    } else {
        return redirect('/');
    }

})->middleware(['auth', 'verified'])->name('project-view');

Route::get('/role-manager', RoleManager::class)->middleware(['auth', 'verified'])->name('role-manager');
Route::get('/permission-manager', PermissionManager::class)->middleware(['auth', 'verified'])->name('permission-manager');

Route::view('/roles-and-permissions', 'roles-and-permissions')
    ->middleware(['auth', 'verified'])
    ->name('roles-and-permissions');

Route::view('/projects', 'projects')->middleware(['auth', 'verified'])->name('projects');

Route::view('/constructions', 'constructions')->middleware(['auth', 'verified'])->name('constructions');
Route::view('/repairs', 'repairs')->middleware(['auth', 'verified'])->name('repairs');
Route::view('/fabrications', 'fabrications')->middleware(['auth', 'verified'])->name('fabrications');

// Route::view('/gantt', 'gantt-chart-example')->middleware(['auth', 'verified'])->name('gantt');
// This route will handle URLs like /gantt/123
Route::get('/gantt/{projectId}', [GanttController::class, 'showGantt'])
    ->where('projectId', '[0-9]+') // Optional: Ensure projectId is numeric
    ->middleware(['auth', 'verified'])
    ->name('gantt.show'); // Name remains the same, but now expects a parameter


Route::get('/project/create/{category?}', ProjectFormTest::class)->middleware(['auth', 'verified'])->name('create-project');
Route::get('/project/edit/{id}/{category?}', ProjectFormTest::class)->middleware(['auth', 'verified'])->name('edit-project');

Route::get('/pow/create/{projectId}', POWFormTest::class)->middleware(['auth', 'verified'])->name('create-pow');
Route::get('/pow/edit/{projectId}/{powId}', POWFormTest::class)->middleware(['auth', 'verified'])->name('edit-pow');

// Route::view('/project-form/{id}', 'project-form')->middleware(['auth', 'verified'])->name('project-form');
Route::get('/project-form/{id}', ProjectReportTest::class)->middleware(['auth', 'verified'])->name('project-form');
// Route::get('/project-form/{id}', ProjectReportTest::class)->middleware(['auth', 'verified'])->name('pow-form');


Route::get('/accomplishment-reports/{report}/documentation', [AccomplishmentReportController::class, 'showDocumentation'])
    ->middleware(['auth', 'verified'])
    ->name('accomplishment.documentation.show');

// Uses route model binding for DocumentationUpload
Route::patch('/documentation-uploads/{upload}/toggle-approval', [AccomplishmentReportController::class, 'toggleApproval'])
    ->name('documentation.toggle-approval');

Route::delete('/documentation-uploads/{upload}', [AccomplishmentReportController::class, 'destroyDocumentation'])
    ->name('documentation.destroy');

Route::view('/individual-pow/{id}', 'view-individual-pow-form')->middleware(['auth', 'verified'])->name('individual-pow-form');
Route::get('/individual-pow-form/{powId}', POWReportTest::class)->middleware(['auth', 'verified'])->name('pow-form');


require __DIR__ . '/auth.php';
