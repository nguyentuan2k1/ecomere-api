<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryRepository implements CategoryInterface
{
    /**
     * Create
     * @param array $data
     * @return Category|false
     */
    public function create($data)
    {
        try {
            $category = new Category();

            foreach ($data as $field => $value) {
                $category->$field = $value;
            }

            $category->save();

            return $category;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    /**
     * Find by name
     * @param string $category_name
     * @return mixed
     */
    public function findByName($category_name)
    {
        return Category::where("name", $category_name)->first();
    }

    /**
     * Get list level 0
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getListNoChild()
    {
        $model = Category::query()->where("parent_category", 0);

        if (!empty($params['active'])) $model = $model->where("active", $params['active']);

        $model = $model->orderBy("order", "DESC");

        $model = $model->get();

        return $model;
    }

    public function findById($id)
    {
        $model = Category::with('parents')->with('childs')->find($id);

        return $model;
    }
}
