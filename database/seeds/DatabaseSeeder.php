<?php

use App\Reservation;
use App\ReservedMenu;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TableSeeder::class);
        $this->call(MenuCategorySeeder::class);
        // $this->call(ReservationSeeder::class);
        // $this->call(ReservedMenuSeeder::class);
        // $this->call(ReservedFeeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(TableSeeder::class);
    }
}
