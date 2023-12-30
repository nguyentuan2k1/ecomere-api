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

    /**
     * Get List No child and item first have all child
     * @return mixed
     */
    public function getListNoChild()
    {
        $data = $this->categoryRepository->getListNoChild();

        if (!empty($data)) $data[0] = $this->findById($data[0]->id);

        return $data;
    }

    /**
     * get category by category id
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->categoryRepository->findById($id);
    }
}
