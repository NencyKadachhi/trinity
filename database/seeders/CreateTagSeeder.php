<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = ['Fashion', 'Beauty', 'Lifestyle blogs', 'Technology', 'Food'];
        foreach ($tags as $value) {
            $add = new Tag();
            $add->tag_name = $value;
            $add->save();
        }
    }
}
