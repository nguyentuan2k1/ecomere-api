<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($result, $message = '')
    {
        $response = [
            'success' => true,
            'data'    => $result,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, 200);
    }

    public function sendError($message, $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public function sendValidator($validator)
    {
        $response = [
            'success'   => false,
            'validator' => $validator,
        ];

        return response()->json($response, 422);
    }

    public function sendPaginationResponse($result, $message = '')
    {
        $result     = collect($result);
        $data       = $result->get('data');
        $pagination = $result->forget('data');
        $pagination = $pagination->forget('links');

        $response = [
            'success'    => true,
            'data'       => $data,
            'pagination' => $pagination,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, 200);
    }
}
