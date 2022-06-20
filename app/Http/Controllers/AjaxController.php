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
                . '</td><td><div class="form-check"><label class=form-check-label for=meja' . $key->id . '><input class=form-check-input type=radio name=meja id=meja' . $key->id . ' value=' . $key->id . ' required>&nbsp;Pilih Meja</label></div></td></tr>';
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
                <td class="align-middle">' . number_format($key->price, 0, ',', '.') . ' <strong>IDR</strong></td>
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
        # code...
    }
}
