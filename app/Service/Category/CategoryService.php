<?php

namespace App\Service\Category;

use App\Repositories\Category\CategoryInterface;

class CategoryService
{
    public $categoryRepository;

    public function __construct(CategoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Create category
     * @param array $data
     * @return \App\Models\User|false
     */
    public function create($data)
    {
        return $this->categoryRepository->create($data);
    }

    /**
     * Find by name
     * @param $category_name
     * @return mixed
     */
    public function findByName($category_name)
    {
        return $this->categoryRepository->findByName($category_name);
    }
}
