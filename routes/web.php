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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
	Route::get('/home', 'HomeController@index')->name('home');
	Route::get('/', 'HomeController@index')->name('dashboard');
	Route::get('/check_in_out', 'AttendanceController@index')->name('attendance.index');
	Route::get('/attendance', 'AttendanceController@list')->name('attendance.list');
	Route::post('/attendance', 'AttendanceController@store')->name('attendance.store');
	Route::get('/attendance/details/{id}', 'AttendanceController@details')->name('attendance.details');
	Route::post('/checkin', 'AttendanceController@checkin')->name('attendance.checkin');
	Route::post('/checkout', 'AttendanceController@checkout')->name('attendance.checkout');

	Route::get('/mail', 'MailController@index')->name('mail.index');
	Route::get('/compose', 'MailController@composeMail')->name('mail.compose');
	Route::get('/view', 'MailController@viewMail')->name('mail.view');

	//Allow these routes for admin only
	Route::middleware(['can:isAdmin'])->group(function () {
		Route::get('/employees', 'UserController@index')->name('user_employee.index');
		Route::get('/contractors', 'UserController@index')->name('user_contractor.index');
		Route::get('/clients', 'UserController@index')->name('user_client.index');
		Route::post('/add_user', 'UserController@store')->name('user.store');

		Route::get('/wages','WagesController@index')->name('wages.index');
		Route::post('/wages','WagesController@store')->name('wages.store');
		Route::post('/wages/{id}','WagesController@destroy')->name('wages.destroy');

		Route::get('/roster','RosterController@index')->name('roster.index');
		Route::post('/add_roster','RosterController@store')->name('roster.store');

		Route::get('/roster-variation','RosterVariationController@index')->name('roster_variation.index');
		Route::post('/roster-variation/accept/{id}','RosterVariationController@statusAccept')->name('roster_variation.approve_status');
		Route::post('/roster-variation/decline/{id}','RosterVariationController@statusDecline')->name('roster_variation.decline_status');

		Route::get('/site','SiteController@index')->name('site.index');
		Route::post('/site','SiteController@store')->name('site.store');
		Route::post('/store_room','SiteController@store_room')->name('room.store');
		Route::get('/generate_qr/{id}', 'SiteController@generate_qr')->name('generate.qr');

		Route::get('/scanner', function(){
			return view('backend.pages.scanner');
		})->name('scanner');

		Route::get('/siteAttendance','AttendanceController@site_attendance')->name('site.attendance');
		Route::post('/qr_login', 'AttendanceController@ajax_qr_login')->name('ajax.qrLogin');
		
	});
	
});
