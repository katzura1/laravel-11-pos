<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    protected function handleTransaction(callable $callback, string $errorMessage): JsonResponse
    {
        try {
            DB::beginTransaction();
            $response = $callback();
            DB::commit();
            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'status' => false,
                'message' => $errorMessage
            ], 500);
        }
    }
}
