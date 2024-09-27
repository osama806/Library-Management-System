<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use ResponseTrait;

    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the categories.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('books')->get();
        return $this->getResponse('categories', CategoryResource::collection($categories), 200);
    }

    /**
     * Store a newly created category in storage.
     * @param \App\Http\Requests\Categories\StoreCategoryRequest $storeCategoryRequest
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $storeCategoryRequest)
    {
        $validatedData = $storeCategoryRequest->validated();
        $response = $this->categoryService->createCategory($validatedData);
        return $response['status']
            ? $this->getResponse('msg', 'Created category successfully', 201)
            : $this->getResponse('error ', 'There is error in server', 500);
    }

    /**
     * Display the specified category.
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->getResponse('category', new CategoryResource($category), 200);
    }

    /**
     * Update the specified category in storage.
     * @param \App\Http\Requests\Categories\UpdateCategoryRequest $updateCategoryRequest
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $updateCategoryRequest, Category $category)
    {
        $validatedData = $updateCategoryRequest->validated();
        $response = $this->categoryService->updateCategory($validatedData, $category);
        return $response['status']
            ? $this->getResponse('msg', 'Updated category successfully', 200)
            : $this->getResponse('error', $response['msg'], $response['code']);
    }

    /**
     * Remove the specified category from storage.
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        if (!Auth::user()->is_admin) {
            return $this->getResponse('error', "Can't access to this permission", 400);
        }

        $category->delete();
        return $this->getResponse('msg', 'Deleted category successfully', 200);
    }
}
