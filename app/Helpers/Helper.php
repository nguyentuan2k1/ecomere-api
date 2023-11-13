<?php

use Illuminate\Support\Facades\Http;

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

        return $response->body();
    }
}
