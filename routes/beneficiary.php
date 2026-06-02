<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Beneficiary\AuthController;

Route::post('/beneficiary/register', [AuthController::class, 'register'])->name('beneficiary.register');  

Route::group(['prefix' => 'beneficiary', 'as' => 'beneficiary.', 'namespace' => 'Beneficiary', 'middleware' => ['auth', 'beneficiary']], function () {

    
    Route::get('/', 'HomeController@index')->name('home');  
    Route::post('profile/media', 'ProfileController@storeMedia')->name('profile.storeMedia');
    Route::post('profile/ckmedia', 'ProfileController@storeCKEditorImages')->name('profile.storeCKEditorImages');
    Route::get('/profile', 'ProfileController@show')->name('profile.show');
    Route::put('/profile/update/{id}', 'ProfileController@update')->name('profile.update');

    // Beneficiary Family
    Route::post('beneficiary-families/media', 'BeneficiaryFamilyController@storeMedia')->name('beneficiary-families.storeMedia');
    Route::post('beneficiary-families/ckmedia', 'BeneficiaryFamilyController@storeCKEditorImages')->name('beneficiary-families.storeCKEditorImages');
    Route::post('beneficiary-families/show', 'BeneficiaryFamilyController@show')->name('beneficiary-families.show');
    Route::post('beneficiary-families/create', 'BeneficiaryFamilyController@create')->name('beneficiary-families.create');
    Route::post('beneficiary-families/store', 'BeneficiaryFamilyController@store')->name('beneficiary-families.store');
    Route::post('beneficiary-families/edit', 'BeneficiaryFamilyController@edit')->name('beneficiary-families.edit');
    Route::put('beneficiary-families/update/{beneficiaryFamily}', 'BeneficiaryFamilyController@update')->name('beneficiary-families.update');
    Route::post('beneficiary-families/destroy', 'BeneficiaryFamilyController@destroy')->name('beneficiary-families.destroy');  

    // Beneficiary Orders
    Route::post('beneficiary-orders/media', 'BeneficiaryOrdersController@storeMedia')->name('beneficiary-orders.storeMedia');
    Route::post('beneficiary-orders/ckmedia', 'BeneficiaryOrdersController@storeCKEditorImages')->name('beneficiary-orders.storeCKEditorImages');
    Route::resource('beneficiary-orders', 'BeneficiaryOrdersController');
    Route::post('beneficiary-orders/{beneficiaryOrder}/training-satisfaction', 'BeneficiaryOrdersController@submitTrainingSatisfaction')
        ->name('beneficiary-orders.training-satisfaction');
    Route::post('beneficiary-orders/{beneficiaryOrder}/assistance-pickup', 'BeneficiaryOrdersController@submitAssistancePickup')
        ->name('beneficiary-orders.assistance-pickup');
    Route::post('beneficiary-orders/{beneficiaryOrder}/assistance-complete-docs', 'BeneficiaryOrdersController@submitAssistanceCompleteDocs')
        ->name('beneficiary-orders.assistance-complete-docs');
    Route::post('beneficiary-orders/{beneficiaryOrder}/assistance-satisfaction', 'BeneficiaryOrdersController@submitAssistanceSatisfaction')
        ->name('beneficiary-orders.assistance-satisfaction');
    Route::post('beneficiary-orders/{beneficiaryOrder}/surgical-receipt', 'BeneficiaryOrdersController@submitSurgicalReceipt')
        ->name('beneficiary-orders.surgical-receipt');
    Route::post('beneficiary-orders/{beneficiaryOrder}/detection-pickup', 'BeneficiaryOrdersController@submitDetectionPickup')
        ->name('beneficiary-orders.detection-pickup');
    Route::post('beneficiary-orders/{beneficiaryOrder}/detection-receipt', 'BeneficiaryOrdersController@submitDetectionReceipt')
        ->name('beneficiary-orders.detection-receipt');
    Route::post('beneficiary-orders/{beneficiaryOrder}/detection-satisfaction', 'BeneficiaryOrdersController@submitDetectionSatisfaction')
        ->name('beneficiary-orders.detection-satisfaction');

    Route::get('user-alerts', 'UserAlertsController@index')->name('user-alerts.index');

    // Loan Actions
    Route::post('loan/update', 'LoanController@update')->name('loan.update'); 

    // Mailbox 
    Route::post('mailbox/media', 'MailboxController@storeMedia')->name('mailbox.storeMedia');
    Route::get('mailbox', 'MailboxController@index')->name('mailbox.index');
    Route::post('mailbox/store', 'MailboxController@store')->name('mailbox.store');
    Route::post('mailbox/star', 'MailboxController@star')->name('mailbox.star');
    Route::post('mailbox/important', 'MailboxController@important')->name('mailbox.important');
    Route::post('mailbox/archive', 'MailboxController@archive')->name('mailbox.archive');
    Route::post('mailbox/show', 'MailboxController@show')->name('mailbox.show');
    Route::delete('mailbox/destroy/{message}', 'MailboxController@destroy')->name('mailbox.destroy');
    Route::post('mailbox/reply', 'MailboxController@reply')->name('mailbox.reply');
    Route::post('mailbox/load-more', 'MailboxController@loadMore')->name('mailbox.loadMore');
});
