<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Service\Category\CategoryService;
use App\Service\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CrawlerController extends Controller
{
    public $categoryService;
    public $productService;

    public function __construct(CategoryService $categoryService, ProductService $productService)
    {
        $this->categoryService = $categoryService;
        $this->productService  = $productService;
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

    public function crawlProduct(Request $request)
    {
        $url  = "https://totoday.vn/";
        $data = file_get_contents($url);

        preg_match_all('/<div class="product-item">\s*<.*?>\s*<.*?>\s*<img src="(.*?)" .*>/', $data, $images);
        preg_match_all('/<a class="product-name" href="(.*?)">(.*?)<\/a>/', $data, $productNames);
        preg_match_all('/<span class="product-price--current tp_product_price">(.*?)đ<\/span>/', $data, $prices);

        $descriptions = [];

        foreach ($productNames[1] as $link) {
            $dataLink = file_get_contents("https://totoday.vn" . $link);
            $regex    = '/<div .*? id="pills-home" .*?>\s*(.*?)<\/div>/';
            preg_match($regex, $dataLink, $matchContent);

            $pattern        = '/<[^>]*>|style="[^"]*"/';
            $result         = preg_replace($pattern, '<br>', $matchContent);
            $descriptions[] = $result;
        }

        dd($images, $productNames, $prices, $descriptions);
    }

    public function crawlProductView(Request $request)
    {
        return view("crawl.product");
    }

    public function crawlProductHandle(Request $request)
    {
        dd("3");
        try {
            ini_set("max_execution_time", 0);
            $url = $request->get("url");

            if (!filter_var($url, FILTER_VALIDATE_URL)) return back()->with(["error" => "Not a URL"]);

            $regexName                = $request->get("regex-name");
            $regexPrice               = $request->get("regex-price");
            $regexImage               = $request->get("regex-image");
            $regexDesc                = $request->get("regex-desc");
            $regexLinkProduct         = $request->get("regex-link-product");
            $regexLinkProductPosition = $request->get("regex-link-product");
            $category                 = 7;

            $data = file_get_contents($url);

            if (empty($data)) return back()->with(["error" => "Can not get data"]);

            preg_match_all($regexLinkProduct, $data, $matchLinkProduct);

            foreach ($matchLinkProduct[$regexLinkProductPosition] as $link) {
                if (!filter_var($link, FILTER_VALIDATE_URL)) $link = "{$url}$link";

                $dataProd = file_get_contents($link);

                preg_match_all($regexName, $dataProd, $matchNames);
                preg_match_all($regexPrice, $dataProd, $matchPrice);
                preg_match_all($regexImage, $dataProd, $matchImage);
                preg_match_all($regexDesc, $dataProd, $matchDesc);
                dd($matchImage);

                if (!empty($matchNames[1][0])
                    && !empty($matchPrice[1][0])
                    && !empty($matchImage[1][0])
                    && !empty($matchDesc[1][0])
                ) {
                    $arrData = [
                        "name"        => $matchNames[1][0],
                        "price"       => $matchPrice[1][0],
                        "image"       => $matchImage[1][0],
                        "description" => $matchDesc[1][0],
                        "category_id" => 7
                    ];

                    $product = $this->productService->create($arrData);
                }
            }

        } catch (\Exception $exception) {
            $err = $exception->getMessage() . " - Line: " . $exception->getLine() . " - File: " . $exception->getFile();
            Log::error($err);

            return back()->with(["error" => $err]);
        }
    }
}
