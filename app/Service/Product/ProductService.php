<?php

namespace App\Service\Product;

use App\Repositories\Product\ProductInterface;

class ProductService
{
    public $productRepository;

    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function create($data = [])
    {
        return $this->productRepository->create($data);
    }

    public function getProd($params = [])
    {
        return $this->productRepository->getProd($params);
    }
}
