<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'staff']], function () {
    Route::post('dynamic-services/media', 'DynamicServiceController@storeMedia')->name('dynamic-services.storeMedia');
    Route::delete('dynamic-services/destroy', 'DynamicServiceController@massDestroy')->name('dynamic-services.massDestroy');
    Route::put('dynamic-services/{dynamicService}/program-meetings', 'DynamicServiceController@updateProgramMeetings')->name('dynamic-services.update-program-meetings');
    Route::put('dynamic-services/{dynamicService}/workflow', 'DynamicServiceController@processWorkflow')->name('dynamic-services.process-workflow');
    Route::resource('dynamic-services', 'DynamicServiceController');
});
