<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Term;

class TermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create default terms
        Term::create([
            'name'      => 'Uncategorized',
            'slug'      => 'uncategorized',
            'taxonomy'  => 'category',
        ]);
        Term::create([
            'name'      => 'Blog',
            'slug'      => 'blog',
            'taxonomy'  => 'category',
        ]);

        Term::create([
            'name'      => 'story',
            'slug'      => 'story',
            'taxonomy'  => 'tag',
        ]);
        Term::create([
            'name'      => 'Velocity Developer',
            'slug'      => 'velocity-developer',
            'taxonomy'  => 'tag',
        ]);
    }
}
