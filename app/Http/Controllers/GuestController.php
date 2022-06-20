<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Menu;
use App\Payment;
use App\Reservation;
use App\ReservedFee;
use App\ReservedMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Response;

class GuestController extends Controller
{

    public function menu()
    {
        $data = DB::table('menus')
            ->join('menu_categories', 'menus.id_category', '=', 'menu_categories.id')
            ->select('menus.id', 'menus.name', 'menus.price', 'menus.description', 'menu_categories.name as category_name')
            ->where('menus.deleted_at', null)
            ->orderBy('menu_categories.name')
            ->get();
        // dd($data);
        return view('menu', [
            'data' => $data
        ]);
    }
    public function about()
    {
        return view('about');
    }

    # reservation thingy
    // tampil halaman reservasi
    public function reservation()
    {
        return view('reservation');
    }
    // input data reservasi meja
    public function reserve(Request $data)
    {
        $char = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $reservation_code = Helper::RandomChar($char);
        if (isset($data->meja)) {
            $datareservasi = [
                'reservation_code' => $reservation_code,
                'nama_pemesan' => $data->nama,
                'kontak' => $data->kontak,
                'table_id' => $data->meja,
                'time' => $data->tanggal . ' ' . $data->waktu,
                'status' => 0,
            ];
            Reservation::create($datareservasi);
            return redirect(route('choosemenu', ['id' => $reservation_code]));
        } else {
            return redirect(route('reservation'))->with([
                'alert' => 'Check your seat before making the reservation!',
                'nama' => $data->nama,
                'kontak' => $data->kontak,
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
        if (count($check) == 0) {
            return redirect(route('reservation'))->with('alert', 'Reservation code is not exist!');
        } else {
            if ($check[0]['status'] == 0) {
                $data = DB::table('menus')
                    ->join('menu_categories', 'menus.id_category', '=', 'menu_categories.id')
                    ->select('menus.id', 'menus.name', 'menus.price', 'menus.description', 'menu_categories.name as category_name')
                    ->where('menus.deleted_at', null)
                    ->orderBy('menu_categories.name')
                    ->get();
                return view('choosemenu', [
                    'data' => $data,
                    'id' => $id,
                ]);
            } elseif ($check[0]['status'] == 1) {
                return redirect(route('reservationdetail', [
                    'id' => $id
                ]));
            }
        }
    }
    public function reservemenu(Request $data)
    {
        // kode reservasi
        $reservation_code = $data->id;
        // data pesanan
        $datapesan = [];
        // hitung jumlah menu di db
        $menudb = Menu::get()->count();
        if (isset($data->quantity)) {
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
            // dd($menudb);
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
        if ($datapesan == null) {
            $tablefee = 50000;
        } else {
            $tablefee = 0;
            ReservedMenu::insert($datapesan);
        }
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
        if (count($reservationstatus) == 0) {
            return redirect(route('reservation'))->with([
                'alert' => "Reservation code doesn't exist! Please make a reservation first."
            ]);
        } elseif ($reservationstatus[0]['status'] == 0) {
            return redirect(route('reservemenu', ['id' => $id]))->with([
                'alert' => "Your reservation doesn't have the menu details. Please input the menu first."
            ]);
        } else {
            $basedata = Reservation::where('reservation_code', $id)->get();
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
            if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($reservationstatus[0]['time'] . '- 2 hours'))) {
                if (count($datapayment) > 0) {
                    $paymentUrl = $datapayment[0]['url'];
                    $random = $datapayment[0]['order_id'];
                } else {
                    $paymentUrl = null;
                    $random = 'null';
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
        $file = public_path('images/barcode/' . $id . '.png');

        $headers = array(
            'Content-Type: image/png',
        );

        return Response::download($file, 'Table Booking Villa Bintan Resto ' . $id . '.png', $headers);
    }
}
