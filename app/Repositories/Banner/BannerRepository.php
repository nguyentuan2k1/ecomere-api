<?php

namespace App\Repositories\Banner;

use App\Models\Banner;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class BannerRepository implements BannerInterface
{
    public function getList($params = [])
    {
        $banners = Banner::query()->orderBy("order", "ASC");
        $banners = $banners->orderBy("id", "DESC");
        $banners = $banners->where("active", config("generate.active"));

        if (!empty($params['is_home'])) $banners->where("is_home", $params['is_home']);

        if (!empty($params['per_page'])
            || !empty($params['page'])
        ) {
//            if (!empty($params['page'])
//                && is_integer($params['page'])
//                && $params['page'] > 0
//            ) {
//                $page = $params['page'];
//            } else {
//                $page = config("generate.page_default");
//            }
//
//            if (!empty($params['per_page'])
//                && is_integer($params['per_page'])
//                && $params['per_page'] > 0
//            ) {
//                $perPage = $params['per_page'];
//            } else {
//                $perPage = config("generate.page_default");
//            }
//
//            $banners = $banners->paginate($perPage);
        } else {
            $banners = $banners->get();
        }

        return $banners;
    }

    public function create($data)
    {
        // TODO: Implement create() method.
    }
}
