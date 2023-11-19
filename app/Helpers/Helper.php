<?php

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

if (!function_exists("vietnameseToLatin")) {
    function vietnameseToLatin($string) {
        $needtobt = [
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Ă'=>'A', 'Ā'=>'A', 'Ą'=>'A', 'Æ'=>'A', 'Ǽ'=>'A',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'ă'=>'a', 'ā'=>'a', 'ą'=>'a', 'æ'=>'a', 'ǽ'=>'a',
            'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',
            'Ç'=>'C', 'Č'=>'C', 'Ć'=>'C', 'Ĉ'=>'C', 'Ċ'=>'C',
            'ç'=>'c', 'č'=>'c', 'ć'=>'c', 'ĉ'=>'c', 'ċ'=>'c',
            'Đ'=>'Dj', 'Ď'=>'D', 'Đ'=>'D',
            'đ'=>'dj', 'ď'=>'d',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ĕ'=>'E', 'Ē'=>'E', 'Ę'=>'E', 'Ė'=>'E',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'ę'=>'e', 'ė'=>'e',
            'Ĝ'=>'G', 'Ğ'=>'G', 'Ġ'=>'G', 'Ģ'=>'G',
            'ĝ'=>'g', 'ğ'=>'g', 'ġ'=>'g', 'ģ'=>'g',
            'Ĥ'=>'H', 'Ħ'=>'H',
            'ĥ'=>'h', 'ħ'=>'h',
            'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'Ĩ'=>'I', 'Ī'=>'I', 'Ĭ'=>'I', 'Į'=>'I',
            'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'į'=>'i', 'ĩ'=>'i', 'ī'=>'i', 'ĭ'=>'i', 'ı'=>'i',
            'Ĵ'=>'J',
            'ĵ'=>'j',
            'Ķ'=>'K',
            'ķ'=>'k', 'ĸ'=>'k',
            'Ĺ'=>'L', 'Ļ'=>'L', 'Ľ'=>'L', 'Ŀ'=>'L', 'Ł'=>'L',
            'ĺ'=>'l', 'ļ'=>'l', 'ľ'=>'l', 'ŀ'=>'l', 'ł'=>'l',
            'Ñ'=>'N', 'Ń'=>'N', 'Ň'=>'N', 'Ņ'=>'N', 'Ŋ'=>'N',
            'ñ'=>'n', 'ń'=>'n', 'ň'=>'n', 'ņ'=>'n', 'ŋ'=>'n', 'ŉ'=>'n',
            'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ō'=>'O', 'Ŏ'=>'O', 'Ő'=>'O', 'Œ'=>'O',
            'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ō'=>'o', 'ŏ'=>'o', 'ő'=>'o', 'œ'=>'o', 'ð'=>'o',
            'Ŕ'=>'R', 'Ř'=>'R',
            'ŕ'=>'r', 'ř'=>'r', 'ŗ'=>'r',
            'Š'=>'S', 'Ŝ'=>'S', 'Ś'=>'S', 'Ş'=>'S',
            'š'=>'s', 'ŝ'=>'s', 'ś'=>'s', 'ş'=>'s',
            'Ŧ'=>'T', 'Ţ'=>'T', 'Ť'=>'T',
            'ŧ'=>'t', 'ţ'=>'t', 'ť'=>'t',
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ũ'=>'U', 'Ū'=>'U', 'Ŭ'=>'U', 'Ů'=>'U', 'Ű'=>'U', 'Ų'=>'U',
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ũ'=>'u', 'ū'=>'u', 'ŭ'=>'u', 'ů'=>'u', 'ű'=>'u', 'ų'=>'u',
            'Ŵ'=>'W', 'Ẁ'=>'W', 'Ẃ'=>'W', 'Ẅ'=>'W',
            'ŵ'=>'w', 'ẁ'=>'w', 'ẃ'=>'w', 'ẅ'=>'w',
            'Ý'=>'Y', 'Ÿ'=>'Y', 'Ŷ'=>'Y',
            'ý'=>'y', 'ÿ'=>'y', 'ŷ'=>'y',
            'Ž'=>'Z', 'Ź'=>'Z', 'Ż'=>'Z', 'Ž'=>'Z',
            'ž'=>'z', 'ź'=>'z', 'ż'=>'z', 'ž'=>'z',
            '“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '•'=>'-', '…'=>'...', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>' degrees ',
            '¼'=>' 1/4 ', '½'=>' 1/2 ', '¾'=>' 3/4 ', '⅓'=>' 1/3 ', '⅔'=>' 2/3 ', '⅛'=>' 1/8 ', '⅜'=>' 3/8 ', '⅝'=>' 5/8 ', '⅞'=>' 7/8 ',
            '÷'=>' divided by ', '×'=>' times ', '±'=>' plus-minus ', '√'=>' square root ', '∞'=>' infinity ',
            '≈'=>' almost equal to ', '≠'=>' not equal to ', '≡'=>' identical to ', '≤'=>' less than or equal to ', '≥'=>' greater than or equal to ',
            '←'=>' left ', '→'=>' right ', '↑'=>' up ', '↓'=>' down ', '↔'=>' left and right ', '↕'=>' up and down ',
            '℅'=>' care of ', '℮' => ' estimated ',
            'Ω'=>' ohm ',
            '♀'=>' female ', '♂'=>' male ',
            '©'=>' Copyright ', '®'=>' Registered ', '™' =>' Trademark ',
        ];

        $string = strtr($string, $needtobt);
        $string = preg_replace("/[^\x9\xA\xD\x20-\x7F]/u", "", $string);

        return $string;
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
                        'dynamicLinkDomain' => 'examplefiver.page.link',
                        'link' => $fullUrl
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
