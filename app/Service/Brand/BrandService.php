<?php

namespace App\Service\Brand;

use App\Repositories\Brand\BrandInterface;

class BrandService
{
    public $brandRepository;

    public function __construct(BrandInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getBrand($params = [])
    {
        return $this->brandRepository->getBrand($params);
    }
}
