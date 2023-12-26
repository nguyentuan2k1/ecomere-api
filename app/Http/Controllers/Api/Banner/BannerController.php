<?php

namespace App\Http\Controllers\Api\Banner;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Service\Banner\BannerService;
use Illuminate\Http\Request;

class BannerController extends BaseController
{
    public $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    public function getHomeBanner(Request $request)
    {
        $params   = [];
        $paginate = false;

        if (!empty($request->get("is_home"))) $params['is_home'] = $request->get("is_home");

//        if (!empty($request->get("per_page"))) $params['per_page'] = $request->get("per_page");
//
//        if (!empty($request->get("page"))) $params['page'] = $request->get("page");

        $listBanner = $this->bannerService->getList($params);

        return $this->sendResponse($listBanner);
    }
}
