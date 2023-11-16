<?php

namespace App\Service\UploadFile;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadFileService
{
    /**
     * Upload File
     * @param $file
     * @param $file_name
     * @param string $directory
     * @param string $permission
     * @return false|string
     */
    public function uploadFile($file, $file_name, $directory = "uploads", $permission = "public")
    {
        try {
            if (!is_file($file)) return false;

            $path = Storage::disk("local")->putFileAs($directory, $file, $file_name, $permission);

            return $path;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }
}
