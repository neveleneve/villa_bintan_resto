<?php

use App\Helpers\Helper;
use App\Reservation;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Reservation::insert([
            [
                'reservation_code' => 'ZL0UFTFHMK',
                'nama_pemesan' => 'Budiman',
                'kontak' => '082291829938',
                'table_id' => 1,
                'time' => '2021-11-08 14:30:00',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'reservation_code' => 'TLPAXLUZJY',
                'nama_pemesan' => 'Andika',
                'kontak' => '082299382918',
                'table_id' => 2,
                'time' => '2021-11-08 14:30:00',
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
