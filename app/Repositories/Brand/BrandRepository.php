<?php

namespace App\Repositories\Brand;

use App\Models\Brand;
use Illuminate\Support\Facades\Log;

class BrandRepository implements BrandInterface
{
    public function getBrand($params = [])
    {
        $brand = Brand::query()->select([
            "id",
            "name",
        ]);

        if (!empty($params['search'])) $brand = $brand->where("name", "LIKE", "%{$params['search']}%");

        $brand = $brand->orderBy("id", "DESC");

        if (!empty($params['per_page'])) return $brand->paginate($params['per_page']);

        return $brand->get();
    }
}
