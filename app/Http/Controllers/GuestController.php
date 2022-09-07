<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Menu;
use App\MenuCategory;
use App\Payment;
use App\Reservation;
use App\ReservedFee;
use App\ReservedMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class GuestController extends Controller
{

    public function menu()
    {
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
        return view('menu', [
            'data' => $data,
            'cat' => $cat,
            'jmlmenu' => $datamenu,
        ]);
        // imagecopymerge
    }
    public function about()
    {
        return view('about');
    }

    # reservation thingy
}
