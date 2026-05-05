<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\Wdr\Setting\EventController;
use App\Http\Controllers\GeneralSettings\EventDocumentController;
use App\Http\Controllers\GeneralSettings\ParticipantDocumentController;
use App\Http\Controllers\GeneralSettings\UploadController;

use App\Http\Controllers\Security\ActivityAuditController;
use App\Http\Controllers\Security\RoleController as SecurityRoleController;
use App\Http\Controllers\Wdr\Admin\UserController as AdminUserController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\Wdr\Setting\VenueController;
use App\Http\Controllers\UtilController;
use App\Http\Controllers\Wdr\Auth\AdminController as WdrAuthAdminController;
use App\Http\Controllers\Wdr\Admin\DashboardController;
use App\Http\Controllers\Wdr\Admin\ImportExportController;
use App\Http\Controllers\Wdr\Admin\WorkforceDailyReportController as AdminWorkforceDailyReportController;
use App\Http\Controllers\Wdr\Admin\WorkforceReportDocumentController;
use App\Http\Controllers\Wdr\Customer\WorkforceDailyReportController;
use App\Http\Controllers\Wdr\Setting\AppSettingController;
use App\Http\Controllers\Wdr\Setting\DayTypeController;
use App\Http\Controllers\Wdr\Setting\EventImageController;


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
    appLog('In home route');
    if (!auth()->check()) {
        appLog('User is not authenticated');
        return redirect()->route('login');
    }

    $roleRoutes = [
        'SuperAdmin' => 'wdr.admin.report',
        'Customer'   => 'wdr.report',
    ];

    foreach ($roleRoutes as $role => $route) {
        if (auth()->user()->hasRole($role)) {
            appLog("Redirecting to $route for role $role");
            return redirect()->route($route);
        }
    }

    abort(403, 'Unauthorized role');
})->name('home');


Route::controller(MicrosoftController::class)->group(function () {
    Route::get('auth/microsoft', 'redirectToMicrosoft')->name('auth.microsoft');
    Route::get('auth/microsoft/callback', 'handleMicrosoftCallback');
});

// Image Uploader
Route::post('/uploads/process', [UploadController::class, 'process'])->name('uploads.process');
Route::delete('/uploads/revert', [UploadController::class, 'revert'])->name('uploads.revert');


