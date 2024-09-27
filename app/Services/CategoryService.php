<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\ResponseTrait;

class CategoryService
{
    use ResponseTrait;

    /**
     * Store a newly created category in storage.
     * @param array $data
     * @return bool[]
     */
    public function createCategory(array $data)
    {
        Category::create([
            'name'              =>      $data['name'],
            'description'       =>      $data['description']
        ]);

        return ['status'        =>      true];
    }

    /**
     * Update the specified category in storage.
     * @param array $data
     * @param \App\Models\Category $category
     * @return array
     */
    public function updateCategory(array $data,  Category $category)
    {
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (empty($filteredData)) {
            return [
                'status'        =>      false,
                'msg'           =>      'Not Found Any Data in Request!',
                'code'          =>      404
            ];
        }

        $category->update($filteredData);
        return ['status'        =>      true];
    }
}
