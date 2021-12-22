<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'id' => 1,
            'code' => 'cat1',
        ]);

        Category::create([
            'id' => 2,
            'code' => 'cat2',
            'parent_id' => 1,
        ]);

        Category::create([
            'id' => 3,
            'code' => 'cat3',
            'parent_id' => 2,
        ]);

        Category::create([
            'id' => 4,
            'code' => 'cat4',
            'parent_id' => 1,
        ]);

        Category::create([
            'id' => 5,
            'code' => 'cat5',
            'parent_id' => 3,
        ]);

        Category::create([
            'id' => 6,
            'code' => 'cat6',
        ]);


        Category::create([
            'id' => 7,
            'code' => 'cat7',
            'parent_id' => 6,
        ]);
    }
}
