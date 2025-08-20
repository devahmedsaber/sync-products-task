<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findByExternalId(int $externalId): ?Product
    {
        return Product::where('external_id', $externalId)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }
}
