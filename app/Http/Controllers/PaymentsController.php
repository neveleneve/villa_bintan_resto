<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Reservation;
use App\ReservedFee;
use App\ReservedMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function notification(Request $data)
    {
        dd($data);
    }

    public function finish(Request $request)
    {
        $orderid  = $request->order_id;
        $datapayment = Payment::where('order_id', $orderid)->get();
        $reservationcode = $datapayment[0]['reservation_code'];
        return redirect(
            route('paymentsstatus', [
                'id' => $reservationcode
            ])
        );
    }
    public function failed()
    {
        # code...
    }
    public function unfinish()
    {
        # code...
    }
    public function status($id)
    {
        $datareservasi = Reservation::where('reservation_code', $id)->get();
        $datamenureservasi = DB::table('reserved_menus')
            ->join('menus', 'reserved_menus.menu_id', '=', 'menus.id')
            ->select([
                'menus.id',
                'menus.name',
                'reserved_menus.harga',
                'reserved_menus.jumlah'
            ])
            ->where('reserved_menus.reservation_code', $id)
            ->get();
        $datafeereservasi = ReservedFee::where('reservation_code', $id)->get();
        $datapayment = Payment::where('reservation_code', $id)->orderBy('created_at', 'DESC')->get();
        return view('paymentsstatus', [
            'reservation' => $datareservasi,
            'reservationmenu' => $datamenureservasi,
            'reservationfee' => $datafeereservasi,
            'payment' => $datapayment,
            'no' => 1,
            'id' => $id,
        ]);
    }    
}
