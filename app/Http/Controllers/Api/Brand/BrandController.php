<?php

namespace App\Http\Controllers\Api\Brand;

use App\Http\Controllers\BaseController;
use App\Service\Brand\BrandService;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function getBrand(Request $request)
    {
        $allBrand = $this->brandService->getBrand($request->all());

        if (!empty($request->get("per_page"))) return $this->sendPaginationResponse($allBrand);

        return $this->sendResponse($allBrand);
    }
}
