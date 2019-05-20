<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//This route is for testing purpose only, please delete this in deployed application or leave it as it is
Route::get('testing',function(){
	// return encrypt('client');
	return decrypt('eyJpdiI6IlBMdzdwRUExdlN1OXQxNzVpTXNDckE9PSIsInZhbHVlIjoiZVwvNDluR2RGNk0rZGk1czlwMGw3N3c9PSIsIm1hYyI6IjZlZWQ4NGIwYzYzNGZhZDg2YTFmMGRjNGVmMmIzODg0ZDkyOWJhMzFkYjFjNzc5Mjk4MjZmOTM1NjE4ZDM5MWUifQ==');
});

Auth::routes();

Route::get('support', 'MailController@support');
Route::post('support', 'MailController@support')->name('support');

Route::middleware(['auth'])->group(function () {
	Route::group(['prefix' => 'admin', 'middleware' => ['role:superAdmin']], function() {
		Route::resource('roles','RoleController');
		Route::resource('users','UserRoleController');
		Route::post('assignSupportTask','HomeController@assignSupportTask')->name('assignSupportTask');
	});
	Route::get('/', 'HomeController@index')->name('dashboard');
	Route::get('/check_in_out', 'AttendanceController@index')->name('attendance.index');
	Route::get('/attendance', 'AttendanceController@list')->name('attendance.list');
	Route::post('/attendance', 'AttendanceController@list')->name('attendance.search');
	Route::get('/attendance/details/{client_id}/{employee_id}/{date}', 'AttendanceController@details')->name('attendance.details');
	Route::post('/checkin', 'AttendanceController@checkin')->name('attendance.checkin');
	Route::post('/checkout', 'AttendanceController@checkout')->name('attendance.checkout');
	Route::post('/ajax_in_out_stat', 'AttendanceController@ajax_in_out_stat')->name('ajax.in_out_stat');

	Route::get('/incident_report', 'IncidentReportController@incident_report')->name('incident.create');
	Route::post('/incident_report', 'IncidentReportController@incident_report')->name('incident.store');
	Route::post('/ajax_incident_report_details', 'IncidentReportController@ajax_incident_report_details')->name('incident.view');
	Route::get('/print_incident_report/{id}', 'IncidentReportController@print_incident_report')->name('incident.print');

	Route::get('/leave_application','CoreController@leave_requests')->name('leave_request.index');
	Route::post('/leave_application','CoreController@leave_requests')->name('leave_request.store');

	//this goes to admin route
	Route::post('/updateLeaveRequestStatus','CoreController@updateStatus')->name('leave_request.status');

	Route::get('/scanner', function(){
			return view('backend.pages.scanner');
		})->name('scanner');
	Route::post('/qr_login', 'AttendanceController@ajax_qr_login')->name('ajax.qrLogin');
	Route::get('/siteAttendance','AttendanceController@site_attendance')->name('site.attendance');
	Route::post('/siteAttendance','AttendanceController@site_attendance')->name('site.attendance.search');

	Route::get('/mail', 'MailController@index')->name('mail.index');
	Route::get('/compose', 'MailController@composeMail')->name('mail.compose');
	Route::get('/view', 'MailController@viewMail')->name('mail.view');

	// Route::get('/profile/{id}', 'UserController@profile_edit')->name('profile.edit');
	Route::get('/edit-password', 'UserController@password_edit')->name('password.edit');
	Route::post('/update-password/{id}', 'UserController@password_update')->name('password.update');
	
	//Allow these routes for admin and contractor only
	Route::middleware(['role:superAdmin|contractor'])->group(function () {
		Route::get('/company', 'UserController@index')->name('user_company.index');
		Route::get('/employees', 'UserController@index')->name('user_employee.index');

		Route::middleware(['role:superAdmin'])->group(function () {
			Route::get('/contractors', 'UserController@index')->name('user_contractor.index');
		});

		Route::get('/clients', 'UserController@index')->name('user_client.index');
		Route::post('/add_user', 'UserController@store')->name('user.store');
		Route::get('/edit_user/{id}', 'UserController@edit')->name('user.edit');
		Route::post('/ajax_delete_documents', 'UserController@ajax_delete_documents');
		Route::post('/ajax_user_details', 'UserController@ajax_user_details')->name('user.view');

		Route::post('/update_user/{id}', 'UserController@update')->name('user.update');
		Route::get('/delete_user/{id}', 'UserController@destroy')->name('user.delete');

		Route::get('/wages','WagesController@index')->name('wages.index');
		Route::post('/wages','WagesController@store')->name('wages.store');
		Route::post('/wages/{id}','WagesController@destroy')->name('wages.destroy');

		Route::get('/roster','RosterController@index')->name('roster.index');
		Route::delete('/deleteRoster','RosterController@destroy')->name('roster.destroy');
		Route::post('/ajax_update_roster','RosterController@ajax_update_roster')->name('roster.update');
		Route::post('/ajax_store_roster','RosterController@ajax_store_roster')->name('roster.store');

		Route::get('/roster-variation','RosterVariationController@index')->name('roster_variation.index');
		Route::post('/roster-variation/accept/{id}/{date}','RosterVariationController@statusAccept')->name('roster_variation.approve_status');
		Route::post('/roster-variation/decline/{id}/{date}','RosterVariationController@statusDecline')->name('roster_variation.decline_status');

		Route::get('/site','SiteController@index')->name('site.index');
		Route::post('/site','SiteController@store')->name('site.store');
		Route::post('/store_room','SiteController@store_room')->name('room.store');
		Route::get('/generate_qr/{arr}', 'SiteController@generate_qr')->name('generate.qr');
		Route::post('/site/delete_room/{id}','SiteController@delete_room')->name('room.destroy');
		
		Route::get('/questionTemplate','QuestionTemplateController@index')->name('question.index');
		Route::get('/questionTemplate/add','QuestionTemplateController@addMore')->name('question.add');
		Route::post("/questionTemplate/add","QuestionTemplateController@addMorePost");
		Route::get('/questionTemplate/{id}','QuestionTemplateController@destroy')->name('question.destroy');

	});	
	Route::middleware(['permission:import_export_excel'])->group(function () {
		Route::get('/export_excel/{id}', 'CoreController@export_to_excel')->name('export_to_excel');
		Route::post('/import_excel', 'CoreController@import_from_excel')->name('import_from_excel');
	});

});

