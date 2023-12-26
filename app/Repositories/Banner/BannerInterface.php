<?php

namespace App\Repositories\Banner;

interface BannerInterface
{
    public function create($data);

    public function getList($params = []);
}
