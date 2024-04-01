<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            ['name' => 'Adidas', 'image' => null],
            ['name' => 'Nike', 'image' => null],
            ['name' => 'Gucci', 'image' => null],
            ['name' => 'Blend', 'image' => null],
            ['name' => 'Boutique Moschino', 'image' => null],
            ['name' => 'Champion', 'image' => null],
            ['name' => 'Jack & Jones', 'image' => null],
            ['name' => 'Naf Naf', 'image' => null],
            ['name' => 'Puma', 'image' => null],
            ['name' => 'Under Armour', 'image' => null],
            ['name' => 'Vans', 'image' => null],
            ['name' => 'Zara', 'image' => null],
            ['name' => 'H&M', 'image' => null],
            ['name' => 'Lacoste', 'image' => null],
            ['name' => 'Hugo Boss', 'image' => null],
            ['name' => 'Calvin Klein', 'image' => null],
            ['name' => 'Tommy Hilfiger', 'image' => null],
            ['name' => 'Louis Vuitton', 'image' => null],
            ['name' => 'Prada', 'image' => null],
            ['name' => 'Dolce & Gabbana', 'image' => null],
            ['name' => 'Versace', 'image' => null],
            ['name' => 'Gucci', 'image' => null],
            ['name' => 'Balenciaga', 'image' => null],
            ['name' => 'Armani', 'image' => null],
            ['name' => 'Burberry', 'image' => null],
            ['name' => 'Fendi', 'image' => null],
        ];

        DB::table('brands')->insert($brands);
    }
}
