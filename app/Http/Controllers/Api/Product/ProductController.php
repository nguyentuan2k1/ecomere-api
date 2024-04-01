<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\BaseController;
use App\Service\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProductController extends BaseController
{
    public $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getProd(Request $request)
    {
        try {
            $products = $this->productService->getProd($request->all());

             $products = collect($products)->map(function ($product) {
                 $createdAt = timestampToDateApi($product->created_at);
                 $updatedAt = timestampToDateApi($product->updated_at);

                 $product->created_at    = $createdAt ?? Carbon::createFromTimestamp($product->created_at, config("generate.timezone_vietnam"))->format(config("generate.default_format_date"));
                 $product->updated_at    = $updatedAt ?? Carbon::createFromTimestamp($product->updated_at, config("generate.timezone_vietnam"))->format(config("generate.default_format_date"));
                 $product->price         = $product->variant_price ?? $product->price;
                 $product->sale_price    = $product->variant_sale_price ?? $product->sale_price;
                 $product->brand_name    = $product['brand']['name'];
                 $product->category_name = $product['category']['name'];
                 $product->sale_percent  = 0;
                 $product->image         = $product->variant_image ?? $product->image;

                 if (!empty($product->sale_price))
                     $product->sale_percent = round( (($product->price - $product->sale_price) / $product->price) * 100, 2);

                 $product = collect($product)->only([
                     "id",
                     "price",
                     "name",
                     "sale_price",
                     "image",
                     "description",
                     "brand_name",
                     "category_name",
                     "rating",
                     "sale_percent",
                 ]);

                 return $product;
             });

            return $this->sendResponse($products);
        } catch (\Exception $exception) {
            Log::error("Api ProductController : {$exception->getMessage()} - {$exception->getFile()} - {$exception->getLine()}");

            return $this->sendError($exception->getMessage());
        }
    }
}
