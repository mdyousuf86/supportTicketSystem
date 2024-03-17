<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('/', 'login')->name('login');
        Route::get('logout', 'logout')->middleware('admin')->name('logout');
    });

    // Admin Password Reset
    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('reset');
        Route::post('reset', 'sendResetCodeEmail');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware('admin', 'adminPermission')->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('ticket/statistic', 'ticketStatistics')->name('statistics');
        Route::get('ticket/statistic/widget', 'ticketStatisticsWidget')->name('ticket.statistic.widget');
        Route::get('first/response/chart', 'firstResponseChart')->name('first.response.chart');
        Route::get('submitted/hours/chart', 'ticketSubmittedByHours')->name('submitted.hours.chart');
        Route::get('department/wise/chart', 'departmentWiseTickets')->name('department.wise.tickets.chart');
        Route::get('first/reply/staff/chart', 'firstReplyByStaff')->name('first.reply.staff.chart');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit')->name('request.report.submit');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');


        //New Tickets
        Route::get('new/tickets', 'newTickets')->name('new.tickets');
        Route::get('clients/replies', 'clientsReplies')->name('clients.replies');
        Route::get('staff/replies', 'staffReplies')->name('staff.replies');
        Route::get('without/reply', 'withoutReplies')->name('without.replies');
    });

    //Department
    Route::controller('TicketDepartmentController')->prefix('department')->name('department.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('details/{departmentId?}', 'details')->name('details');
        Route::post('details/save/{id?}', 'detailsSave')->name('details.save');

        Route::get('custom/field/{departmentId?}', 'customField')->name('custom.field');
        Route::post('custom/field/save/{id?}', 'customFieldSave')->name('custom.field.save');

        Route::post('status/{id}', 'status')->name('status');
        Route::post('sort-table', 'sortDepartment')->name('sort');
    });
    //status
    Route::controller('TicketStatusController')->prefix('status')->name('status.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('sort-table', 'sortStatus')->name('sort');
        Route::post('status/{id}', 'status')->name('status');
    });
    //priority
    Route::controller('TicketPriorityController')->prefix('priority')->name('priority.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('sort-table', 'sortPriority')->name('sort');
        Route::post('status/{id}', 'status')->name('status');
    });
    //spam filter
    Route::controller('SpamFiltersController')->prefix('spam-filter')->name('spam.filter.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('delete/{id}', 'delete')->name('delete');
    });
    //staff
    // Route::controller('StaffController')->prefix('staff')->name('staff.')->group(function () {
    //     Route::get('/', 'index')->name('index');
    //     Route::post('save/{id?}', 'save')->name('save');
    //     Route::post('status/{id}', 'status')->name('status');

    //     Route::get('/role', 'roleIndex')->name('role');

    // });

    Route::controller('StaffController')->prefix('staff')->name('staff.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('switch-status/{id}', 'status')->name('status');
        Route::get('login/{id}', 'login')->name('login');
    });
    //role
    Route::controller('RolesController')->prefix('roles')->name('roles.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('add', 'add')->name('add');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('save/{id?}', 'save')->name('save');
    });
    // permission
    Route::controller('PermissionController')->prefix('permissions')->name('permissions.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('update-permissions', 'updatePermissions')->name('update');
    });




    //Category Setting
    Route::controller('CategoryController')->name('category.')->prefix('categories')->group(function () {
        Route::get('/', 'index')->name('all');
        Route::get('trashed', 'trashed')->name('trashed');
        Route::post('store/{id?}', 'store')->name('store');
        Route::post('delete/{id}', 'delete')->name('delete');
    });

    //reply messages
    Route::controller('PredefinedRepliesController')->prefix('replies')->name('reply.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('status/{id}', 'status')->name('status');
        Route::get('predefined/reply', 'predefinedMessage')->name('predefined.messages');
    });

    //status
    Route::controller('TicketFeedbackController')->prefix('feedback')->name('feedback.')->group(function () {
        Route::get('/{id?}', 'index')->name('index');
    });


    // Users Manager
    Route::controller('ManageUsersController')->name('users.')->prefix('users')->group(function () {
        Route::get('/', 'allUsers')->name('all');
        Route::get('active', 'activeUsers')->name('active');
        Route::get('banned', 'bannedUsers')->name('banned');
        Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
        Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
        Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
        Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');

        Route::get('detail/{id}', 'detail')->name('detail');
        Route::post('update/{id}', 'update')->name('update');
        Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
        Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
        Route::get('login/{id}', 'login')->name('login');
        Route::post('status/{id}', 'status')->name('status');

        Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
        Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
        Route::get('list', 'list')->name('list');
        Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');
    });

    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });


    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/new', 'supportTicketDepartment')->name('department');
        Route::get('new/{departmentId}', 'openSupportTicket')->name('open');
        Route::post('create', 'storeSupportTicket')->name('store');
        Route::post('user/check', 'userCheck')->name('user');
        Route::get('/index', 'tickets')->name('index');
        Route::get('/{id}', 'tickets')->name('index.status');
        Route::get('view/{ticketNumber}', 'ticketReply')->name('view');
        Route::get('note/view/{ticketNumber}', 'addNote')->name('note');
        Route::get('custom/fields/{ticketNumber}', 'customFields')->name('custom.fields');
        Route::get('other/ticket/{ticketNumber}', 'otherTickets')->name('other.tickets');
        Route::get('log/{ticketNumber}', 'log')->name('log');
        Route::post('department/change', 'changeDepartment')->name('department.change');
        Route::post('priority/change', 'changePriority')->name('priority.change');
        Route::post('/assigned', 'ticketAssigned')->name('assigned');
        Route::post('reply/{ticketNumber}', 'replyTicket')->name('reply');
        Route::post('predefined/reply', 'predefinedMessage')->name('predefined.messages');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{ticket}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
        Route::get('attachment/delete/{ticket}', 'ticketAttachmentDelete')->name('attachment.delete');
        Route::post('number/filter', 'ticketNumberFilter')->name('number.filter');
    });

    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::controller('GeneralSettingController')->group(function () {
        // General Setting
        Route::get('general-setting', 'index')->name('setting.index');
        Route::post('general-setting', 'update')->name('setting.update');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit')->name('setting.system.configuration.submit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit')->name('setting.custom.css.submit');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit')->name('setting.cookie.submit');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit')->name('maintenance.mode.submit');
    });


    Route::controller('CronConfigurationController')->name('cron.')->prefix('cron')->group(function () {
        Route::get('index', 'cronJobs')->name('index');
        Route::post('store', 'cronJobStore')->name('store');
        Route::post('update', 'cronJobUpdate')->name('update');
        Route::post('delete/{id}', 'cronJobDelete')->name('delete');
        Route::get('schedule', 'schedule')->name('schedule');
        Route::post('schedule/store', 'scheduleStore')->name('schedule.store');
        Route::post('schedule/status/{id}', 'scheduleStatus')->name('schedule.status');
        Route::get('schedule/pause/{id}', 'schedulePause')->name('schedule.pause');
        Route::get('schedule/logs/{id}', 'scheduleLogs')->name('schedule.logs');
        Route::post('schedule/log/resolved/{id}', 'scheduleLogResolved')->name('schedule.log.resolved');
        Route::post('schedule/log/flush/{id}', 'logFlush')->name('log.flush');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global', 'global')->name('global');
        Route::post('global/update', 'globalUpdate')->name('global.update');
        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate')->name('sms.update');
        Route::post('sms/test', 'smsTest')->name('sms.test');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });


    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('system-update', 'systemUpdate')->name('update');
        Route::post('update-upload', 'updateUpload')->name('update.upload');
    });


    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');


    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');
        });
    });
});
