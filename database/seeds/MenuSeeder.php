<?php

use App\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::insert([
            [
                'id_category' => 1,
                'name' => 'xincent burger',
                'price' => 50000,
                'description' => 'Classic marinara sauce.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 1,
                'name' => 'margherita',
                'price' => 60000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 2,
                'name' => 'ammaretti',
                'price' => 120000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 2,
                'name' => 'bostrengo',
                'price' => 75000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 3,
                'name' => 'late vegetale',
                'price' => 150000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 3,
                'name' => 'ice tea',
                'price' => 45000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 4,
                'name' => 'bucatini',
                'price' => 300000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 4,
                'name' => 'cannelloni',
                'price' => 150000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 5,
                'name' => 'diablo',
                'price' => 150000,
                'description' => 'Classic marinara sauce, authentic old-world pepperoni.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 1,
                'name' => 'tajine',
                'price' => 300000,
                'description' => 'Moroccan Tagine in China.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 1,
                'name' => 'bissara',
                'price' => 150000,
                'description' => 'BISSARA B ZIT LAOUD.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 4,
                'name' => 'couscous',
                'price' => 1500000,
                'description' => 'COUSCOUS BIL ADASS.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 3,
                'name' => 'ice chocolate',
                'price' => 150000,
                'description' => 'Melted Chocolate Mixed with ice.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 3,
                'name' => 'ice chocolate fruit mix',
                'price' => 150000,
                'description' => 'Melted Chocolate Mixed with fruit flavour.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
            [
                'id_category' => 5,
                'name' => 'the detroiter',
                'price' => 300000,
                'description' => 'Pizza with the similar taste of detroit.',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null
            ],
        ]);
    }
}
