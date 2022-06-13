<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// route ke tampilan home
Route::get('/', function () {
    return view('welcome');
})->name('dashboard');
// route ke tampilan menu
Route::get('/menu', 'GuestController@menu')->name('menu');
// route ke tampilan tentang villa bintan resto
// Route::get('/about', 'GuestController@about')->name('about');
// route ke tampilan reservasi meja
Route::get('/reservation', 'GuestController@reservation')->name('reservation');
// route ke tampilan memilih menu setelah reservasi meja
Route::get('/reservation/{id}', 'GuestController@menureservation')->name('choosemenu');
// route ke tampilan detail pemesanan setelah memilih menu
Route::get('/reservation-detail/{id}', 'GuestController@reservationdetails')->name('reservationdetail');

// fungsi input reservasi meja
Route::post('/reserve', 'GuestController@reserve')->name('reserve');
// fungsi input menu pilihan setelah reservasi meja
Route::post('/reserve_menu', 'GuestController@reservemenu')->name('reservemenu');

Route::post('/payments/notification', 'PaymentsController@notification')->name('paymentsnotification');
Route::get('/payments/finish', 'PaymentsController@finish')->name('paymentsfinish');
Route::get('/payments/failed', 'PaymentsController@failed')->name('paymentsfailed');
Route::get('/payments/unfinish', 'PaymentsController@unfinish')->name('paymentsunfinish');
Route::get('/payments/status/{id}', 'PaymentsController@status')->name('paymentsstatus');
Route::get('/payment/status/{id}', 'HomeController@paymentstatus')->name('paymentstatus');

// Ajax
Route::get('/table-check', 'AjaxController@tablecheck')->name('tableCheck');

Auth::routes();

Route::get('/dashboard', 'HomeController@index')->name('home');

Route::get('/reservations', 'HomeController@reservation')->name('adminreservation');
Route::get('/reservation/detail/{id}', 'HomeController@reservationdetail')->name('adminreservationdetail');

Route::get('/payments', 'HomeController@payments')->name('adminpayments');

Route::get('/menus', 'HomeController@menus')->name('adminmenus');
Route::get('/menu/delete/{id}', 'HomeController@menudelete')->name('adminmenudelete');
Route::get('/menu/restore/{id}', 'HomeController@menurestore')->name('adminmenurestore');
Route::get('/menu/view', 'AjaxController@menuview')->name('adminmenuview'); //ajax
Route::post('/menu/edit', 'HomeController@menuedit')->name('adminmenuedit');
Route::post('/menu/add', 'HomeController@menuadd')->name('adminmenuadd');
Route::get('/menu/image/delete/{id}', 'HomeController@deleteimagemenu')->name('adminmenuimagedelete');
Route::get('/menu/search', 'AjaxController@menusearch')->name('adminmenusearch');

Route::get('/categories', 'HomeController@categories')->name('admincategories');
Route::post('/categories/add', 'HomeController@addcategories')->name('adminaddcategories');
Route::post('/categories/edit', 'HomeController@editcategories')->name('admineditcategories');
Route::get('/categories/view', 'AjaxController@getDataCategory')->name('getcategorydata');