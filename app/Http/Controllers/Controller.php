<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function jsonSuccess(string $message): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'reload' => true]);
    }
}
