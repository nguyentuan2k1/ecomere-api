<?php

namespace App\Repositories\Product;

interface ProductInterface
{
    public function create($data = []);

    public function getProduct($params = []);
}