// Booking MANAGEMENT ******************************************************************** Admin All Route
Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin', 'prevent-back-history', 'auth.session'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/wdr/admin/dashboard', 'dashboard')->name('wdr.admin.dashboard');
    });

    //Import and Export
    Route::controller(ImportExportController::class)->group(function () {
        Route::get('/wdr/admin/report/import', 'showImportForm')->name('wdr.admin.report.import');
        Route::post('/wdr/admin/report/import', 'import')->name('wdr.admin.report.import.store');
        Route::post('/wdr/admin/report/export', 'export')->name('wdr.admin.report.export');
    });

    //WDR
    Route::controller(AdminWorkforceDailyReportController::class)->group(function () {
        Route::get('/wdr/admin/report', 'index')->name('wdr.admin.report');
        Route::get('/wdr/admin/report/list', 'list')->name('wdr.admin.report.list');
        Route::get('/wdr/admin/report/detail/{id}', 'detail')->name('wdr.admin.report.detail');
        Route::get('/wdr/admin/report/gallery/{id}', 'gallery')->name('wdr.admin.report.gallery');
        // for event switching
        Route::get('/wdr/admin/events/{id}/switch',  'switch')->name('wdr.admin.event.switch');
        Route::delete('/wdr/admin/report/delete/{id}', 'destroy')->name('wdr.admin.report.destroy');
        
    });

    Route::controller(DayTypeController::class)->group(function () {
        Route::get('/wdr/setting/day_type', 'index')->name('wdr.setting.day_type');
        Route::get('/wdr/setting/day_type/list', 'list')->name('wdr.setting.day_type.list');
        Route::get('/wdr/setting/day_type/get/{id}', 'get')->name('wdr.setting.day_type.get');
        Route::post('wdr/setting/day_type/update', 'update')->name('wdr.setting.day_type.update');
        Route::delete('/wdr/setting/day_type/delete/{id}', 'delete')->name('wdr.setting.day_type.delete');
        Route::post('/wdr/setting/day_type/store', 'store')->name('wdr.setting.day_type.store');
        Route::get('/wdr/setting/day_type/mv/get/{id}', 'getEventView')->name('wdr.setting.day_type.get.mv');
    });


    //     // Venue
    Route::controller(VenueController::class)->group(function () {
        Route::get('/wdr/setting/venue', 'index')->name('wdr.setting.venue');
        Route::get('/wdr/setting/venue/list', 'list')->name('wdr.setting.venue.list');
        Route::get('/wdr/setting/venue/get/{id}', 'get')->name('wdr.setting.venue.get');
        Route::post('/wdr/setting/venue/update', 'update')->name('wdr.setting.venue.update');
        Route::delete('/wdr/setting/venue/delete/{id}', 'delete')->name('wdr.setting.venue.delete');
        Route::post('/wdr/setting/venue/store', 'store')->name('wdr.setting.venue.store');
    });

    //Event
    Route::controller(EventController::class)->group(function () {
        Route::get('/wdr/setting/event', 'index')->name('wdr.setting.event');
        Route::get('/wdr/setting/event/list', 'list')->name('wdr.setting.event.list');
        Route::get('/wdr/setting/event/get/{id}', 'get')->name('wdr.setting.event.get');
        Route::post('/wdr/setting/event/update', 'update')->name('wdr.setting.event.update');
        Route::delete('/wdr/setting/event/delete/{id}', 'delete')->name('wdr.setting.event.delete');
        Route::post('/wdr/setting/event/store', 'store')->name('wdr.setting.event.store');
    });

    Route::get('/auth/ms-signup', [WdrAuthAdminController::class, 'msSignUp'])->name('auth.ms.signup');
    Route::post('/signup/ms/store', [UserController::class, 'msStore'])->name('admin.signup.ms.store');

    Route::controller(AdminUserController::class)->group(function () {
        // Route::get('/vapp/admin/users/profile', 'profile')->name('vapp.admin.users.profile');
        Route::post('/wdr/admin/users/profile/update', 'update')->name('wdr.admin.users.profile.update');
        Route::post('/wdr/admin/users/profile/password/update', 'updatePassword')->name('wdr.admin.users.profile.password.update');
        Route::get('/wdr/admin/users/invite-user', 'showForm')->name('wdr.admin.users.invite.form');
        Route::post('/wdr/invite-user', 'sendInvite')->name('wdr.admin.users.invite.send');
    });

    //Applicaiton Setting
    Route::controller(AppSettingController::class)->group(function () {
        Route::get('/wdr/setting/application', 'index')->name('wdr.setting.application');
        Route::get('/wdr/setting/application/list', 'list')->name('wdr.setting.application.list');
        Route::get('/wdr/setting/application/get/{id}', 'get')->name('wdr.setting.application.get');
        Route::post('/wdr/setting/application/update', 'update')->name('wdr.setting.application.update');
        Route::delete('/wdr/setting/application/delete/{id}', 'delete')->name('wdr.setting.application.delete');
        Route::post('/wdr/setting/application/store', 'store')->name('wdr.setting.application.store');
    });

    // docs
    Route::get('/event/docs/{document}/download', [EventDocumentController::class, 'download'])
        ->name('event.docs.download');

    Route::delete('/event/docs/{document}', [EventDocumentController::class, 'destroy'])
        ->name('event.docs.destroy');

    Route::controller(AdminUserController::class)->group(function () {
        Route::get('/wdr/admin/users/profile', 'profile')->name('admin.users.profile');
        Route::post('/wdr/admin/users/profile/update', 'update')->name('admin.users.profile.update');
        Route::post('/wdr/admin/users/profile/password/update', 'updatePassword')->name('admin.users.profile.password.update');
        Route::get('/wdr/admin/users/invite-user', 'showForm')->name('admin.users.invite.form');
        // Route::post('/wdr/invite-user', 'sendInvite')->name('admin.users.invite.send');
    });
});



// shared routes between SuperAdmin and Customer
Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer', 'role:SuperAdmin|Customer',  'prevent-back-history', 'auth.session'])->group(function () {
    // docs

    // Event Image
    Route::controller(EventImageController::class)->group(function () {
        Route::get('/wdr/setting/event/file/{id}', 'getPrivateFile')->name('wdr.setting.event.file');
    });

    Route::get('/reports/{report}/images/export', [WorkforceReportDocumentController::class, 'exportImages'])
        ->name('reports.images.export');

    Route::get('/wdr/docs/{document}/download', [WorkforceReportDocumentController::class, 'download'])
        ->name('wdr.docs.download');
    Route::get('/wdr/docs/{document}/view.{ext}', [WorkforceReportDocumentController::class, 'view'])
        ->name('wdr.docs.view.ext');

        Route::get('/wdr/report/pdf/{id?}', [AdminWorkforceDailyReportController::class, 'reportPdf'])->name('wdr.report.pdf');
    Route::delete('/wdr/docs/{document}', [WorkforceReportDocumentController::class, 'destroy'])
        ->name('wdr.docs.destroy');
});

