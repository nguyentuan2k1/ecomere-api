<?php

namespace App\Service\Banner;

use App\Repositories\Banner\BannerInterface;

class BannerService
{
    public $bannerRepository;

    public function __construct(BannerInterface $bannerRepository)
    {
        $this->bannerRepository = $bannerRepository;
    }

    public function getList($params = [])
    {
        return $this->bannerRepository->getList($params);
    }
}
