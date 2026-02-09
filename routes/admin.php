<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Route::group(['middleware' => ['auth'], 'namespace' => 'Admin'], function () {
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
});

Auth::routes();
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'staff']], function () {
    
    Route::get('/', 'HomeController@index')->name('home');
    Route::post('updateStatuses', 'HomeController@updateStatuses')->name('updateStatuses');
    Route::post('/arrange', 'HomeController@arrange')->name('arrange');
    Route::post('arrange/update', 'HomeController@updateArrange')->name('arrange.update'); 

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::post('users/media', 'UsersController@storeMedia')->name('users.storeMedia');
    Route::post('users/ckmedia', 'UsersController@storeCKEditorImages')->name('users.storeCKEditorImages');
    Route::resource('users', 'UsersController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Regions
    Route::delete('regions/destroy', 'RegionsController@massDestroy')->name('regions.massDestroy');
    Route::resource('regions', 'RegionsController');

    // Cities
    Route::delete('cities/destroy', 'CitiesController@massDestroy')->name('cities.massDestroy');
    Route::resource('cities', 'CitiesController');

    // Districts
    Route::delete('districts/destroy', 'DistrictsController@massDestroy')->name('districts.massDestroy');
    Route::resource('districts', 'DistrictsController');

    // Nationalities
    Route::delete('nationalities/destroy', 'NationalitiesController@massDestroy')->name('nationalities.massDestroy');
    Route::resource('nationalities', 'NationalitiesController');

    // Marital Status
    Route::delete('marital-statuses/destroy', 'MaritalStatusController@massDestroy')->name('marital-statuses.massDestroy');
    Route::resource('marital-statuses', 'MaritalStatusController'); 

    // Beneficiary Categories
    Route::delete('beneficiary-categories/destroy', 'BeneficiaryCategoryController@massDestroy')->name('beneficiary-categories.massDestroy');
    Route::resource('beneficiary-categories', 'BeneficiaryCategoryController');
    
    // Accommodation Entities
    Route::delete('accommodation-entities/destroy', 'AccommodationEntityController@massDestroy')->name('accommodation-entities.massDestroy');
    Route::resource('accommodation-entities', 'AccommodationEntityController');

    // Family Relationship
    Route::delete('family-relationships/destroy', 'FamilyRelationshipController@massDestroy')->name('family-relationships.massDestroy');
    Route::resource('family-relationships', 'FamilyRelationshipController');

    // Health Conditions
    Route::delete('health-conditions/destroy', 'HealthConditionsController@massDestroy')->name('health-conditions.massDestroy');
    Route::resource('health-conditions', 'HealthConditionsController');

    // Educational Qualifications
    Route::delete('educational-qualifications/destroy', 'EducationalQualificationsController@massDestroy')->name('educational-qualifications.massDestroy');
    Route::resource('educational-qualifications', 'EducationalQualificationsController');

    // Job Types
    Route::delete('job-types/destroy', 'JobTypesController@massDestroy')->name('job-types.massDestroy');
    Route::resource('job-types', 'JobTypesController');

    // Required Documents
    Route::delete('required-documents/destroy', 'RequiredDocumentsController@massDestroy')->name('required-documents.massDestroy');
    Route::resource('required-documents', 'RequiredDocumentsController');

    // Departments
    Route::delete('departments/destroy', 'DepartmentsController@massDestroy')->name('departments.massDestroy');
    Route::resource('departments', 'DepartmentsController');

    // Sections
    Route::delete('sections/destroy', 'SectionsController@massDestroy')->name('sections.massDestroy');
    Route::resource('sections', 'SectionsController');

    // Beneficiary Import
    Route::get('beneficiaries/import', 'BeneficiaryImportController@showImportForm')->name('beneficiaries.import');
    Route::post('beneficiaries/import/upload', 'BeneficiaryImportController@uploadCsv')->name('beneficiaries.import.upload');
    Route::post('beneficiaries/import/process', 'BeneficiaryImportController@processImport')->name('beneficiaries.import.process');
    
    // Beneficiaries
    Route::delete('beneficiaries/destroy', 'BeneficiariesController@massDestroy')->name('beneficiaries.massDestroy');
    Route::put('beneficiaries/update-status/{beneficiary}', 'BeneficiariesController@updateStatus')->name('beneficiaries.update-status');
    Route::put('beneficiaries/update-case-study/{beneficiary}', 'BeneficiariesController@updateCaseStudy')->name('beneficiaries.update-case-study');
    Route::get('beneficiaries/{beneficiary}/login-as', 'BeneficiariesController@loginAsBeneficiary')->name('beneficiaries.login-as');
    Route::resource('beneficiaries', 'BeneficiariesController');
    

    // Disability Types
    Route::delete('disability-types/destroy', 'DisabilityTypesController@massDestroy')->name('disability-types.massDestroy');
    Route::resource('disability-types', 'DisabilityTypesController');

    // Beneficiary Family
    Route::delete('beneficiary-families/destroy', 'BeneficiaryFamilyController@massDestroy')->name('beneficiary-families.massDestroy');
    Route::post('beneficiary-families/media', 'BeneficiaryFamilyController@storeMedia')->name('beneficiary-families.storeMedia');
    Route::post('beneficiary-families/ckmedia', 'BeneficiaryFamilyController@storeCKEditorImages')->name('beneficiary-families.storeCKEditorImages');
    Route::post('beneficiary-families/show', 'BeneficiaryFamilyController@show')->name('beneficiary-families.show');
    Route::post('beneficiary-families/create', 'BeneficiaryFamilyController@create')->name('beneficiary-families.create');
    Route::post('beneficiary-families/store', 'BeneficiaryFamilyController@store')->name('beneficiary-families.store');
    Route::post('beneficiary-families/edit', 'BeneficiaryFamilyController@edit')->name('beneficiary-families.edit');
    Route::put('beneficiary-families/update/{beneficiaryFamily}', 'BeneficiaryFamilyController@update')->name('beneficiary-families.update');
    Route::post('beneficiary-families/destroy', 'BeneficiaryFamilyController@destroy')->name('beneficiary-families.destroy');


    // Economic Status
    Route::delete('economic-statuses/destroy', 'EconomicStatusController@massDestroy')->name('economic-statuses.massDestroy');
    Route::resource('economic-statuses', 'EconomicStatusController');

    // Beneficiary Files
    Route::delete('beneficiary-files/destroy', 'BeneficiaryFilesController@massDestroy')->name('beneficiary-files.massDestroy');
    Route::post('beneficiary-files/media', 'BeneficiaryFilesController@storeMedia')->name('beneficiary-files.storeMedia');
    Route::post('beneficiary-files/ckmedia', 'BeneficiaryFilesController@storeCKEditorImages')->name('beneficiary-files.storeCKEditorImages');
    Route::resource('beneficiary-files', 'BeneficiaryFilesController');

    // Task Status
    Route::delete('task-statuses/destroy', 'TaskStatusController@massDestroy')->name('task-statuses.massDestroy');
    Route::resource('task-statuses', 'TaskStatusController');

    // Task Tag
    Route::delete('task-tags/destroy', 'TaskTagController@massDestroy')->name('task-tags.massDestroy');
    Route::resource('task-tags', 'TaskTagController');

    // Task
    Route::delete('tasks/destroy', 'TaskController@massDestroy')->name('tasks.massDestroy');
    Route::post('tasks/media', 'TaskController@storeMedia')->name('tasks.storeMedia');
    Route::post('tasks/ckmedia', 'TaskController@storeCKEditorImages')->name('tasks.storeCKEditorImages');
    Route::post('tasks/update-status', 'TaskController@updateStatus')->name('tasks.update-status');
    Route::resource('tasks', 'TaskController');

    // Tasks Calendar
    Route::resource('tasks-calendars', 'TasksCalendarController', ['except' => ['create', 'store', 'edit', 'update', 'show', 'destroy']]);

    // Task Boards
    Route::delete('task-boards/destroy', 'TaskBoardsController@massDestroy')->name('task-boards.massDestroy');
    Route::resource('task-boards', 'TaskBoardsController');

    // Task Priority
    Route::delete('task-priorities/destroy', 'TaskPriorityController@massDestroy')->name('task-priorities.massDestroy');
    Route::resource('task-priorities', 'TaskPriorityController');

    // Faq Category
    Route::delete('faq-categories/destroy', 'FaqCategoryController@massDestroy')->name('faq-categories.massDestroy');
    Route::resource('faq-categories', 'FaqCategoryController');

    // Faq Question
    Route::delete('faq-questions/destroy', 'FaqQuestionController@massDestroy')->name('faq-questions.massDestroy');
    Route::resource('faq-questions', 'FaqQuestionController');

    // User Queries
    Route::resource('user-queries', 'UserQueriesController', ['except' => ['create', 'store', 'show', 'destroy']]);

    // Sliders
    Route::delete('sliders/destroy', 'SlidersController@massDestroy')->name('sliders.massDestroy');
    Route::post('sliders/media', 'SlidersController@storeMedia')->name('sliders.storeMedia');
    Route::post('sliders/ckmedia', 'SlidersController@storeCKEditorImages')->name('sliders.storeCKEditorImages');
    Route::resource('sliders', 'SlidersController');

    // Front Achievement
    Route::delete('front-achievements/destroy', 'FrontAchievementController@massDestroy')->name('front-achievements.massDestroy');
    Route::post('front-achievements/media', 'FrontAchievementController@storeMedia')->name('front-achievements.storeMedia');
    Route::post('front-achievements/ckmedia', 'FrontAchievementController@storeCKEditorImages')->name('front-achievements.storeCKEditorImages');
    Route::resource('front-achievements', 'FrontAchievementController');

    // Front Projects
    Route::delete('front-projects/destroy', 'FrontProjectsController@massDestroy')->name('front-projects.massDestroy');
    Route::post('front-projects/media', 'FrontProjectsController@storeMedia')->name('front-projects.storeMedia');
    Route::post('front-projects/ckmedia', 'FrontProjectsController@storeCKEditorImages')->name('front-projects.storeCKEditorImages');
    Route::resource('front-projects', 'FrontProjectsController');

    // Front Partners
    Route::delete('front-partners/destroy', 'FrontPartnersController@massDestroy')->name('front-partners.massDestroy');
    Route::post('front-partners/media', 'FrontPartnersController@storeMedia')->name('front-partners.storeMedia');
    Route::post('front-partners/ckmedia', 'FrontPartnersController@storeCKEditorImages')->name('front-partners.storeCKEditorImages');
    Route::resource('front-partners', 'FrontPartnersController', ['except' => ['show']]);

    // Front Reviews
    Route::delete('front-reviews/destroy', 'FrontReviewsController@massDestroy')->name('front-reviews.massDestroy');
    Route::post('front-reviews/media', 'FrontReviewsController@storeMedia')->name('front-reviews.storeMedia');
    Route::post('front-reviews/ckmedia', 'FrontReviewsController@storeCKEditorImages')->name('front-reviews.storeCKEditorImages');
    Route::resource('front-reviews', 'FrontReviewsController');

    // Settings
    Route::post('settings/media', 'SettingsController@storeMedia')->name('settings.storeMedia');
    Route::post('settings/ckmedia', 'SettingsController@storeCKEditorImages')->name('settings.storeCKEditorImages');
    Route::post('settings/update', 'SettingsController@update')->name('settings.update');
    Route::post('settings/update-theme', 'SettingsController@updateThemeSettings')->name('settings.updateTheme');
    Route::get('settings/get-theme-settings', 'SettingsController@getThemeSettings')->name('settings.getThemeSettings');
    Route::get('settings', 'SettingsController@index')->name('settings.index');

    // Front Links
    Route::delete('front-links/destroy', 'FrontLinksController@massDestroy')->name('front-links.massDestroy');
    Route::resource('front-links', 'FrontLinksController');

    // Subscriptions
    Route::delete('subscriptions/destroy', 'SubscriptionsController@massDestroy')->name('subscriptions.massDestroy');
    Route::resource('subscriptions', 'SubscriptionsController');

    // Services
    Route::delete('services/destroy', 'ServicesController@massDestroy')->name('services.massDestroy');
    Route::get('services/list', 'ServicesController@list')->name('services.list');
    Route::get('services/services_by_type', 'ServicesController@servicesByType')->name('services.services_by_type');
    Route::resource('services', 'ServicesController');

    // Dynamic Services
    Route::post('dynamic-services/media', 'DynamicServiceController@storeMedia')->name('dynamic-services.storeMedia');
    Route::delete('dynamic-services/destroy', 'DynamicServiceController@massDestroy')->name('dynamic-services.massDestroy');
    Route::put('dynamic-services/{dynamicService}/program-meetings', 'DynamicServiceController@updateProgramMeetings')->name('dynamic-services.update-program-meetings');
    Route::match(['get', 'post'], 'dynamic-services/meeting-attendance', 'DynamicServiceController@meetingAttendance')->name('dynamic-services.meeting-attendance');
    Route::resource('dynamic-services', 'DynamicServiceController');

    // Projects (Donations)
    Route::delete('projects/destroy', 'ProjectsController@massDestroy')->name('projects.massDestroy');
    Route::resource('projects', 'ProjectsController');

    // Beneficiary Order Donation Allocations
    Route::post('beneficiary-orders/{beneficiaryOrder}/allocate-donation', 'BeneficiaryOrdersController@allocateDonation')
        ->name('beneficiary-orders.allocate-donation');
    Route::delete('beneficiary-orders/{beneficiaryOrder}/donation-allocations/{donationAllocation}', 'BeneficiaryOrdersController@removeDonationAllocation')
        ->name('beneficiary-orders.donation-allocations.destroy');

    // Donators
    Route::delete('donators/destroy', 'DonatorsController@massDestroy')->name('donators.massDestroy');
    Route::resource('donators', 'DonatorsController');

    // Donations
    Route::resource('donations', 'DonationsController')->only(['index', 'create', 'store', 'show']);

    // Dynamic Service Workflows (legacy)
    Route::get('dynamic-service-workflows/{dynamicServiceOrder}', 'DynamicServiceWorkflowController@show')->name('dynamic-service-workflows.show');
    Route::post('dynamic-service-workflows/{workflow}/transition', 'DynamicServiceWorkflowController@transition')->name('dynamic-service-workflows.transition');
    Route::post('dynamic-service-workflows/{workflow}/attendance', 'DynamicServiceWorkflowController@updateAttendance')->name('dynamic-service-workflows.attendance');
    Route::post('dynamic-service-workflows/{workflow}/accounting', 'DynamicServiceWorkflowController@updateAccounting')->name('dynamic-service-workflows.accounting');
    Route::post('dynamic-service-workflows/{workflow}/satisfaction', 'DynamicServiceWorkflowController@updateSatisfaction')->name('dynamic-service-workflows.satisfaction');

    // Workflow Engine (generic workflow instances)
    Route::get('workflow-instances', 'WorkflowInstancesController@index')->name('workflow-instances.index');
    Route::get('workflow-instances/create', 'WorkflowInstancesController@create')->name('workflow-instances.create');
    Route::post('workflow-instances/start', 'WorkflowInstancesController@start')->name('workflow-instances.start');
    Route::get('workflow-instances/{workflowInstance}', 'WorkflowInstancesController@show')->name('workflow-instances.show');
    Route::post('workflow-instances/{workflowInstance}/execute', 'WorkflowInstancesController@executeStep')->name('workflow-instances.execute');

    // Beneficiary Orders
    Route::delete('beneficiary-orders/destroy', 'BeneficiaryOrdersController@massDestroy')->name('beneficiary-orders.massDestroy');
    Route::post('beneficiary-orders/media', 'BeneficiaryOrdersController@storeMedia')->name('beneficiary-orders.storeMedia');
    Route::post('beneficiary-orders/ckmedia', 'BeneficiaryOrdersController@storeCKEditorImages')->name('beneficiary-orders.storeCKEditorImages');
    Route::put('beneficiary-orders/update-status/{beneficiaryOrder}', 'BeneficiaryOrdersController@updateStatus')->name('beneficiary-orders.update-status');
    Route::post('beneficiary-orders/save-signature/{beneficiaryOrder}', 'BeneficiaryOrdersController@saveSignature')->name('beneficiary-orders.save-signature');
    Route::get('beneficiary-orders/signature-download/{beneficiaryOrder}', 'BeneficiaryOrdersController@signatureDownload')->name('beneficiary-orders.signature-download');
    Route::resource('beneficiary-orders', 'BeneficiaryOrdersController');

    // Beneficiary Orders Import
    Route::post('beneficiary-orders/import', 'BeneficiaryOrderImportController@uploadCsv')->name('beneficiary-orders.import');

    // Service Statuses
    Route::delete('service-statuses/destroy', 'ServiceStatusesController@massDestroy')->name('service-statuses.massDestroy');
    Route::resource('service-statuses', 'ServiceStatusesController');

    // Incoming Letters
    Route::delete('incoming-letters/destroy', 'IncomingLettersController@massDestroy')->name('incoming-letters.massDestroy');
    Route::post('incoming-letters/media', 'IncomingLettersController@storeMedia')->name('incoming-letters.storeMedia');
    Route::post('incoming-letters/ckmedia', 'IncomingLettersController@storeCKEditorImages')->name('incoming-letters.storeCKEditorImages');
    Route::resource('incoming-letters', 'IncomingLettersController');

    // Letters Organizations
    Route::delete('letters-organizations/destroy', 'LettersOrganizationsController@massDestroy')->name('letters-organizations.massDestroy');
    Route::resource('letters-organizations', 'LettersOrganizationsController');

    // Outgoing Letters
    Route::delete('outgoing-letters/destroy', 'OutgoingLettersController@massDestroy')->name('outgoing-letters.massDestroy');
    Route::post('outgoing-letters/media', 'OutgoingLettersController@storeMedia')->name('outgoing-letters.storeMedia');
    Route::post('outgoing-letters/ckmedia', 'OutgoingLettersController@storeCKEditorImages')->name('outgoing-letters.storeCKEditorImages');
    Route::resource('outgoing-letters', 'OutgoingLettersController');

    // Courses
    Route::delete('courses/destroy', 'CoursesController@massDestroy')->name('courses.massDestroy');
    Route::post('courses/media', 'CoursesController@storeMedia')->name('courses.storeMedia');
    Route::post('courses/ckmedia', 'CoursesController@storeCKEditorImages')->name('courses.storeCKEditorImages');
    Route::get('courses/qr-attendance/{course}', 'CoursesController@qrAttendance')->name('courses.qr-attendance');
    Route::get('courses/qr-certificate/{course}', 'CoursesController@qrCertificate')->name('courses.qr-certificate');
    Route::resource('courses', 'CoursesController');

    // Course Students
    Route::delete('course-students/destroy', 'CourseStudentsController@massDestroy')->name('course-students.massDestroy');
    Route::resource('course-students', 'CourseStudentsController');

    // Buildings
    Route::delete('buildings/destroy', 'BuildingsController@massDestroy')->name('buildings.massDestroy');
    Route::resource('buildings', 'BuildingsController');

    // Archives
    Route::delete('archives/destroy', 'ArchivesController@massDestroy')->name('archives.massDestroy');
    Route::resource('archives', 'ArchivesController');

    // Letter Archives
    Route::delete('letter-archives/destroy', 'LetterArchivesController@massDestroy')->name('letter-archives.massDestroy');
    Route::resource('letter-archives', 'LetterArchivesController');

    // Beneficiary Archives
    Route::delete('beneficiary-archives/destroy', 'BeneficiaryArchivesController@massDestroy')->name('beneficiary-archives.massDestroy');
    Route::resource('beneficiary-archives', 'BeneficiaryArchivesController');

    // Beneficiary Orders Archives
    Route::delete('beneficiary-orders-archives/destroy', 'BeneficiaryOrdersArchivesController@massDestroy')->name('beneficiary-orders-archives.massDestroy');
    Route::resource('beneficiary-orders-archives', 'BeneficiaryOrdersArchivesController');

    // Beneficiary Un Completed
    Route::delete('beneficiary-un-completeds/destroy', 'BeneficiaryUnCompletedController@massDestroy')->name('beneficiary-un-completeds.massDestroy');
    Route::resource('beneficiary-un-completeds', 'BeneficiaryUnCompletedController');

    // Beneficiary Orders Done
    Route::delete('beneficiary-orders-dones/destroy', 'BeneficiaryOrdersDoneController@massDestroy')->name('beneficiary-orders-dones.massDestroy');
    Route::resource('beneficiary-orders-dones', 'BeneficiaryOrdersDoneController');

    // Beneficiary Orders Rejected
    Route::delete('beneficiary-orders-rejecteds/destroy', 'BeneficiaryOrdersRejectedController@massDestroy')->name('beneficiary-orders-rejecteds.massDestroy');
    Route::resource('beneficiary-orders-rejecteds', 'BeneficiaryOrdersRejectedController');

    // Beneficiary Report
    Route::get('beneficiary-reports/export', 'BeneficiaryReportController@export')->name('beneficiary-reports.export');
    Route::resource('beneficiary-reports', 'BeneficiaryReportController')->only(['index']);

    // Beneficiary Orders Reports
    Route::get('beneficiary-orders-reports/export', 'BeneficiaryOrdersReportsController@export')->name('beneficiary-orders-reports.export');
    Route::resource('beneficiary-orders-reports', 'BeneficiaryOrdersReportsController')->only(['index']);

    // Service Loans Reports
    Route::get('service-loans-reports/export', 'ServiceLoansReportController@export')->name('service-loans-reports.export');
    Route::resource('service-loans-reports', 'ServiceLoansReportController')->only(['index']);

    // Task Reports
    Route::delete('task-reports/destroy', 'TaskReportsController@massDestroy')->name('task-reports.massDestroy');
    Route::resource('task-reports', 'TaskReportsController');

    // Beneficiary Order Followups
    Route::delete('beneficiary-order-followups/destroy', 'BeneficiaryOrderFollowupsController@massDestroy')->name('beneficiary-order-followups.massDestroy');
    Route::post('beneficiary-order-followups/media', 'BeneficiaryOrderFollowupsController@storeMedia')->name('beneficiary-order-followups.storeMedia');
    Route::post('beneficiary-order-followups/ckmedia', 'BeneficiaryOrderFollowupsController@storeCKEditorImages')->name('beneficiary-order-followups.storeCKEditorImages');
    Route::post('beneficiary-order-followups/create', 'BeneficiaryOrderFollowupsController@create')->name('beneficiary-order-followups.create');
    Route::post('beneficiary-order-followups/store', 'BeneficiaryOrderFollowupsController@store')->name('beneficiary-order-followups.store');
    Route::post('beneficiary-order-followups/edit', 'BeneficiaryOrderFollowupsController@edit')->name('beneficiary-order-followups.edit');
    Route::put('beneficiary-order-followups/update/{beneficiaryOrderFollowup}', 'BeneficiaryOrderFollowupsController@update')->name('beneficiary-order-followups.update');
    Route::post('beneficiary-order-followups/destroy', 'BeneficiaryOrderFollowupsController@destroy')->name('beneficiary-order-followups.destroy');

    // Storage Locations
    Route::delete('storage-locations/destroy', 'StorageLocationsController@massDestroy')->name('storage-locations.massDestroy');
    Route::resource('storage-locations', 'StorageLocationsController');

    // Charities
    Route::delete('charities/destroy', 'CharitiesController@massDestroy')->name('charities.massDestroy');
    Route::post('charities/media', 'CharitiesController@storeMedia')->name('charities.storeMedia');
    Route::post('charities/ckmedia', 'CharitiesController@storeCKEditorImages')->name('charities.storeCKEditorImages');
    Route::resource('charities', 'CharitiesController');

    // Building Beneficiary
    Route::delete('building-beneficiaries/destroy', 'BuildingBeneficiaryController@massDestroy')->name('building-beneficiaries.massDestroy');
    Route::resource('building-beneficiaries', 'BuildingBeneficiaryController');

    // Beneficiary Refused
    Route::delete('beneficiary-refuseds/destroy', 'BeneficiaryRefusedController@massDestroy')->name('beneficiary-refuseds.massDestroy');
    Route::resource('beneficiary-refuseds', 'BeneficiaryRefusedController');

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


    // Consultation Types
    Route::delete('consultation-types/destroy', 'ConsultationTypesController@massDestroy')->name('consultation-types.massDestroy');
    Route::resource('consultation-types', 'ConsultationTypesController');

    // Consultants
    Route::delete('consultants/destroy', 'ConsultantsController@massDestroy')->name('consultants.massDestroy');
    Route::post('consultants/media', 'ConsultantsController@storeMedia')->name('consultants.storeMedia');
    Route::resource('consultants', 'ConsultantsController');

    // Consultant Schedules
    Route::delete('consultant-schedules/destroy', 'ConsultantSchedulesController@massDestroy')->name('consultant-schedules.massDestroy');
    Route::resource('consultant-schedules', 'ConsultantSchedulesController');

    // Loans
    Route::delete('loans/destroy', 'LoansController@massDestroy')->name('loans.massDestroy');
    Route::resource('loans', 'LoansController');

    // Beneficiary Field Visibility Settings
    Route::get('beneficiary-field-visibility', 'BeneficiaryFieldVisibilityController@index')->name('beneficiary-field-visibility.index');
    Route::get('beneficiary-field-visibility/{id}/edit', 'BeneficiaryFieldVisibilityController@edit')->name('beneficiary-field-visibility.edit');
    Route::put('beneficiary-field-visibility/{id}', 'BeneficiaryFieldVisibilityController@update')->name('beneficiary-field-visibility.update');
    Route::post('beneficiary-field-visibility/bulk-update', 'BeneficiaryFieldVisibilityController@bulkUpdate')->name('beneficiary-field-visibility.bulk-update');
    Route::post('beneficiary-field-visibility/{id}/toggle-visibility', 'BeneficiaryFieldVisibilityController@toggleVisibility')->name('beneficiary-field-visibility.toggle-visibility');
    Route::post('beneficiary-field-visibility/{id}/toggle-required', 'BeneficiaryFieldVisibilityController@toggleRequired')->name('beneficiary-field-visibility.toggle-required');
    
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
