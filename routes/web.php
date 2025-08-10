<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MatrixController;
use App\Http\Controllers\TrainingMatrixController;




use Illuminate\Routing\Middleware\ThrottleRequests;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login.login');
});


Route::get('/memory', function () {
    echo 'Penggunaan Memori: ' . memory_get_usage() . ' bytes';
});


// Login Route
Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout.post');
Route::get('/test-pdf', [FormController::class, 'showpdf'])->name('show.pdf');
Route::post('/test-pdf', [FormController::class, 'testpdf'])->name('testpdf');
// Route AllRole
Route::middleware(['auth:web'])->group(function () {
    // Dashboard Employee Training Record
    Route::get('/EmployeeTrainingRecord_list', [EmployeeController::class, 'index'])->name('dashboard.employee');
    Route::get('Training-record/public/employee/{id}', [EmployeeController::class, 'show'])->name('employee.show');

    // Dashboard Summary Training Record
    Route::get('/SummaryTrainingRecord_list', [SummaryController::class, 'index'])->name('dashboard.summary');
    Route::get('Training-record/public/summary/{id}', [SummaryController::class, 'show'])->name('summary.show');

    // Dashboard Master Data Employee
    Route::get('/Employee/dashboard', [PesertaController::class, 'index'])->name('dashboard.peserta');

    // Dashboard Training Matrix
    Route::get('/matrix/dashboard', [MatrixController::class, 'index'])->name('matrix.index');

    // Dashboard Production Competency Training Matrix
    Route::get('/training-matrix/dashboard', [TrainingMatrixController::class, 'index'])->name('training-matrix.index');

   

    // Search SummaryTraining Record
    Route::post('/api/trainings/search', [SummaryController::class, 'search']);


    // API download pdf summary
    Route::get('/summary/download/{id}', [SummaryController::class, 'downloadSummaryPdf'])->name('download.summary');
    Route::get('/employee/download/{id}', [EmployeeController::class, 'downloadPdf'])->name('download.employee');
});

// Super Admin Route
Route::middleware(['auth', 'role:Super Admin,Admin'])->group(function () {

    Route::get('/index', [FormController::class, 'index'])->name('dashboard.index');

    // Crud Form
    Route::get('/index/create', [FormController::class, 'create'])->name('dashboard.create');
    Route::post('/index/create/store', [FormController::class, 'store'])->name('dashboard.store');
    Route::get('/index/edit/{id}', [FormController::class, 'edit'])->name('dashboard.edit');
    Route::put('/index/update/{id}', [FormController::class, 'update'])->name('dashboard.update');


    // Crud Peserta
    Route::get('/Employee/New_Employee/', [PesertaController::class, 'create'])->name('peserta.create');
    Route::post('/Employee/New_Employee/store', [PesertaController::class, 'store'])->name('peserta.store');
    Route::get('/Employee/edit/{peserta}', [PesertaController::class, 'edit'])->name('peserta.edit');
    Route::put('/Employee/update/{peserta}', [PesertaController::class, 'update'])->name('peserta.update');
    Route::delete('/Employee/delete/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');


    // API Search Peserta Form
    Route::get('/participants/{badgeNo}', [PesertaController::class, 'getParticipantByBadgeNo']);

    Route::get('/get-job-skill/{skillCode}', [FormController::class, 'getJobSkill']);


    Route::get('/training-record/{id}', [FormController::class, 'show'])->name('dashboard.show');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/User/New_User', [UserController::class, 'store'])->name('user.store');
    Route::get('/User/dashboard', [UserController::class, 'index'])->name('user.index');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/user/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/update/{user}', [UserController::class, 'update'])->name('user.update');
});




// Super Admin Route
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    // Route Import Export Peserta
    Route::post('/users/import', [ExcelController::class, 'import_peserta'])->name('import.peserta');
    Route::get('/users/expor', [ExcelController::class, 'export_peserta'])->name('export.peserta');

    // Route Import Export Training
    Route::post('/training/import', [ExcelController::class, 'import_training'])->name('import.training');
    Route::get('/training/export', [ExcelController::class, 'export_training'])->name('export.training');

    // Route Export Matrix
    Route::get('/matrix/export', [ExcelController::class, 'export_matrix'])->name('export.matrix');

    // Route Export Training Matrix
    Route::get('/training-matrix/export', [ExcelController::class, 'export_training_matrix'])->name('export.training-matrix');

    // Route Export Training Request
    Route::get('/training-request/export', [ExcelController::class, 'export_training_request'])->name('export.training-request');

    Route::delete('/index/{id}', [FormController::class, 'destroy'])->name('dashboard.destroy');

    // Route Matrix
    Route::get('/matrix/{id}', [MatrixController::class, 'show'])->name('matrix.show');
    Route::put('/matrix/update/{id}', [MatrixController::class, 'updateLicense'])->name('matrix.update');


    // Route Training Matrix
    Route::put('/training-record/{id}/comment', [FormController::class, 'updateComment'])->name('update.comment');
    Route::get('/training-matrix/downloadpdf', [TrainingMatrixController::class, 'downloadpdf'])->name('download.pdf');

    // Route Job Skill
    Route::post('/job-skill/create', [FormController::class, 'jobs_skill_store'])->name('jobs_skill.store');
    Route::delete('/job-skill/delete/{id}', [FormController::class, 'jobs_skill_destroy'])->name('jobs_skill.destroy');

    
});
