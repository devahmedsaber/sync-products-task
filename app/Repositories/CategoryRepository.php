<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function findOrCreate(string $name): Category
    {
        return Category::firstOrCreate(['name' => $name]);
    }
}
