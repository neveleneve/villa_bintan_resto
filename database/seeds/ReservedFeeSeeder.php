<?php

use App\ReservedFee;
use Illuminate\Database\Seeder;

class ReservedFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReservedFee::insert([
            [
                'reservation_code' => 'ZL0UFTFHMK',
                'fee' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
