<?php

return [
    "max_length"            => 255,
    "fire_base_web_api_key" => env("FIRE_BASE_WEB_API_KEY", ""),

    "user" => [
        "password" => [
            "min" => 6,
            "max" => 20,
        ],

        "full_name" => [
            "min" => 2,
        ],

      "avatar" => [
          "default" => "default_avatar.png",
      ],
    ],

    "file_storage_directory" => [
        "avatar" => "public/avatar",
    ],

    "social_accepts" => [
        "google" => "google"
    ],

    "reset_token_time" => env("RESET_TOKEN_TIME", 3600),
];
