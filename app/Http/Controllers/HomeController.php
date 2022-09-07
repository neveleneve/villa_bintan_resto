<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Menu;
use App\MenuCategory;
use App\Payment;
use App\Reservation;
use App\ReservedFee;
use App\ReservedMenu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use \PDF;

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

    #region halaman admin

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $datapass = [];
        if (Auth::user()->role == 0) {
            $datareservasimasukhariini = Reservation::where('status', 1)
                ->whereDate('created_at', date('Y-m-d'))
                ->count();
            $databookingmasukhariini = Reservation::where('status', 1)
                ->whereDate('time', date('Y-m-d'))
                ->count();
            $completedreservation = Reservation::where('status', 1)
                ->count();
            $datapass = [
                'reservationtoday' => $datareservasimasukhariini,
                'bookingtoday' => $databookingmasukhariini,
                'completedreservation' => $completedreservation,
            ];
        } else {
            $completedreservation = Reservation::where('status', 1)
                ->where('user_id', Auth::user()->id)
                ->count();
            $datapass = [
                'completedreservation' => $completedreservation,
            ];
        }
        return view('admin.home', $datapass);
    }

    public function reservation()
    {
        // update data reservasi one by one
        $this->checkreservation();
        $no = 1;
        $datareservasi = DB::table('reservations')
            ->select([
                'reservations.id AS id',
                'reservations.reservation_code as codereservation',
                'reservations.nama_pemesan AS pemesan',
                'reservations.time AS reservationtime',
                'reservations.STATUS AS reservasistatus',
                'reservations.reserved_status AS bookingstatus',
                'tables.no_meja AS nomeja',
                DB::raw('(SELECT COUNT( * ) FROM payments WHERE reservation_code = reservations.reservation_code) AS jumlahpembayaran'),
                DB::raw('(SELECT order_id FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1) AS order_id'),
                DB::raw('(SELECT status_code FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1) AS status_code')
            ])
            ->join('tables', 'tables.id', '=', 'reservations.table_id')
            ->orderBy('reservations.reserved_status')
            ->orderBy('reservations.time')
            ->orderBy('status_code')
            ->get();
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

    public function bookedin($id)
    {
        Reservation::where('reservation_code', $id)->update(['reserved_status' => 1]);
        return redirect(route('adminreservation'));
    }

    public function payments()
    {
        $datapayment = Payment::orderBy('id', 'desc')->get();
        $this->checkreservation();
        return view('admin.payment', [
            'payments' => $datapayment
        ]);
    }

    public function checkreservation()
    {
        $payments = Payment::where('status_code', '<>', '200')
            ->get();
        $reservation = Reservation::where('status', 1)
            ->get();
        foreach ($reservation as $key) {
            if ($key->time < date('Y-m-d H:i:s', strtotime('-2 Hours'))) {
                Reservation::where('id', $key->id)
                    ->update([
                        'reserved_status' => 2
                    ]);
            }
        }
        foreach ($payments as $key) {
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
            if (isset($content['transaction_status'])) {
                if ($key->transaction_status != $content['transaction_status']) {
                    Payment::where('id', $key->id)->update([
                        'transaction_status' => $content['transaction_status']
                    ]);
                }
            }
            if ($key->status_code != $content['status_code']) {
                Payment::where('id', $key->id)->update([
                    'status_code' => $content['status_code']
                ]);
            }
        }
    }

    public function menus()
    {
        $data = DB::table('menus')
            ->join('menu_categories', 'menus.id_category', '=', 'menu_categories.id')
            ->select('menus.id', 'menus.name', 'menus.price', 'menus.description', 'menu_categories.id as category_id', 'menus.deleted_at')
            ->orderBy('menu_categories.name')
            ->get();
        $jmlmenupercat = DB::select(DB::raw('SELECT c.id, COUNT( s.NAME ) AS menucount FROM menu_categories c JOIN menus s ON c.id = s.id_category GROUP BY c.id'));
        $datamenu = [];
        foreach ($jmlmenupercat as $key) {
            $datamenu[$key->id] = $key->menucount;
        }
        // dd($datamenu);
        $menuavail = Menu::where('deleted_at', '=', null)
            ->count();
        $menunotavail = Menu::onlyTrashed()
            ->count();
        $cat = MenuCategory::orderBy('name')
            ->get();
        return view('admin.menu', [
            'menu' => $data,
            'jmlmenu' => $datamenu,
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
        // dd($data->all());
        if (isset($data->image)) {
            if (File::exists(public_path('images/menu/' . $data->viewid . '.jpg'))) {
                File::delete(public_path('images/menu/' . $data->viewid . '.jpg'));
            }
            $img = $data->file('image');
            $dest = public_path('/images/menu');
            $img->move($dest, $data->viewid . '.jpg');
        }
        Menu::where('id', $data->viewid)->update([
            'name' => strtolower($data->viewname),
            'price' => $data->viewprice,
            'description' => $data->viewdesc,
            'id_category' => $data->viewcat
        ]);
        return redirect(route('adminmenus'));
    }

    public function deleteimagemenu($id)
    {
        File::delete(public_path('images/menu/' . $id . '.jpg'));
        return redirect(route('adminmenus'));
    }

    public function categories()
    {
        $data = MenuCategory::get();
        return view('admin.category', [
            'category' => $data
        ]);
    }

    public function editcategories(Request $data)
    {
        // dd($data->all());
        MenuCategory::where('id', $data->idkategori)->update([
            'name' => $data->namakategori
        ]);
        return redirect(route('admincategories'));
    }

    public function addcategories(Request $data)
    {
        MenuCategory::insert([
            'name' => $data->nama,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        return redirect(route('admincategories'));
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
        // return view('admin.paymentstatus', [
        //     'payment_data' => $content
        // ]);
    }

    public function report()
    {
        return view('admin.report');
    }

    public function postreport(Request $req)
    {
        return redirect(route('adminreportpreview', [
            'bulan' => $req->bulan,
            'tahun' => $req->tahun,
        ]));
    }

    public function reportpreview(Request $req)
    {
        $datareservasi = DB::select('SELECT
            id, reservation_code, nama_pemesan, time,
            (SELECT SUM(harga * jumlah) FROM reserved_menus WHERE reservation_code = reservations.reservation_code) as totalmenu,
            (SELECT SUM(fee) FROM reserved_fees WHERE reservation_code = reservations.reservation_code) as totalfee
        FROM
            reservations 
        WHERE
            YEAR ( time ) = "' . $req->tahun . '"
            AND MONTH ( time ) = "' . $req->bulan . '"
        ORDER BY time');
        $pdf = PDF::loadView('admin.reportpreview', [
            'data' => $datareservasi,
            'tahun' => $req->tahun,
            'bulan' => $this->bulan($req->bulan)
        ])
            ->setPaper('A4', 'landscape');
        return $pdf->stream('Laporan Pemasukan Restoran.pdf');
    }

    public function strukprint($id)
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

        $pdf = PDF::loadView('admin.strukpreview', [
            'datareservasi' => $datareservasi,
            'datafee' => $datafee,
            'datamenu' => $menu,
            'id' => $id,
        ])
            ->setPaper('A4', 'landscape');
        return $pdf->stream('Struk Pemesanan ' . $id . '.pdf');
    }

    public function bulan($id)
    {
        switch ($id) {
            case '1':
                $namabulan = 'Januari';
                break;
            case '2':
                $namabulan = 'Februari';
                break;
            case '3':
                $namabulan = 'Maret';
                break;
            case '4':
                $namabulan = 'April';
                break;
            case '5':
                $namabulan = 'Mei';
                break;
            case '6':
                $namabulan = 'Juni';
                break;
            case '7':
                $namabulan = 'Juli';
                break;
            case '8':
                $namabulan = 'Agustus';
                break;
            case '9':
                $namabulan = 'September';
                break;
            case '10':
                $namabulan = 'Oktober';
                break;
            case '11':
                $namabulan = 'November';
                break;
            case '12':
                $namabulan = 'Desember';
                break;
        }
        return $namabulan;
    }

    #endregion

    #region halaman customer

    // tampil halaman reservasi
    public function custreservation()
    {
        if (Auth::user()->role == 0) {
            return redirect(route('home'));
        } else {
            return view('reservation');
        }
    }

    // input data reservasi meja
    public function reserve(Request $data)
    {
        // dd($data->all());
        $char = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $reservation_code = Helper::RandomChar($char);
        if (isset($data->meja)) {
            for ($i = 0; $i < count($data->meja); $i++) {
                $datareservasi[$i] = [
                    'reservation_code' => $reservation_code,
                    'user_id' => $data->user_id,
                    'nama_pemesan' => $data->nama,
                    'kontak' => $data->kontak,
                    'table_id' => $data->meja[$i],
                    'time' => $data->tanggal . ' ' . $data->waktu,
                    'status' => 0,
                    'reserved_status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }
            Reservation::insert($datareservasi);
            return redirect(route('choosemenu', ['id' => $reservation_code]));
        } else {
            return redirect(route('reservation'))->with([
                'alert' => 'Check your seat before making the reservation!',
                'tanggal' => $data->tanggal,
                'waktu' => $data->waktu,
                'seat' => $data->seat,
            ]);
        }
    }

    // tampil halaman pesan menu setelah reservasi meja
    /** note
     * 1. sistem func memeriksa apakah id yang digunakan ada pada database
     *      a. jika id tidak tersedia pada database, sistem akan kembali kehalaman reservasi
     *      b. jika id tersedia pada database, sistem akan memeriksa status reservasi.
     *          - jika status reservasi 0, sistem akan mengarahkan ke halaman pemesanan menu
     *          - jika status reservasi 1, sistem akan mengarahkan ke halaman detail reservasi dan pembayaran
     *          - jika status reservasi 2, sistem akan mengarahkan ke halaman nota pembayaran
     */
    public function menureservation($id)
    {
        $check = Reservation::where('reservation_code', $id)->get();
        $statustelat = false;
        if (count($check) == 0) {
            return redirect(route('reservation'));
        } else {
            if ($check[0]->status == 0 && date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($check[0]->time . '- 2 hours'))) {
                $statustelat = true;
            }
            if (count($check) == 0) {
                return redirect(route('reservation'))->with('alert', 'Reservation code is not exist!');
            } else {
                if ($check[0]['status'] == 0) {
                    $data = DB::table('menus')
                        ->join('menu_categories', 'menus.id_category', '=', 'menu_categories.id')
                        ->select('menus.id', 'menus.name', 'menus.price', 'menus.description', 'menu_categories.id as category_id')
                        ->where('menus.deleted_at', null)
                        ->orderBy('menu_categories.name')
                        ->get();
                    $cat = MenuCategory::orderBy('name')
                        ->get();
                    $jmlmenupercat = DB::select(DB::raw('SELECT c.id, COUNT( s.NAME ) AS menucount FROM menu_categories c JOIN menus s ON c.id = s.id_category GROUP BY c.id'));
                    $datamenu = [];
                    foreach ($jmlmenupercat as $key) {
                        $datamenu[$key->id] = $key->menucount;
                    }
                    return view('choosemenu', [
                        'data' => $data,
                        'cat' => $cat,
                        'jmlmenu' => $datamenu,
                        'id' => $id,
                        'telat' => $statustelat,
                    ]);
                } elseif ($check[0]['status'] == 1) {
                    return redirect(route('reservationdetail', [
                        'id' => $id,
                    ]));
                }
            }
        }
    }

    public function reservemenu(Request $data)
    {
        // dd($data->all());
        // kode reservasi
        $reservation_code = $data->id;
        // data pesanan
        $datapesan = [];
        // hitung jumlah menu di db
        if (isset($data->quantity)) {
            $menudb = Menu::get()->count();
            // hitung jumlah menu yang tampil di web
            $jumlahmenu = $data->quantity;
            // hitung jumlah menu yang tampil di web
            $menu = count($data->quantity);
            // list data menu yang dipilih pada tampilan web
            $dataarray = array_filter($data->quantity);
            // list data menu id
            $datamenuid = $data->menu_id;
            // list array key dari menu yang dipilih pada tampilan web
            $dataarraykey = array_keys($dataarray);
            if ($menudb == $menu) {
                for ($i = 0; $i < count($dataarraykey); $i++) {
                    $menudatabase = Menu::where('id', $datamenuid[$dataarraykey[$i]])->get('price');
                    $datapesan[$i] = [
                        'reservation_code' => $reservation_code,
                        'menu_id' => $datamenuid[$dataarraykey[$i]],
                        'harga' => $menudatabase[0]['price'],
                        'jumlah' => $jumlahmenu[$dataarraykey[$i]],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                }
            } else {
                return redirect(route('choosemenu', [
                    'id' => $data->id
                ]))->with([
                    'alert' => 'There is an update on menu. Please re-choose your selected menu!'
                ]);
            }
        }
        $jumlahmeja = Reservation::where('reservation_code', $reservation_code)->count();
        $tablefee = 0;
        if ($datapesan == null) {
            for ($i = 0; $i < $jumlahmeja; $i++) {
                $tablefee += 50000;
            }
        } else {
            $tablefee = 0;
            ReservedMenu::insert($datapesan);
        }
        // dd($tablefee);
        ReservedFee::create([
            'reservation_code' => $reservation_code,
            'fee' => $tablefee,
        ]);
        Reservation::where('reservation_code', $reservation_code)->update([
            'status' => 1
        ]);
        return redirect(route('reservationdetail', ['id' => $data->id]));
    }

    public function reservationdetails($id)
    {
        // cek status pemesanan
        $reservationstatus = Reservation::where('reservation_code', $id)
            ->get(['time', 'status']);

        // dd($reservationstatus);
        if (count($reservationstatus) == 0) {
            return redirect(route('reservation'))->with([
                'alert' => "Reservation code doesn't exist! Please make a reservation first."
            ]);
        } elseif ($reservationstatus[0]['status'] == 0) {
            return redirect(route('choosemenu', [
                'id' => $id
            ]));
        } else {
            $basedata = Reservation::join('tables', 'reservations.table_id', '=', 'tables.id')
                ->where('reservations.reservation_code', $id)
                ->get([
                    'reservations.*',
                    'tables.no_meja',
                    'tables.kapasitas',
                ]);
            $tablefee = ReservedFee::where('reservation_code', $id)->get();
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
            // cek data pembayaran
            $datapayment = Payment::where('reservation_code', $id)->orderBy('created_at', 'DESC')->get();
            // $paymentUrl = null;
            // $random = null;
            if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($reservationstatus[0]['time'] . '- 2 hours'))) {
                if (count($datapayment) > 0) {
                    $paymentUrl = $datapayment[0]['url'];
                    $random = $datapayment[0]['order_id'];
                } else {
                    $paymentUrl = null;
                    $random = null;
                }
            } else {
                if ((count($datapayment) == 0) || ($datapayment[0]['status_code'] == "407")) {
                    // random char for order_id
                    $char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                    $random = Helper::RandomChar($char, 9);
                    $total = 0;
                    $i = 0;
                    // ekstrak data pesanan konsumen
                    foreach ($menu as $key) {
                        $datapesanan[$i] = [
                            'id' => $key->id,
                            'price' => $key->harga,
                            'quantity' => $key->jumlah,
                            'name' => $key->name,
                        ];
                        $total += $key->jumlah * $key->harga;
                        $i++;
                    }
                    if (isset($datapesanan)) {
                        $jumlahpesanan = count($datapesanan);
                    } else {
                        $jumlahpesanan = 0;
                    }
                    $datapesanan[$jumlahpesanan] = [
                        'id' => 'TABLEFEE',
                        'price' => $tablefee[0]['fee'],
                        'quantity' => 1,
                        'name' => 'Table Reservation Fee',
                    ];
                    $total += $tablefee[0]['fee'];
                    // variable untuk menyimpan data pesanan dan pemesan
                    $param = [
                        'transaction_details' => [
                            'order_id' => $random,
                            'gross_amount' => $total,
                        ],
                        'item_details' => $datapesanan,
                        'customer_details' => [
                            'first_name' => $basedata[0]['nama_pemesan'],
                            'phone' => $basedata[0]['kontak'],
                        ]
                    ];
                    // mendapatkan link url pembayaran
                    $this->initPaymentGateway();
                    try {
                        $paymentUrl = \Midtrans\Snap::createTransaction($param)->redirect_url;
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                    Payment::insert([
                        'reservation_code' => $id,
                        'user_id' => Auth::user()->id,
                        'order_id' => $random,
                        'url' => $paymentUrl,
                        'status_code' => '404',
                        'transaction_status' => "Transaction doesn't exist.",
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $paymentUrl = $datapayment[0]['url'];
                    $random = $datapayment[0]['order_id'];
                }
            }
            $endpoint = "https://api.sandbox.midtrans.com/v2/" . $random . "/status";
            $client = new \GuzzleHttp\Client([
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode(env('MIDTRANS_SERVER_KEY')),
                ]
            ]);
            $response = $client->request('GET', $endpoint);
            $content = json_decode($response->getBody(), true);
            $datapayments = Payment::where('order_id', $random)
                ->orderBy('created_at', 'DESC')
                ->get();
            if (isset($content['transaction_status'])) {
                if (count($datapayments) > 0) {
                    if ($datapayments[0]['transaction_status'] != $content['transaction_status']) {
                        Payment::where('order_id', $random)->update([
                            'transaction_status' => $content['transaction_status']
                        ]);
                    }
                }
            }
            if (count($datapayments) > 0) {
                if ($datapayments[0]['status_code'] != $content['status_code']) {
                    Payment::where('order_id', $random)->update([
                        'status_code' => $content['status_code']
                    ]);
                }
            }
            return view('reservationdetail', [
                'reservation_data' => $basedata,
                'reservation_fee_data' => $tablefee,
                'reservation_menu_data' => $menu,
                'no' => 1,
                'payments_url' => $paymentUrl,
                'random' => $random,
                'status_pembayaran' => $content,
                'id' => $id,
                'jumlahpembayaran' => count($datapayments),
                'datapembayaran' => $datapayments,
            ]);
        }
    }

    public function downloadbarcode($id)
    {
        $file = public_path('images/scan_barcode/' . $id . '.png');

        $headers = array(
            'Content-Type: image/png',
        );

        return Response::download($file, 'Table Booking Villa Bintan Resto ' . $id . '.png', $headers);
    }

    public function reservationlist()
    {
        if (Auth::user()->role == 0) {
            return redirect(route('home'));
        } else {
            $this->checkreservation();
            $no = 1;
            $datareservasi = DB::table('reservations')
                ->select([
                    'reservations.id AS id',
                    'reservations.reservation_code as codereservation',
                    'reservations.nama_pemesan AS pemesan',
                    'reservations.time AS reservationtime',
                    'reservations.STATUS AS reservasistatus',
                    'reservations.reserved_status AS bookingstatus',
                    'tables.no_meja AS nomeja',
                    DB::raw('(SELECT COUNT( * ) FROM payments WHERE reservation_code = reservations.reservation_code) AS jumlahpembayaran'),
                    DB::raw('(SELECT order_id FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1) AS order_id'),
                    DB::raw('(SELECT status_code FROM payments WHERE reservation_code = reservations.reservation_code ORDER BY created_at DESC LIMIT 1) AS status_code')
                ])
                ->join('tables', 'tables.id', '=', 'reservations.table_id')
                ->orderBy('reservations.reserved_status')
                ->orderBy('reservations.time')
                ->orderBy('status_code')
                ->where('reservations.user_id', Auth::user()->id)
                ->get();
            return view('admin.reservation', [
                'datareservasi' => $datareservasi,
                'no' => $no,
            ]);
        }
    }

    public function paymentslist()
    {
        $datapayment = Payment::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();
        $this->checkreservation();
        return view('admin.payment', [
            'payments' => $datapayment
        ]);
    }
    #endregion
}