Route::middleware(['auth', 'otp', 'XssSanitizer',  'role:Customer',  'prevent-back-history', 'auth.session'])->group(function () {
    // used to select venues from event
    Route::get('/wdr/events/{event_id}/venues', [WorkforceDailyReportController::class, 'byEvent'])->name('events.venues');
});

Route::middleware(['auth', 'otp', 'mutli.event', 'XssSanitizer',  'role:Customer',  'prevent-back-history', 'auth.session'])->group(function () {


    Route::controller(WorkforceDailyReportController::class)->group(function () {
        Route::get('/wdr/report/create', 'create')->name('wdr.report.create');
        Route::post('/wdr/report/store', 'store')->name('wdr.report.store');
        Route::delete('/wdr/report/delete/{id}', 'destroy')->name('wdr.report.destroy');
        Route::get('/wdr/report', 'index')->name('wdr.report');
        Route::get('/wdr/report/list', 'list')->name('wdr.report.list');
        Route::get('/wdr/report/gallery/{id}', 'gallery')->name('wdr.report.gallery');
        // for event switching
        Route::get('/wdr/customer/events/{id}/switch',  'switch')->name('wdr.customer.guardian.switch');
    });
});


// Customer Pick an event
Route::get('/wdr/customer/report/pick', function () {
    return view('/wdr/customer/report/pick');
})->name('wdr.customer.report.pick')->middleware('role:Customer');
Route::post('/wdr/customer/events/switch', [WorkforceDailyReportController::class, 'pickEvent'])->name('wdr.customer.report.event.switch')->middleware('role:Customer');

Route::get('/wdr/logout', [WdrAuthAdminController::class, 'logout'])->name('wdr.logout');


