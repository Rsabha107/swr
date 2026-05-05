<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Swr\Admin\ImportExportController;
use App\Http\Controllers\Swr\Admin\SecondmentWeeklyDocumentController;
use App\Http\Controllers\Swr\Admin\SecondmentWeeklyReportController as AdminSecondmentWeeklyReportController;
use App\Http\Controllers\Swr\Customer\SecondmentWeeklyReportController;

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

// Booking MANAGEMENT ******************************************************************** Admin All Route
Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin', 'prevent-back-history', 'auth.session'])->group(function () {

    //Import and Export
    Route::controller(ImportExportController::class)->group(function () {
        Route::get('/swr/admin/report/import', 'showImportForm')->name('swr.admin.report.import');
        Route::post('/swr/admin/report/import', 'import')->name('swr.admin.report.import.store');
        Route::post('/swr/admin/report/export', 'export')->name('swr.admin.report.export');
    });

    //SWR Admin Reports
    Route::controller(AdminSecondmentWeeklyReportController::class)->group(function () {
        Route::get('/swr/admin/report', 'index')->name('swr.admin.report');
        Route::get('/swr/admin/report/list', 'list')->name('swr.admin.report.list');
        Route::get('/swr/admin/report/detail/{id}', 'detail')->name('swr.admin.report.detail');
        Route::get('/swr/admin/report/gallery/{id}', 'gallery')->name('swr.admin.report.gallery');
        Route::post('/swr/admin/report/{id}/approve', 'approve')->name('swr.admin.report.approve');
        Route::post('/swr/admin/report/{id}/reject', 'reject')->name('swr.admin.report.reject');
        Route::get('/swr/admin/report/pdf/{id?}', 'reportPdf')->name('swr.admin.report.pdf');
        Route::get('/swr/admin/events/{id}/venues', 'byEvent')->name('swr.admin.events.venues');
        Route::delete('/swr/admin/report/delete/{id}', 'destroy')->name('swr.admin.report.destroy');
    });

});

// shared routes between SuperAdmin and Customer
Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin|Customer',  'prevent-back-history', 'auth.session'])->group(function () {
    // docs

    Route::get('/reports/{report}/images/export', [SecondmentWeeklyDocumentController::class, 'exportImages'])
        ->name('reports.images.export');

    Route::get('/swr/docs/{document}/download', [SecondmentWeeklyDocumentController::class, 'download'])
        ->name('swr.docs.download');
    Route::get('/swr/docs/{document}/view.{ext}', [SecondmentWeeklyDocumentController::class, 'view'])
        ->name('swr.docs.view.ext');

    Route::delete('/swr/docs/{document}', [SecondmentWeeklyDocumentController::class, 'destroy'])
        ->name('swr.docs.destroy');
});

Route::middleware(['auth', 'otp', 'XssSanitizer',  'role:Customer',  'prevent-back-history', 'auth.session'])->group(function () {
    // used to select venues from event
    Route::get('/swr/events/{event_id}/venues', [SecondmentWeeklyReportController::class, 'byEvent'])->name('swr.events.venues');
});

Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer',  'role:Customer',  'prevent-back-history', 'auth.session'])->group(function () {

    Route::controller(SecondmentWeeklyReportController::class)->group(function () {
        Route::get('/swr/report', 'index')->name('swr.report');
        Route::get('/swr/report/list', 'list')->name('swr.report.list');
        Route::get('/swr/report/create', 'create')->name('swr.report.create');
        Route::post('/swr/report/store', 'store')->name('swr.report.store');
        Route::get('/swr/report/detail/{id}', 'detail')->name('swr.report.detail');
        Route::get('/swr/report/edit/{id}', 'edit')->name('swr.report.edit');
        Route::put('/swr/report/update/{id}', 'update')->name('swr.report.update');
        Route::get('/swr/report/gallery/{id}', 'gallery')->name('swr.report.gallery');
        Route::delete('/swr/report/delete/{id}', 'destroy')->name('swr.report.destroy');
        // for event switching
        Route::get('/swr/customer/events/{id}/switch',  'switch')->name('swr.customer.event.switch');
        // AJAX endpoint for getting venues by event
        Route::get('/swr/report/byEvent/{event_id}', 'byEvent')->name('swr.report.byEvent');
    });
});

// ****************** ADMIN *********************
Route::group(['middleware' => 'prevent-back-history'], function () {

    // Add User

    require __DIR__ . '/auth.php';

    // Route::get('/run-migration', function () {
    //     Artisan::call('optimize:clear');

    //     Artisan::call('migrate:refresh --seed');
    //     return "Migration executed successfully";
    // });


});
