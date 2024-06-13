<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Fashion', 'Beauty', 'Lifestyle blogs', 'Technology', 'Food'];
        foreach ($categories as $value) {
            $add = new Category();
            $add->category_name = $value;
            $add->save();
        }
    }
}
