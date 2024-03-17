<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

Route::get('cron', 'CronController@cron')->name('cron');
Route::get('cron/auto/closed/ticket', 'CronController@autoTicketClose');

Route::controller('SupportTicketController')->prefix('support-ticket')->name('support.ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'supportTicketDepartment')->name('department');
    Route::get('new/{departmentId}', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
    Route::get('attachment/delete/{ticket}', 'ticketAttachmentDelete')->name('attachment.delete');
    Route::get('feedback/{ticketNumber}', 'feedbackTicket')->name('feedback');
    Route::post('feedback/{ticketId}', 'feedbackSave')->name('feedback.save');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');


    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
