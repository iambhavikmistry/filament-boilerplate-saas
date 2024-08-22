<?php

use Illuminate\Support\Facades\Route;

Route::impersonate();

// Documentation routes
Route::view('docs/{page?}', 'docs::index')->where('page', '(.*)');

// Additional Auth Routes
Route::get('logout', '\Wave\Http\Controllers\Auth\LoginController@logout')->name('wave.logout');
Route::get('user/verify/{verification_code}', '\Wave\Http\Controllers\Auth\RegisterController@verify')->name('verify');
Route::post('register/complete', '\Wave\Http\Controllers\Auth\RegisterController@complete')->name('wave.register-complete');

Route::view('install', 'wave::install')->name('wave.install');

/***** Pages *****/
Route::get('p/{page}', '\Wave\Http\Controllers\PageController@page');

Route::group(['middleware' => 'auth'], function () {
    Route::redirect('settings', 'settings/profile')->name('settings');

    if(config("wave.billing_provider") == 'paddle'){
        Route::get('settings/invoices/{invoice}', '\Wave\Http\Controllers\SubscriptionController@invoice')->name('wave.paddle.invoice');
    }

    Route::post('notification/read/{id}', '\Wave\Http\Controllers\NotificationController@delete')->name('wave.notification.read');
    Route::post('changelog/read', '\Wave\Http\Controllers\ChangelogController@read')->name('changelog.read');

    /********** Checkout/Billing Routes ***********/
    Route::post('cancel', '\Wave\Http\Controllers\SubscriptionController@cancel')->name('wave.cancel');
    Route::view('checkout/welcome', 'theme::welcome');

    Route::post('subscribe', '\Wave\Http\Controllers\SubscriptionController@subscribe')->name('wave.subscribe');
    Route::post('switch-plans', '\Wave\Http\Controllers\SubscriptionController@switchPlans')->name('wave.switch-plans');
});

Route::get('wave/theme/image/{theme_name}', '\Wave\Http\Controllers\ThemeImageController@show');
Route::redirect('admin/login', '/auth/login');

Route::get('reset', \Wave\Actions\Reset::class);

/***** Billing Routes *****/
Route::post('webhook/paddle', '\Wave\Http\Controllers\Billing\Webhooks\PaddleWebhook@handler')->middleware('paddle-webhook-signature');
Route::post('webhook/stripe', '\Wave\Http\Controllers\Billing\Webhooks\StripeWebhook@handler');
Route::get('stripe/portal', '\Wave\Http\Controllers\Billing\Stripe@redirect_to_customer_portal')->name('stripe.portal');
Route::redirect('billing', 'settings/subscription')->name('billing');

Route::get('work', function(){
    
    $user = \App\Models\User::find(34);

    $user->syncRoles([]);
    $user->assignRole('basic');
});
