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

    /**
     * Get prod
     * @param array $params
     * @return bool|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getProd($params = [])
    {
        try {
            $query = Product::query()->with(['brand', 'category']);
            $query = $query->join('product_variant', "product_variant.product_id", "products.id" );
            $query = $query->select([
                "products.*",
                "product_variant.id as variant_id",
                "product_variant.price as variant_price",
                "product_variant.sale_price as variant_sale_price",
                "product_variant.image as variant_image",
            ]);

//            if (!empty($params['brand'])) $query = $query->whereIn()

            if (!empty($params['filter'])) {
                // price : Lower to high
//                $query = $query->orderBy("product_variant.sale_price", "ASC")->orderBy("product_variant.price", "ASC");
                // price : high to lower
//                $query = $query->orderBy("product_variant.sale_price", "DESC")->orderBy("product_variant.price", "DESC");
                // newest
//                $query = $query->orderBy("id", "DESC");
                // customer review
//                $query = $query->orderBy("products.rating", "DESC");
            } else {
                $query = $query->orderBy("id", "DESC");
            }

            if (!empty($params['per_page'])) return $query->paginate($params['per_page']);

            return $query->get();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }
}
