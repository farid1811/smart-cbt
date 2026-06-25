<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Peserta;

// ─── Halaman utama ──────────────────────────────────────────────────────────
Route::get('/', [App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// ─── Auth ───────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/admin/login',  [LoginController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Area Admin ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Data Peserta
    Route::resource('peserta', Admin\PesertaController::class)->except(['show'])->parameters(['peserta' => 'peserta']);
    Route::post('peserta/{peserta}/reset-password', [Admin\PesertaController::class, 'resetPassword'])->name('peserta.resetPassword');
    Route::post('peserta/{peserta}/toggle-status',  [Admin\PesertaController::class, 'toggleStatus'])->name('peserta.toggleStatus');

    // Modul Pembelajaran
    Route::resource('modules', Admin\ModuleController::class)->except(['show']);

    // Kategori & Hierarki (tanpa SubKategori)
    Route::get('api/groups', [Admin\CategoryController::class, 'getGroups'])->name('api.groups');
    Route::get('api/codes/{groupId}', [Admin\CategoryController::class, 'getCodesByGroup'])->name('api.codesByGroup');
    Route::get('api/categories/{codeId}', [Admin\CategoryController::class, 'getCategoriesByCode'])->name('api.categoriesByCode');

    Route::post('categories/code', [Admin\CategoryController::class, 'storeCode'])->name('categories.storeCode');
    Route::put('categories/code/{code}', [Admin\CategoryController::class, 'updateCode'])->name('categories.updateCode');
    Route::delete('categories/code/{code}', [Admin\CategoryController::class, 'destroyCode'])->name('categories.destroyCode');

    Route::resource('categories', Admin\CategoryController::class)->except(['show']);

    // Bank Soal
    Route::post('questions/bulk-delete', [Admin\QuestionController::class, 'bulkDelete'])->name('questions.bulkDelete');
    Route::post('questions/delete-by-category', [Admin\QuestionController::class, 'deleteByCategory'])->name('questions.deleteByCategory');
    Route::resource('questions', Admin\QuestionController::class)->except(['show']);

    // Paket Tryout & Import Soal per Paket
    Route::resource('tryouts', Admin\TryoutPackageController::class);
    Route::get('tryouts/{tryout}/seb-config',       [Admin\TryoutPackageController::class, 'downloadSebConfig'])->name('tryouts.sebConfig');
    Route::post('tryouts/{tryout}/add-question',    [Admin\TryoutPackageController::class, 'addQuestion'])->name('tryouts.addQuestion');
    Route::post('tryouts/{tryout}/remove-question', [Admin\TryoutPackageController::class, 'removeQuestion'])->name('tryouts.removeQuestion');

    // Import Soal — terintegrasi ke dalam Detail Paket
    Route::get('tryouts/{tryout}/import',             [Admin\ImportController::class, 'showForm'])->name('tryouts.import.form');
    Route::post('tryouts/{tryout}/import-word',       [Admin\ImportController::class, 'wordPreview'])->name('tryouts.import.word');
    Route::post('tryouts/{tryout}/import-pdf',        [Admin\ImportController::class, 'pdfPreview'])->name('tryouts.import.pdf');
    Route::post('tryouts/{tryout}/import-confirm',    [Admin\ImportController::class, 'confirm'])->name('tryouts.import.confirm');

    // Rekap Nilai
    Route::get('/rekap',        [Admin\RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/export', [Admin\RekapController::class, 'exportCsv'])->name('rekap.export');

    // Pengaturan — CMS Homepage
    Route::get('/pengaturan/homepage',  [Admin\HomepageController::class, 'index'])->name('homepage.index');
    Route::put('/pengaturan/homepage',  [Admin\HomepageController::class, 'update'])->name('homepage.update');

    // Pengaturan — Alumni CRUD
    Route::resource('alumni', Admin\AlumniController::class)->except(['show']);
});

// ─── Area Peserta ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'is_peserta'])->prefix('peserta')->name('peserta.')->group(function () {

    Route::get('/dashboard', [Peserta\DashboardController::class, 'index'])->name('dashboard');

    // Modul & Paket
    Route::get('/modules', [Peserta\DashboardController::class, 'modules'])->name('modules.index');
    Route::get('/modules/{module}', [Peserta\DashboardController::class, 'showModule'])->name('modules.show');
    Route::get('/drills', [Peserta\DashboardController::class, 'drills'])->name('drills.index');
    Route::get('/tryouts', [Peserta\DashboardController::class, 'tryouts'])->name('tryouts.index');

    // Riwayat Nilai
    Route::get('/riwayat', [Peserta\ResultController::class, 'index'])->name('results.index');

    // Ujian
    Route::get('/tryout/{package}/seb-config', [Peserta\ExamController::class, 'downloadSebConfig'])->name('exam.sebConfig');
    Route::get('/tryout/{package}/mulai',      [Peserta\ExamController::class, 'start'])->name('exam.start')->middleware('seb');
    Route::get('/ujian/{session}/{nomor}',     [Peserta\ExamController::class, 'show'])->name('exam.show')->middleware('seb');
    Route::post('/ujian/{session}/jawab',     [Peserta\ExamController::class, 'saveAnswer'])->name('exam.saveAnswer')->middleware('throttle:exam-ajax');
    Route::post('/ujian/{session}/ragu',      [Peserta\ExamController::class, 'toggleRagu'])->name('exam.toggleRagu')->middleware('throttle:exam-ajax');
    Route::post('/ujian/{session}/pelanggaran', [Peserta\ExamController::class, 'logViolation'])->name('exam.logViolation');
    Route::post('/ujian/{session}/submit',    [Peserta\ExamController::class, 'submit'])->name('exam.submit');
    Route::get('/hasil/{result}',             [Peserta\ExamController::class, 'result'])->name('exam.result');
});