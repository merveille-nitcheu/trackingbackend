<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //

    public function success(mixed $data, string $msg="okay", int $statusCode=200):JsonResponse {
        return response()->json([
            'data' => $data,
            'success' => true,
            'msg' => $msg
        ], $statusCode);
    }

    public function error(string $msg, int $statusCode=200):JsonResponse {
        return response()->json([
            'data' => null,
            'success' => false,
            'msg' => $msg
        ], $statusCode);
    }
}
