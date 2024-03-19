<?php

return [
    "max_length"            => 255,
    "fire_base_web_api_key" => env("FIRE_BASE_WEB_API_KEY", ""),
    "per_page_default"      => 15,
    "page_default"          => 1,

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
        "image"  => "public/image",
    ],

    "social_accepts" => [
        "google" => "google"
    ],

    "reset_token_time" => env("RESET_TOKEN_TIME", 3600),
    "url_mobile_app"   => env("URL_MOBILE_APP"),
    "mobile_app_id"    => env("MOBILE_APP_ID"),
    "url_crawl_data"   => env("URL_CRAWL_DATA"),

    'active'   => "Y",
    'deactive' => "N",

    "file_type_accept" => [
        "avatar" => ["jpg", "png", "jpeg", "svg"],
        "image" => ["jpg", "png", "jpeg"]
    ]
];
