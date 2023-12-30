<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Service\Category\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends BaseController
{
    public $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getList(Request $request)
    {
        try {
            return $this->sendResponse($this->categoryService->getListNoChild());
        } catch (\Exception $exception) {
            $erros = $exception->getMessage() . " - Line: " . $exception->getLine() . " - File: " . $exception->getFile();
            Log::error($erros);

            return $this->sendError("Have an error. Please try again", 500);
        }
    }

    public function findById(Request $request, $id)
    {
        $category = $this->categoryService->findById($id);

        return $this->sendResponse($category);
    }
}