// ****************** ADMIN *********************
Route::group(['middleware' => 'prevent-back-history'], function () {

    // Add User
    Route::get('/wdr/auth/signup', [WdrAuthAdminController::class, 'signUp'])->name('auth.signup')->middleware('signed');
    Route::post('/signup/store', [UserController::class, 'store'])->name('admin.signup.store');

    // Add User
    Route::get('/register/{event_id}', [WdrAuthAdminController::class, 'register'])->name('auth.register');
    Route::post('/register/store', [WdrAuthAdminController::class, 'storeRegister'])->name('admin.register.store');

    Route::middleware(['auth', 'prevent-back-history'])->group(function () {

        Route::get('auth/otp', [WdrAuthAdminController::class, 'showOtp'])->name('otp.get');
        Route::post('verify-otp', [WdrAuthAdminController::class, 'verifyOtpAndLogin'])->name('auth.otp.post');
        Route::get('auth/resend', [WdrAuthAdminController::class, 'resendOTP'])->name('otp.resend.get');

        //used to show images in private folder
        Route::get('/doc/{file}', [UtilController::class, 'showImage'])->name('a');

        /*************************************** Play ground */
        // Route::get('/a/{GlobalAttachment}', [UtilController::class, 'serve'])->name('a');
        Route::get('/doc/{file}', [UtilController::class, 'showImage'])->name('a');
        Route::get('/a', function () {
            return response()->file(storage_path('app/private/users/502828276250308124600avatar-2.png'));
        })->name('b');
        /*************************************** End Play ground */

        Route::get('/wdr/logout', [WdrAuthAdminController::class, 'logout'])->name('wdr.logout');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

    require __DIR__ . '/auth.php';

    Route::middleware(['prevent-back-history'])->group(function () {
        Route::get('/auth/forgot', [AdminController::class, 'forgotPassword'])->name('auth.forgot');
        Route::post('forget-password', [AdminController::class, 'submitForgetPasswordForm'])->name('forgot.password.post');
        Route::get('/auth/reset/{token}', [AdminController::class, 'showResetPasswordForm'])->name('reset.password.get');
        Route::post('reset-password', [AdminController::class, 'submitResetPasswordForm'])->name('reset.password.post');
        Route::get('/send-mail', [SendMailController::class, 'index']);
    });

    Route::middleware(['auth', 'otp', 'XssSanitizer', 'role:SuperAdmin', 'prevent-back-history', 'auth.session'])->group(function () {

        Route::controller(SecurityRoleController::class)->group(function () {
            //Admin User
            Route::get('/sec/adminuser/list', 'listAdminUser')->name('sec.adminuser.list');
            Route::post('updateadminuser', 'updateAdminUser')->name('sec.adminuser.update');
            Route::post('createadminuser', 'createAdminUser')->name('sec.adminuser.create');
            Route::get('/sec/adminuser/{id}/edit', 'editAdminUser')->name('sec.adminuser.edit');
            Route::get('/sec/adminuser/{id}/delete', 'deleteAdminUser')->name('sec.adminuser.delete');
            Route::get('/sec/adminuser/add', 'addAdminUser')->name('sec.adminuser.add');
            Route::get('/sec/adminuser/add2', 'addAdminUser2')->name('sec.adminuser.add2');
        });
    });

    // HR Security Settings all routes
    Route::middleware(['auth', 'otp', 'XssSanitizer', 'role:SecurityRole', 'prevent-back-history', 'auth.session'])->group(function () {

        Route::controller(ActivityAuditController::class)->group(function () {
            Route::get('/sec/audit', 'index')->name('sec.audit');
            Route::get('/sec/audit/list', 'list')->name('sec.audit.list');
        });
        // Roles
        Route::controller(SecurityRoleController::class)->group(function () {

            Route::get('/sec/roles/add', function () {
                return view('/sec/roles/add');
            })->name('sec.roles.add');
            Route::get('/sec/roles/roles/list', 'listRole')->name('sec.roles.list');
            Route::post('updaterole', 'updateRole')->name('sec.roles.update');
            Route::post('createrole', 'createRole')->name('sec.roles.create');
            Route::get('/sec/roles/{id}/edit', 'editRole')->name('sec.roles.edit');
            Route::get('/sec/roles/{id}/delete', 'deleteRole')->name('sec.roles.delete');

            // group
            Route::get('/sec/groups/add', function () {
                return view('/sec/groups/add');
            })->name('sec.groups.add');
            Route::get('/sec/groups/list', 'listGroup')->name('sec.groups.list');
            Route::post('updategroup', 'updateGroup')->name('sec.groups.update');
            Route::post('creategroup', 'createGroup')->name('sec.groups.create');
            Route::get('/sec/groups/{id}/edit', 'editGroup')->name('sec.groups.edit');
            Route::get('/sec/groups/{id}/delete', 'deleteGroup')->name('sec.groups.delete');

            // Permission
            Route::get('/sec/permissions/list', 'listPermission')->name('sec.perm.list');
            Route::post('updatepermission', 'updatePermission')->name('sec.perm.update');
            Route::post('createpermission', 'createPermission')->name('sec.perm.create');
            Route::get('/sec/perm/{id}/edit', 'editPermission')->name('sec.perm.edit');
            Route::get('/sec/perm/{id}/delete', 'deletePermission')->name('sec.perm.delete');
            Route::get('/sec/permissions/add', 'addPermission')->name('sec.perm.add');

            Route::get('/sec/perm/import', 'ImportPermission')->name('sec.perm.import');
            Route::post('importnow', 'ImportNowPermission')->name('sec.perm.import.now');


            // Roles in Permission
            Route::get('/sec/rolesetup/list', 'listRolePermission')->name('sec.rolesetup.list');
            Route::post('updaterolesetup', 'updateRolePermission')->name('sec.rolesetup.update');
            Route::post('createrolesetup', 'createRolePermission')->name('sec.rolesetup.create');
            Route::get('/sec/rolesetup/{id}/edit', 'editRolePermission')->name('sec.rolesetup.edit');
            Route::get('/sec/rolesetup/{id}/delete', 'deleteRolePermission')->name('sec.rolesetup.delete');
            Route::get('/sec/rolesetup/add', 'addRolePermission')->name('sec.rolesetup.add');
        });  //
    });  //
    // Route::get('/run-migration', function () {
    //     Artisan::call('optimize:clear');

    //     Artisan::call('migrate:refresh --seed');
    //     return "Migration executed successfully";
    // });


});

require __DIR__ . '/secondment.php';
