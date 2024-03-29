<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Milon\Barcode\DNS1D;
use Milon\Barcode\DNS2D;

// route ke tampilan home
Route::get('/', function () {
    // echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG('AB12msaodh', 'C39+',3,33) . '" alt="barcode"   />';
    return view('welcome');
})->name('dashboard');
// route ke tampilan menu
Route::get('/menu', 'GuestController@menu')->name('menu');
// route ke tampilan tentang villa bintan resto
// Route::get('/about', 'GuestController@about')->name('about');

#region perubahan ke HomeController agak bisa diakses authenticated user role 1 

// route ke tampilan reservasi meja
Route::get('/reservation', 'HomeController@custreservation')->name('reservation');
// route ke tampilan memilih menu setelah reservasi meja
Route::get('/reservation/{id}', 'HomeController@menureservation')->name('choosemenu');
// route ke tampilan detail pemesanan setelah memilih menu
Route::get('/reservation-detail/{id}', 'HomeController@reservationdetails')->name('reservationdetail');
Route::get('/reservation/barcode/{id}', 'HomeController@downloadbarcode')->name('downloadbarcode');
// fungsi input reservasi meja
Route::post('/reserve', 'HomeController@reserve')->name('reserve');
// fungsi input menu pilihan setelah reservasi meja
Route::get('/reserve-menu', 'HomeController@reservemenu')->name('reservemenu');

Route::get('/reservations-list', 'HomeController@reservationlist')->name('reservationlist');
Route::get('/payments-list', 'HomeController@paymentslist')->name('paymentslist');

#endregion

Route::post('/payments/notification', 'PaymentsController@notification')->name('paymentsnotification');
Route::get('/payments/finish', 'PaymentsController@finish')->name('paymentsfinish');
Route::get('/payments/failed', 'PaymentsController@failed')->name('paymentsfailed');
Route::get('/payments/unfinish', 'PaymentsController@unfinish')->name('paymentsunfinish');
Route::get('/payments/status/{id}', 'PaymentsController@status')->name('paymentsstatus');
Route::get('/payment/status/{id}', 'HomeController@paymentstatus')->name('paymentstatus');

// Ajax
Route::get('/table-check', 'AjaxController@tablecheck')->name('tableCheck');
Route::get('/check-reservation', 'AjaxController@reservationcheck')->name('reservationcheck');

Auth::routes([
    'reset' => false
]);

Route::get('/dashboard', 'HomeController@index')->name('home');

Route::get('/reservations', 'HomeController@reservation')->name('adminreservation');
Route::get('/reservation/detail/{id}', 'HomeController@reservationdetail')->name('adminreservationdetail');
Route::get('/reservations/search', 'AjaxController@reservationssearch')->name('adminreservationssearch');
Route::get('/reservations/bookedin/{id}', 'HomeController@bookedin')->name('bookedin');
Route::get('/struk/preview/{id}', 'HomeController@strukprint')->name('adminprintstruk');

Route::get('/payments', 'HomeController@payments')->name('adminpayments');
Route::get('/payments/search', 'AjaxController@paymentssearch')->name('adminpaymentssearch');

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

Route::get('/report', 'HomeController@report')->name('adminreport');
Route::post('/report', 'HomeController@postreport')->name('adminreportpost');
Route::get('/report/preview', 'HomeController@reportpreview')->name('adminreportpreview');
