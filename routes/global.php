<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth'], 'namespace' => 'Global'], function () {
    Route::get('consultants/available-days', 'ConsultantsController@getAvailableDays')->name('consultants.available-days');
    Route::get('consultants/available-times', 'ConsultantsController@getAvailableTimes')->name('consultants.available-times');

    // Chat
    Route::get('chats', 'ChatController@index')->name('admin.chats.index');
    Route::post('chats/start-chat', 'ChatController@startChat')->name('admin.chats.start-chat');
    Route::post('chats/load-conversation', 'ChatController@loadConversation')->name('admin.chats.load-conversation');
    Route::post('chats/send-message', 'ChatController@sendMessage')->name('admin.chats.send-message');
    Route::post('chats/mark-as-read', 'ChatController@markAsRead')->name('admin.chats.mark-as-read');
    Route::post('chats/storeMedia', 'ChatController@storeMedia')->name('admin.chats.storeMedia');
    Route::post('chats/load-more-messages', 'ChatController@loadMoreMessages')->name('admin.chats.load-more-messages');

    // Service Loan Payments
    Route::post('service-loan-payments', 'ServiceLoanPaymentsController@store')->name('service-loan-payments.store');
    Route::get('service-loan-payments/{payment}', 'ServiceLoanPaymentsController@show')->name('service-loan-payments.show');
    Route::post('service-loan-payments/storeMedia', 'ServiceLoanPaymentsController@storeMedia')->name('service-loan-payments.storeMedia');
    Route::get('service-loans/{serviceLoan}/payments/summary', 'ServiceLoanPaymentsController@getSummary')->name('service-loan-payments.summary');
    Route::post('service-loan-payments/validate-amount', 'ServiceLoanPaymentsController@validateAmount')->name('service-loan-payments.validate-amount');
    Route::get('service-loans/{serviceLoan}/payments/next-installment', 'ServiceLoanPaymentsController@getNextInstallment')->name('service-loan-payments.next-installment');
    // Payment Approval Routes
    Route::post('service-loan-payments/{payment}/accept', 'ServiceLoanPaymentsController@accept')->name('service-loan-payments.accept');
    Route::post('service-loan-payments/{payment}/accept-specialist', 'ServiceLoanPaymentsController@acceptSpecialist')->name('service-loan-payments.accept-specialist');
    Route::post('service-loan-payments/{payment}/reject', 'ServiceLoanPaymentsController@reject')->name('service-loan-payments.reject');

    // Beneficiary Family
    Route::get('beneficiary-families/get-by-beneficiary', 'GeneralController@getByBeneficiary')->name('beneficiary-families.get-by-beneficiary');
}); 
