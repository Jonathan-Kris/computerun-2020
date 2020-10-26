<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    return view('static.index');
});
// Route::get('/uses', function () {
//     return view('static.uses');
// });
Route::get('/sponsor-us', function () {
    return view('static.sponsor-us');
});
Route::get('/event-template', function () {
    return view('static.event-template');
});

/* Business-IT Competitions */
Route::get('/bcase', function () {
    return view('static.bcase');
});
Route::get('/moapps', function () {
    return view('static.moapps');
});

/* Mini E-Sports Competitions */
Route::get('/ml', function () {
    return view('static.ml');
});
Route::get('/pubg', function () {
    return view('static.pubg');
});
Route::get('/valorant', function () {
    return view('static.valorant');
});

/* Webinars */
// Route::get('/webinar-bchain', function () {
//     return view('static.webinar-bchain');
// });
Route::get('/webinar-covid', function () {
    return view('static.webinar-covid');
});
Route::get('/webinar-digital', function () {
    return view('static.webinar-digital');
});
Route::get('/webinar-mobile', function () {
    return view('static.webinar-mobile');
});

Route::get('/contact', function () {
    return view('static.contact');
});

Route::get('/twibbon', function () {
    return redirect('/docs/Twibbon-Computerun2020.png');
});

// Route::get('/regist-webinar', function () {
//     return view('regist-webinar');
// });
// Route::get('/regist-competition', function () {
//     return view('regist-competition');
// });
Route::view('userview', "registration");
Route::post('postcontroller', 'PostController@formSubmit');


// Login / User Dashboard
// Route::resource('/login', 'TicketStatusController');
// Route::get('/logout', 'TicketStatusController@logout');

Auth::routes();
Route::post('/changeaccountdetails', 'UserSettingsController@updateContacts');

// Get user details (for registration)
Route::post('/getuserdetails', 'UserSettingsController@getUserDetails');

// Handle registration
Route::get('/register/{id}', 'UserSettingsController@registrationRedirectHandler');
Route::post('/registerevent', 'UserSettingsController@registerEvent');

// Handle payments
Route::get('/pay/{paymentcode}', 'UserSettingsController@paymentIndex');
Route::post('/pay/{paymentcode}', 'UserSettingsController@paymentHandler');

// User Dashboard
Route::get('/home', 'HomeController@index')->name('dashboard.home');

// Administration Panel
Route::get('/admin', function () {
    return redirect('/home');
});
// Route::get('/admin/{path}', 'AdminController@index');
Route::get('/admin/downloadFile/{file_id}', 'AdminController@downloadFromFileId');
Route::get('/admin/events', 'AdminController@getEventsList');
Route::get('/admin/event/{event_id}', 'AdminController@getEventParticipants');
Route::post('/admin/event/{event_id}', 'AdminController@postEventParticipants');
Route::get('/admin/users', 'AdminController@getAllUsers');
Route::post('/admin/users', 'AdminController@postAllUsers');