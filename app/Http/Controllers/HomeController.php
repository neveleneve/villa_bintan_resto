<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuCategory;
use App\Payment;
use App\Reservation;
use App\ReservedFee;
use App\ReservedMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.home');
    }

    public function reservation()
    {
        // update data reservasi one by one
        $i = 0;
        $datapayment = Payment::get();
        foreach ($datapayment as $key) {
            $endpoint = "https://api.sandbox.midtrans.com/v2/" . $key->order_id . "/status";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode(env('MIDTRANS_SERVER_KEY')),
                ]
            ]);
            $response = $client->request('GET', $endpoint);
            $content = json_decode($response->getBody(), true);
            if ($content['status_code'] != $key->status_code) {
                if (isset($content['transaction_status'])) {
                    Payment::where('order_id', $key->order_id)->update([
                        'transaction_status' => $content['transaction_status']
                    ]);
                }
                Payment::where('order_id', $key->order_id)->update([
                    'status_code' => $content['status_code']
                ]);
            }
            $i += 1;
        }
        // proses tampil data
        $no = 1;
        $datareservasi = DB::select('SELECT
        reservations.id AS id,
        reservations.reservation_code AS codereservation,
        reservations.nama_pemesan AS pemesan,
        reservations.time AS reservationtime,
        reservations.STATUS AS reservasistatus,
        `tables`.no_meja AS nomeja,
        ( SELECT COUNT( * ) FROM payments WHERE reservation_code = reservations.reservation_code ) AS jumlahpembayaran,
        ( SELECT order_id FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1 ) AS order_id,
        ( SELECT status_code FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1 ) AS status_code 
        FROM
        reservations
        JOIN `tables` ON `tables`.id = reservations.table_id 
        ORDER BY id DESC');
        return view('admin.reservation', [
            'datareservasi' => $datareservasi,
            'no' => $no,
        ]);
    }

    public function reservationdetail($id)
    {
        $datareservasi = DB::select('SELECT
        reservations.id AS id,
        reservations.reservation_code AS codereservation,
        reservations.nama_pemesan AS pemesan,
        reservations.kontak AS kontak,
        reservations.time AS reservationtime,
        reservations.STATUS AS reservasistatus,
        `tables`.no_meja AS nomeja,
        ( SELECT COUNT( * ) FROM payments WHERE reservation_code = reservations.reservation_code ) AS jumlahpembayaran,
        ( SELECT order_id FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1 ) AS order_id,
        ( SELECT status_code FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1 ) AS status_code 
        FROM reservations JOIN `tables` ON `tables`.id = reservations.table_id where reservations.reservation_code =  "' . $id . '" ORDER BY id DESC');

        $datafee = ReservedFee::where('reservation_code', $id)
            ->get();

        $menu = DB::table('reserved_menus')
            ->join('menus', 'reserved_menus.menu_id', '=', 'menus.id')
            ->select([
                'menus.id',
                'menus.name',
                'reserved_menus.harga',
                'reserved_menus.jumlah'
            ])
            ->where('reserved_menus.reservation_code', $id)
            ->get();

        return view('admin.reservationdetail', [
            'datareservasi' => $datareservasi,
            'pesanan' => $menu,
            'datafee' => $datafee,
        ]);
    }

    public function payments()
    {
        $datapayment = Payment::get();
        return view('admin.payment', [
            'payments' => $datapayment
        ]);
    }

    public function menus()
    {
        $data = DB::table('menus')
            ->join('menu_categories', 'menus.id_category', '=', 'menu_categories.id')
            ->select('menus.id', 'menus.name', 'menus.price', 'menus.description', 'menu_categories.name as category_name', 'menus.deleted_at')
            ->orderBy('menu_categories.name')
            ->get();
        $menuavail = Menu::where('deleted_at', '=', null)->count();
        $menunotavail = Menu::onlyTrashed()->count();
        $cat = MenuCategory::get();
        return view('admin.menu', [
            'menu' => $data,
            'menuavail' => $menuavail,
            'menunot' => $menunotavail,
            'cat' => $cat
        ]);
    }
    public function menuadd(Request $data)
    {
        $id = DB::select("SHOW TABLE STATUS LIKE 'menus'");
        $next_id = $id[0]->Auto_increment;
        if (isset($data->image)) {
            $img = $data->file('image');
            $dest = public_path('/images/menu');
            $img->move($dest, $next_id . '.jpg');
        }
        Menu::insert([
            'id_category' => $data->cat,
            'name' => $data->name,
            'price' => $data->price,
            'description' => $data->desc,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'deleted_at' => null
        ]);
        return redirect(route('adminmenus'));
    }
    public function menudelete($id)
    {
        // $datamenu = Menu::where('id', $id)->get();
        // if (is_file(public_path('images/menu/' . $datamenu[0]['id'] . '.jpg'))) {
        //     File::delete(public_path('images/menu/' . $datamenu[0]['id'] . '.jpg'));
        // }
        Menu::where('id', $id)->delete();
        return redirect(route('adminmenus'));
    }
    public function menurestore($id)
    {
        Menu::onlyTrashed()->where('id', $id)->restore();
        return redirect(route('adminmenus'));
    }
    public function menuedit(Request $data)
    {
        Menu::where('id', $data->viewid)->update([
            'name' => strtolower($data->viewname),
            'price' => $data->viewprice,
            'description' => $data->viewdesc . '.',
            'id_category' => $data->viewcat
        ]);
        return redirect(route('adminmenus'));
    }

    public function categories()
    {
        $data = MenuCategory::get();
        return view('admin.category', [
            'category' => $data
        ]);
    }

    public function paymentstatus($id)
    {
        $payment = Payment::where('order_id', $id)->get();
        if (count($payment) > 0) {
            $endpoint = "https://api.sandbox.midtrans.com/v2/" . $id . "/status";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode(env('MIDTRANS_SERVER_KEY')),
                ]
            ]);
            $response = $client->request('GET', $endpoint);
            $content = json_decode($response->getBody(), true);
        } else {
            return redirect(route('adminpayments'));
        }
        // dd($content);
        return view('admin.paymentstatus', [
            'payment_data' => $content
        ]);
    }
}
