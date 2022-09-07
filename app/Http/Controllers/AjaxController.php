<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuCategory;
use App\Payment;
use App\Reservation;
use App\Table;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class AjaxController extends Controller
{
    public function tablecheck(Request $ajax)
    {
        $data = null;
        $no = 1;
        if ($ajax->ajax()) {
            $tabeldata = DB::select("select * from tables 
            where not exists (select * from reservations where (reservations.time = '" . $ajax->tanggal . " " . $ajax->jam . "' and reservations.status > 0) and
            tables.id = reservations.table_id) 
            and tables.kapasitas >= " . $ajax->jumlah . "
            order by kapasitas");
        }
        foreach ($tabeldata as $key) {
            $data .= '<tr><td>' . $no . '</td><td>Nomor ' . $key->no_meja . '</td><td>' . $key->kapasitas
                . '</td><td><div class="form-check"><label class=form-check-label for=meja' . $key->id . '><input class=form-check-input type=checkbox name=meja[] id=meja' . $key->id . ' value=' . $key->id . ' >&nbsp;Pilih Meja</label></div></td></tr>';
            $no++;
        }
        return Response($data);
    }

    public function menuview(Request $ajax)
    {
        // $data = null;
        if ($ajax->ajax()) {
            $datamenu = Menu::withTrashed()->where('id', $ajax->id)->get();
            // cek jika gambar ada di images/menu
            if (is_file(public_path('images/menu/' . $datamenu[0]['id'] . '.jpg'))) {
                $data = [
                    'id' => $datamenu[0]->id,
                    'name' => ucwords($datamenu[0]->name),
                    'desc' => $datamenu[0]->description,
                    'cat' => $datamenu[0]->id_category,
                    'price' => $datamenu[0]->price,
                    'src' => URL::to('/images/menu/' . $datamenu[0]['id'] . '.jpg'),
                    'hasimage' => true,
                    'link' => route('adminmenuimagedelete', ['id' => $datamenu[0]->id])
                ];
            } else {
                $data = [
                    'id' => $datamenu[0]->id,
                    'name' => ucwords($datamenu[0]->name),
                    'desc' => $datamenu[0]->description,
                    'cat' => $datamenu[0]->id_category,
                    'price' => $datamenu[0]->price,
                    'src' =>  URL::to('/images/default.jpg'),
                    'hasimage' => false,
                    'link' => null
                ];
            }
            return Response($data);
        }
    }

    public function menusearch(Request $ajax)
    {
        $data = null;
        if ($ajax->ajax()) {
            if ($ajax->key != null || $ajax->key != '') {
                $tabeldata = DB::table('menus')
                    ->join('menu_categories', 'menus.id_category', '=', 'menu_categories.id')
                    ->select('menus.id', 'menus.name', 'menus.price', 'menus.description', 'menu_categories.name as category_name', 'menus.deleted_at')
                    ->where('menus.name', 'LIKE', '%' . $ajax->key . '%')
                    ->orderBy('menu_categories.name')
                    ->get();
            } else {
                $tabeldata = DB::table('menus')
                    ->join('menu_categories', 'menus.id_category', '=', 'menu_categories.id')
                    ->select('menus.id', 'menus.name', 'menus.price', 'menus.description', 'menu_categories.name as category_name', 'menus.deleted_at')
                    ->orderBy('menu_categories.name')
                    ->get();
            }
        }
        $status = null;
        $delete = null;
        $image = null;
        if (count($tabeldata) > 0) {
            foreach ($tabeldata as $key) {
                if (File::exists(public_path('images/menu/' . $key->id . '.jpg'))) {
                    $image = '<img src="' . asset('images/menu/' . $key->id . '.jpg') . '">';
                } else {
                    $image = '<img src="' . asset('images/default.jpg') . '">';
                }
                if ($key->deleted_at == null) {
                    $status = '<i class="fas fa-check" title="Available"></i>';
                    $delete = '<a href="' . route("adminmenudelete", ["id" => $key->id]) . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete this menu?\')">Delete</a>';
                } else {
                    $status = '<i class="fas fa-times" title="Not Available"></i>';
                    $delete = '<a href="' . route("adminmenurestore", ["id" => $key->id]) . '" class="btn btn-sm btn-outline-primary" onclick="return confirm(\'Restore this menu?\')">Restore</a>';
                }
                $data .= '<tr>
                <td class="align-middle">
                <div class="media align-items-center">
                <a class="avatar mr-3">
                ' . $image . '
                </a>
                <div class="media-body">
                <span class="name mb-0 text-sm">' . ucwords(strtolower($key->name)) . '</span>
                </div>
                </div>
                </td>
                <td class="align-middle">' . ucfirst($key->description) . '</td>
                <td class="align-middle">' . ucfirst($key->category_name) . '</td>
                <td class="align-middle"><strong>Rp. </strong>' . number_format($key->price, 0, ',', '.') . '</td>
                <td class="align-middle">' . $status . '</td>
                <td class="align-middle">
                <input type="hidden" name="id_menu" value="' . $key->id . '">
                <button type="button" class="btn btn-sm btn-outline-default" type="button" onclick="getMenuData(' . $key->id . ')" data-toggle="modal" data-target="#modalViewMenu">View</button>
                ' . $delete . '
                </td>
                </tr>';
            }
        } else {
            $data .= '<tr>
                <td colspan="6">
                    <h1 class="text-center">Data Menu Kosong</h1>
                </td>
            </tr>';
        }

        return Response($data);
    }

    public function getDataCategory(Request $ajax)
    {
        $data = MenuCategory::where('id', $ajax->id)->get();
        $respond = [
            'id' => $data[0]['id'],
            'nama' => $data[0]['name'],
        ];
        return Response($respond);
    }

    public function paymentssearch(Request $ajax)
    {
        $data = null;
        if ($ajax->ajax()) {
            if ($ajax->key != null || $ajax->key != '') {
                $datapayment = Payment::where('reservation_code', 'LIKE', '%' . $ajax->key . '%')
                    ->orWhere('order_id', 'LIKE', '%' . $ajax->key . '%')
                    ->get();
            } else {
                $datapayment = Payment::get();
            }
            if (count($datapayment) > 0) {
                foreach ($datapayment as $key) {
                    $data .= '<tr>
                        <td>
                            <a class="text-dark"
                                href="' . route('adminreservationdetail', ['id' => $key->reservation_code]) . '">
                                <u>
                                    ' . $key->reservation_code . '
                                </u>
                            </a>
                        </td>
                        <td>
                            <a class="text-dark"
                                href="' . route('paymentstatus', ['id' => $key->order_id]) . '">
                                <u>
                                    ' . $key->order_id . '
                                </u>
                            </a>
                        </td>
                        <td>
                            ' . ucfirst($key->transaction_status) . '
                        </td>
                    </tr>';
                }
            } else {
                $data .= '<tr>
                <td colspan="4">
                    <h1 class="text-center">Data Payments Kosong</h1>
                </td>
                </tr>';
            }
            return Response($data);
        }
    }

    public function reservationssearch(Request $ajax)
    {
        $data = null;
        if ($ajax->ajax()) {
            if ($ajax->key != null || $ajax->key != '') {
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
                    ->where('reservations.reservation_code', 'LIKE', '%' . $ajax->key . '%')
                    ->orWhere('reservations.nama_pemesan', 'LIKE', '%' . $ajax->key . '%')
                    ->get();
            } else {
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
            }
            if (count($datareservasi) > 0) {
                foreach ($datareservasi as $key => $val) {
                    $data .= '<tr>
                    <td class="align-middle">
                        ' . ($key + 1) . '
                    </td>
                    <td class="align-middle">
                        ' . $val->codereservation . '
                    </td>
                    <td class="align-middle">
                    ' . date('d/m/Y H:i', strtotime($val->reservationtime))  . '
                    </td>
                    <td class="align-middle">
                        ' . $val->nomeja . '
                    </td>
                    <td class="align-middle">
                        ' . $val->pemesan . '
                    </td>
                    <td class="text-left align-middle">
                        <ul class="list-group">
                            ' . $this->reservationStatus($val) . '
                        </ul>
                    </td>
                    <td class="align-middle">
                        ' . '<a class="btn btn-sm btn-outline-default"
                        href="' . route("adminreservationdetail", ["id" => $val->codereservation]) . '">Detail</a>' . $this->actions($val) . '
                    </td>
                    </tr>';
                }
            } else {
                $data = '<tr>
                <td colspan="7">
                <h1 class="text-center">Data reservasi kosong</h1>
                </td>
                </tr>';
            }

            return Response($data);
        }
    }

    public function reservationcheck(Request $ajax)
    {
        $payments = Payment::where('reservation_code', $ajax->id)
            ->get();
        $status_sebelum = $payments[0]->status_code;
        $berubah = false;
        $this->checkreservation();
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
            if ($status_sebelum != $content['status_code']) {
                $berubah = true;
            }
        }
        $data = [
            'berubah' => $berubah
        ];
        return Response($data);
    }

    public function checkreservation()
    {
        $payments = Payment::where('status_code', '<>', '200')
            ->get();
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

    public function reservationStatus($data)
    {
        $add = null;
        if ($data->reservasistatus == 0) {
            $add .= '<liclass="list-group-item d-flex justify-content-between align-items-center">Table Reserved. Menu Not Reserved Yet</li>';
        } elseif ($data->reservasistatus == 1) {
            $add .= '<li class="list-group-item d-flex justify-content-between align-items-center">Table and Menu Reserved</li>';
        }
        if ($data->jumlahpembayaran == 0) {
            $add .= '<li class="list-group-item d-flex justify-content-between align-items-center">Payments not initiated</li>';
        } elseif ($data->jumlahpembayaran > 0)
            if ($data->status_code == 200) {
                $add .= ' <li class="list-group-item d-flex justify-content-between align-items-center">Payment succeed</li>';
            } elseif ($data->status_code == 404) {
                $add .= '<li class="list-group-item d-flex justify-content-between align-items-center">Payment process is pending</li>';
            } elseif ($data->status_code == 407) {
                $add .= '<li class="list-group-item d-flex justify-content-between align-items-center">Payment is expired</li>';
            }
        if ((($data->status_code != 200 || $data->jumlahpembayaran == 0)     && date('Y-m-d H:i:s') > date('Y-m-d H:i:s', strtotime($data->reservationtime . '- 2 hours'))) || $data->bookingstatus == 2) {
            $add .= '<li class="list-group-item d-flex justify-content-between align-items-center">Expired reservation</li>';
        } elseif ($data->bookingstatus == 1) {
            $add .= '<li class="list-group-item d-flex justify-content-between align-items-center">Transaction Done</li>';
        }
        return $add;
    }

    public function actions($data)
    {
        $add = null;
        if ($data->status_code == 200 && $data->bookingstatus == 0) {
            $add .= '<a class="btn btn-sm btn-outline-success" onclick="return confirm(\'Tandai reservasi telah selesai?\')" 
            href="' . route("bookedin", ["id" => $data->codereservation]) . '">
            Booked In
            </a>';
        } else if ($data->bookingstatus == 1) {
            $add .= '<a target="__blank" class="btn btn-sm btn-outline-warning" href="' . route("adminprintstruk", ["id" => $data->codereservation]) . '">
            Booked In
            </a>';
        }
        return $add;
    }
}
