<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists("getUrlStorage")) {
    function getUrlStorageFile($file) {
        switch (env("FILESYSTEM_DISK")) {
            case "s3" :
                $url = null;
                break;

            default :
                $url = asset(\Illuminate\Support\Facades\Storage::url($file));
                break;
        }

        return $url;
    }
}

if (!function_exists("VerifyGoogleToken")) {
    function VerifyGoogleToken($token) {
        $url = "https://www.googleapis.com/oauth2/v3/userinfo";

        $response = Http::get($url, [
            'access_token' => $token,
        ]);

        if ($response->status() != 200) return false;

        return json_decode($response->body());
    }
}

if (!function_exists("makeShortUrl")) {
    function makeShortUrl($fullUrl)
    {
        try {
            $client     = New GuzzleHttp\Client();
            $apiShorter = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=' . config('generate.fire_base_web_api_key');

            $data = $client->post($apiShorter, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'dynamicLinkInfo' => [
                        'dynamicLinkDomain' => env("DOMAIN_URI_PREFIX"),
                        'link'              => $fullUrl,

                        'androidInfo' => [
                            "androidPackageName" => config("generate.mobile_app_id"),
                        ],
                        'iosInfo' => [
                            "iosBundleId" => config("generate.mobile_app_id"),
                        ],
                    ],
                    'suffix' => [
                        'option' => 'SHORT'
                    ]
                ]
            ]);

            $data = $data->getBody()->getContents();

            return json_decode($data)->shortLink;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }
}

if (!function_exists("getFileInStorage")) {
    function getFileInStorage($file) {
        if (filter_var($file, FILTER_VALIDATE_URL)) return $file;

        return asset($file);
    }
}

if (!function_exists("timestampToDateApi")) {
    function timestampToDateApi($value, $format = null, $timezone = null) {
        try {
            if (empty($value)) return false;

            if (empty($timezone)) $timezone = config("generate.timezone_vietnam");

            if (empty($format)) $format = config("generate.default_format_date");

            return Carbon::createFromTimestamp($value, $timezone)->format($format);
        } catch (Exception $exception) {
            Log::error("Helper TimestampToDateApi : {$exception->getMessage()} - {$exception->getFile()} - {$exception->getLine()}");

            return false;
        }
    }
}
