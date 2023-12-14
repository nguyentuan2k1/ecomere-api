<?php

namespace App\Repositories\Product;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductRepository implements ProductInterface
{
    /**
     * Create
     * @param array $data
     * @return Product|false
     */
    public function create($data = [])
    {
        try {
            $product = new Product();

            foreach ($data as $field => $value) {
                $product->$field = $value;
            }

            $product->save();

            return $product;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }
}
