<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Service\Category\CategoryService;
use Illuminate\Http\Request;

class CrawlerController extends Controller
{
    public $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function crawlCategory(Request $request)
    {
        $url  = "https://totoday.vn/";
        $data = file_get_contents($url);
        preg_match_all('/<div class="hc-item">(.*?)<\/div>/s', $data, $matches);

        foreach ($matches[0] as $item) {
           $filterImg  = preg_match('/<img src="(.*?)".*?>/', $item, $matchImg);
           $img        = $matchImg[1];
           $filterName = preg_match('/<span class=.*?>(.*?)<\/span>/', $item, $matchName);
           $name       = $matchName[1];

           $category = $this->categoryService->findByName($name);

           if (!empty($category)) continue;

           $categoryData = [
             "name"  => $name,
             "image" => $img
           ];

           $this->categoryService->create($categoryData);
        }

        return "Category đã xong";
    }
}
