<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProofreaderController;
use App\Http\Controllers\RegionAdminController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SentenceController;
use App\Http\Controllers\TranslatorController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home')->middleware(['auth', 'verified', 'home']);

Route::get('/dashboard', [SentenceController::class, 'getRegionalStatistics'])->name('dashboard');

Route::get('/sentences', [SentenceController::class, 'index'])->name('sentences.index');
Route::get('/other-sentences', [SentenceController::class, 'otherSentences'])->name('otherSentences.index');
Route::post('/sentences/upload', [SentenceController::class, 'upload'])->name('sentences.upload');
Route::post('/other-sentences/upload', [SentenceController::class, 'otherSentencesUpload'])->name('otherSentencesUpload');
Route::get('/sentences/random', [SentenceController::class, 'getRandomSentence']);
Route::delete('/sentences/{sentence}', [SentenceController::class, 'destroy'])->name('sentences.destroy');
Route::post('/regions/{region}/export-sentences', [SentenceController::class, 'exportSentences'])
    ->name('regions.export-sentences');


Route::get('/export-status', [SentenceController::class, 'checkExportStatus'])->name('export.status');
Route::get('/download-export/{fileName}', [SentenceController::class, 'downloadExport'])->name('export.download');
Route::get('/download-export-direct/{fileName}', [SentenceController::class, 'downloadExportDirect'])->name('export.download.direct');
Route::get('/statistics', [SentenceController::class, 'getRegionalStatistics']);
Route::get('/translations', [SentenceController::class, 'getRegionalTranslations']);



Route::middleware(['auth', 'region_admin'])->group(function () {
    Route::get('/region-admin', [RegionAdminController::class, 'home'])->name('region-admin.index');
    Route::get('/region-admin/sentences', [RegionAdminController::class, 'index'])->name('region-admin.sentences');
    Route::get('/region-admin/other-sentences', [RegionAdminController::class, 'index'])->name('region-admin.otherSentences');
    Route::post('/region-admin/mark-completed', [RegionAdminController::class, 'markAsCompleted'])->name('region-admin.mark-completed');
    Route::post('/region-admin/mark-available', [RegionAdminController::class, 'markAsAvailable'])->name('region-admin.mark-available');
    Route::post('/region-admin/bulk-complete', [RegionAdminController::class, 'bulkComplete'])->name('region-admin.bulk-complete');
    Route::post('/region-admin/bulk-make-available', [RegionAdminController::class, 'bulkMakeAvailable'])->name('region-admin.bulk-make-available');
    Route::get('/region-admin/users', [RegionAdminController::class, 'users'])->name('region-admin.users');
});

Route::middleware(['auth', 'isTranslator'])->group(function () {
    Route::get('/translator', [TranslatorController::class, 'index'])->name('translator.index');
    Route::get('/translator/translations', [TranslatorController::class, 'translations'])->name('translator.translations');
    Route::get('/translator/dashboard', [TranslatorController::class, 'dashboard'])->name('translator.dashboard');
    Route::post('/translator/translate/{translation}/submit', [TranslatorController::class, 'submitTranslation'])
        ->name('translator.submit');
    Route::post('/translator/translate/{translation}/skip', [TranslatorController::class, 'skipSentence'])
        ->name('translator.skip');
});

Route::middleware(['auth', 'isProofreader'])->group(function () {
    Route::get('/proofreader', [ProofreaderController::class, 'index'])->name('proofreader.index');
    Route::get('/proofreader/translations', [ProofreaderController::class, 'translations'])->name('proofreader.translations');
    Route::get('/proofreader/users', [RegionAdminController::class, 'users'])->name('proofreader.users');
    Route::get('/proofreader/dashboard', [ProofreaderController::class, 'dashboard'])->name('proofreader.dashboard');
    Route::post('/proofreader/review/{translation}', [ProofreaderController::class, 'reviewTranslation'])->name('proofreader.review');
});

Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
Route::get('/regions/create', [RegionController::class, 'create'])->name('regions.create');
Route::post('/regions/store', [RegionController::class, 'store'])->name('regions.store');
Route::get('/regions/{region}/edit', [RegionController::class, 'edit'])->name('regions.edit');
Route::patch('/regions/{region}', [RegionController::class, 'update'])->name('regions.update');
Route::delete('/regions/{region}', [RegionController::class, 'destroy'])->name('regions.destroy');
Route::get('/regions/export', [RegionController::class, 'export'])->name('regions.export');
Route::get('/regions/search', [RegionController::class, 'search'])->name('admin.tags.search');

// Translation routes
Route::post('/translations', [TranslatorController::class, 'saveTranslation'])->middleware('role:translator');
Route::post('/translations/{translation}/proofread', [TranslatorController::class, 'proofreadTranslation'])->middleware('role:proofreader');
Route::post('/translations/{translation}/approve', [TranslatorController::class, 'finalApprove'])->middleware('role:region_admin,fadn');
Route::put('/translations/{translation}', [TranslatorController::class, 'editTranslation']);

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}', [UserController::class, 'show']);
Route::get('/users/{user}', [UserController::class, 'edit'])->name('users.edit');
Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');;
Route::get('/users/{user}/stats', [UserController::class, 'stats']);
// Восстановление пользователя
Route::put('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');

// Мягкое удаление (архивация)
Route::delete('users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');

// Полное удаление
Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
