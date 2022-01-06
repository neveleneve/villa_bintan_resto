<?php

use App\ReservedMenu;
use Illuminate\Database\Seeder;

class ReservedMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReservedMenu::insert([
            [
                'reservation_code' => 'ZL0UFTFHMK',
                'menu_id' => 1,
                'harga' => 50000,
                'jumlah' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'reservation_code' => 'ZL0UFTFHMK',
                'menu_id' => 10,
                'harga' => 300000,
                'jumlah' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'reservation_code' => 'ZL0UFTFHMK',
                'menu_id' => 3,
                'harga' => 120000,
                'jumlah' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }
}
