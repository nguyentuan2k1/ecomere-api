<?php

namespace App\Repositories\Category;

interface CategoryInterface
{
    public function create($data);

    public function findByName($category_name);

    public function getAll();
}
