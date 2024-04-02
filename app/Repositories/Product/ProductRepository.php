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
     * Get product
     * @param array $params
     * @return false|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getProduct($params = [])
    {
        try {
            $query = Product::query()->with(['brand', 'category']);
            $query = $query->join('product_variant', "product_variant.product_id", "products.id" )
                ->where([
                    ["product_variant.active", "Y"],
                ])
                ->whereNull("product_variant.product_parent");

            $query = $query->select([
                "products.*",
                "product_variant.id as variant_id",
                "product_variant.price as variant_price",
                "product_variant.sale_price as variant_sale_price",
                "product_variant.image as variant_image",
            ]);

            if (!empty($params['brand'])) $query = $query->whereIn("brand_id", [$params['brand']]);

            if (!empty($params['sort'])) {
                switch ($params['sort']) {
                    case 1:
                        // popular đang theo mặc định
                        $query = $query->orderBy("id", "DESC");
                        break;
                    case 2:
                        $query = $query->orderBy("id", "DESC");
                        break;
                    case 3:
                        $query = $query->orderBy("products.rating", "DESC");
                        break;
                    case 4:
                        $query = $query->orderBy("product_variant.sale_price", "ASC")->orderBy("product_variant.price", "ASC");
                        break;
                    case 5:
                        $query = $query->orderBy("product_variant.sale_price", "DESC")->orderBy("product_variant.price", "DESC");
                        break;
                }
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
