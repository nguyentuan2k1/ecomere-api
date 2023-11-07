<?php

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
