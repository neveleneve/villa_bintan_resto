<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuCategory;
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
        $data = null;
        if ($ajax->ajax()) {
            $datamenu = Menu::withTrashed()->where('id', $ajax->id)->get();
            foreach ($datamenu as $key) {
                $data = [
                    'id' => $key->id,
                    'name' => ucwords($key->name),
                    'desc' => $key->description,
                    'cat' => $key->id_category,
                    'price' => $key->price
                ];
            }
            // cek jika gambar ada di images/menu
            if (is_file(public_path('images/menu/' . $data['id'] . '.jpg'))) {
                $data = [
                    'src' => URL::to('/images/menu/' . $data['id'] . '.jpg')
                ];
            } else {
                $data = [
                    'src' =>  URL::to('/images/default.jpg')
                ];
            }
            return Response($data);
        }
    }

    public function search(Request $ajax)
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
        foreach ($tabeldata as $key) {
            if (File::exists(public_path('images/menu/' . $key->id . '.jpg'))) {
                $image = '<img src="' . asset('images/menu/' . $key->id . '.jpg') . '">';
            } else {
                $image = '<img src="' . asset('images/menu/default.jpg') . '">';
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
}
