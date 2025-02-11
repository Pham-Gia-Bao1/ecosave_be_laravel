<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;

class ProductSeeder extends Seeder
{
    public function run()
    {

        Product::factory(30)->create();

    }
}
