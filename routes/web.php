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
Route::get('refresh-csrf', function(){
    return csrf_token();
});
Route::get('testing','Tests\TestController@index');
Route::post('testing','Tests\TestController@get');



Route::get('redirect/{driver}', 'Auth\LoginController@redirectToProvider')
    ->name('login.provider')
    ->where('driver', implode('|', config('auth.socialite.drivers')));

Route::get('{driver}/callback', 'Auth\LoginController@handleProviderCallback')
    ->name('login.callback')
    ->where('driver', implode('|', config('auth.socialite.drivers')));

Route::get('support', 'MailController@support');
Route::post('support', 'MailController@support')->name('support');

Route::middleware(['auth'])->group(function () {
	Route::group(['prefix' => 'admin', 'middleware' => ['role:superAdmin']], function() {
		Route::resource('roles','RoleController');
		Route::resource('users','UserRoleController');
		Route::post('assignSupportTask','HomeController@assignSupportTask')->name('assignSupportTask');
	});
	Route::get('/', 'HomeController@index')->name('dashboard');
	Route::post('set_sidebar','CoreController@ajax_set_sidebar');
	Route::get('/check_in_out', 'AttendanceController@index')->name('attendance.index');
	Route::get('/attendance', 'AttendanceController@list')->name('attendance.list');
	Route::post('/attendance', 'AttendanceController@list')->name('attendance.search');
	Route::get('/attendance/details/{client_id}/{employee_id}/{date}', 'AttendanceController@details')->name('attendance.details');
	Route::post('/checkin', 'AttendanceController@checkin')->name('attendance.checkin');
	Route::post('/checkout', 'AttendanceController@checkout')->name('attendance.checkout');
	Route::post('/ajax_in_out_stat', 'AttendanceController@ajax_in_out_stat')->name('ajax.in_out_stat');

	Route::group(['prefix' => 'incidentReport'], function() {
		Route::get('/pending', 'IncidentReportController@incident_report')->name('incident.pending');
		Route::get('/approved', 'IncidentReportController@incident_report')->name('incident.approved');
		Route::post('/search', 'IncidentReportController@incident_report')->name('incident.search');
		Route::post('/store', 'IncidentReportController@store')->name('incident.store');
	});

	Route::post('/ajax_incident_report_details', 'IncidentReportController@ajax_incident_report_details')->name('incident.view');
	Route::get('/print_incident_report/{id}', 'IncidentReportController@print_incident_report')->name('incident.print');
	Route::post('/updateIncidentStatus', 'IncidentReportController@updateIncidentStatus')->name('incident.update');

	Route::group(['prefix' => 'leaveApplications'], function() {
		Route::post('/create','LeaveRequestController@createLeaveRequests')->name('leaveRequest.store');
		Route::get('/pending','LeaveRequestController@leaveRequests')->name('leaveRequest.pending');
		Route::get('/approved','LeaveRequestController@leaveRequests')->name('leaveRequest.approved');
		Route::get('/denied','LeaveRequestController@leaveRequests')->name('leaveRequest.denied');
		Route::get('/archived','LeaveRequestController@leaveRequests')->name('leaveRequest.archived');
		Route::post('/search','LeaveRequestController@leaveRequests')->name('leaveRequest.search');
		Route::delete('/archive','LeaveRequestController@archiveLeaveRequests')->name('leaveRequest.archive');
		Route::post('/undoArchive','LeaveRequestController@undoArchiveLeaveApplication')->name('leaveRequest.undoArchive');
	});
	
	
	//this goes to admin route
	Route::post('/updateLeaveRequestStatus','LeaveRequestController@updateStatus')->name('leave_request.status');
	Route::post('/ajaxUserLeaveRecord','LeaveRequestController@ajaxUserLeaveRecord')->name('leaveRequest.record');

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
	
	Route::get('/roster','RosterController@index')->name('roster.index');
	Route::post('/roster','RosterController@index')->name('roster.index');

	Route::get('/sheets','RosterController@sheets')->name('roster.sheets');

	//Allow these routes for admin and contractor only
	Route::middleware(['role:superAdmin|contractor'])->group(function () {
		Route::get('/company', 'UserController@index')->name('user_company.index');
		Route::get('/employees', 'UserController@index')->name('user_employee.index');

		//SuperAdmin specific roles
		Route::middleware(['role:superAdmin'])->group(function () {
			Route::get('/contractors', 'UserController@index')->name('user_contractor.index');

			//Add or remove currency NOTE:: this is not in use
			Route::get('/systemSettings', 'CoreController@sysIndex')->name('system.index');
		});

		Route::post('/rosterNotify', 'RosterController@rosterNotify')->name('roster.notify');

		Route::get('/clients', 'UserController@index')->name('user_client.index');
		Route::post('/add_user', 'UserController@store')->name('user.store');
		Route::get('/edit_user/{id}', 'UserController@edit')->name('user.edit');
		Route::post('/ajax_delete_documents', 'UserController@ajax_delete_documents');
		Route::post('/ajax_user_details', 'UserController@ajax_user_details')->name('user.view');

		Route::post('/update_user/{id}', 'UserController@update')->name('user.update');
		Route::get('/delete_user/{id}', 'UserController@destroy')->name('user.delete');

		Route::get('/wages','WagesController@index')->name('wages.index');
		Route::post('/wages', 'WagesController@index')->name('wages.search');
		Route::post('/storeWages','WagesController@store')->name('wages.store');
		Route::get('/deleteWages/{id}','WagesController@destroy')->name('wages.destroy');
		Route::post('/editWages','WagesController@edit')->name('wages.edit');
		Route::post('/updateWages','WagesController@update')->name('wages.update');
		
		Route::delete('/deleteRoster','RosterController@destroy')->name('roster.destroy');
		Route::post('/ajax_update_roster','RosterController@ajax_update_roster')->name('roster.update');
		Route::post('/ajax_store_roster','RosterController@ajax_store_roster')->name('roster.store');
		Route::post('/ajaxCheckIfRosterExists','RosterController@ajaxCheckIfRosterExists')->name('roster.check');

		Route::get('/roster-variation','RosterVariationController@index')->name('roster_variation.index');
		Route::post('approveVariation','RosterVariationController@approveVariation')->name('variation.approve');
		Route::post('declineVariation','RosterVariationController@declineVariation')->name('variation.decline');

		Route::get('/site','SiteController@index')->name('site.index');
		Route::post('/site','SiteController@index')->name('site.search');
		Route::post('/siteStore','SiteController@store')->name('site.store');
		Route::post('/store_room','SiteController@store_room')->name('room.store');
		Route::get('/generate_qr/{arr}', 'SiteController@generate_qr')->name('generate.qr');
		Route::get('/deleteRoom/{id}','SiteController@delete_room')->name('room.destroy');
		Route::get('/deleteBuilding/{id}','SiteController@delete_building')->name('building.destroy');
		
		Route::get('/questionTemplate','QuestionTemplateController@index')->name('question.index');
		Route::get('/questionTemplate/add','QuestionTemplateController@addMore')->name('question.add');
		Route::post("/questionTemplate/add","QuestionTemplateController@addMorePost");
		Route::get('/questionTemplate/{id}','QuestionTemplateController@destroy')->name('question.destroy');



	});	
	Route::middleware(['permission:import_export_excel'])->group(function () {
		Route::post('/exportExcel', 'CoreController@export_to_excel')->name('export.excel');
		Route::post('/import_excel/{type}', 'CoreController@import_from_excel')->name('import_from_excel');
	});

	Route::get('reports/','ReportController@index')->name('report.index');
	Route::post('wagesFilterItems','ReportController@wagesFilterItems')->name('wagesReport.filter');
	Route::post('wagesReport','ReportController@wagesReport')->name('wagesReport.render');
	// Route::get('/reports/{name}',function(){
	// 	return redirect('/reports');
	// })->where('name','[A-Za-z]+');

});

Auth::routes();