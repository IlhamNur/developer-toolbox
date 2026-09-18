<?php

namespace App\Http\Controllers;

use App\Services\Mvel\MvelExecutionException;
use App\Services\Mvel\MvelExecutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

final class MvelExecutionController
{
    public function execute(Request $request, MvelExecutorService $executor): JsonResponse
    {
        try {
            $validated = $request->validate([
                'expression' => ['required', 'string', 'max:51200'],
                'variables' => ['required', 'array', 'max:100'],
            ]);

            $result = $executor->execute($validated['variables'], $validated['expression']);

            return response()->json($result);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (MvelExecutionException $exception) {
            return response()->json([
                'success' => false,
                'error' => [
                    'type' => $exception->errorType,
                    'message' => $exception->getMessage(),
                ],
            ], $exception->status >= 400 && $exception->status < 600 ? $exception->status : 422);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'error' => [
                    'type' => 'MVEL_EXECUTOR_ERROR',
                    'message' => $exception->getMessage(),
                ],
            ], 502);
        }
    }

    public function health(MvelExecutorService $executor): JsonResponse
    {
        try {
            return response()->json($executor->health());
        } catch (Throwable $exception) {
            return response()->json([
                'status' => 'DOWN',
                'message' => $exception->getMessage(),
            ], 503);
        }
    }
}
