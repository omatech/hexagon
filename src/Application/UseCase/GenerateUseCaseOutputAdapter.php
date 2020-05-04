<?php

namespace Omatech\Hexagon\Application\UseCase;

use Illuminate\Http\JsonResponse;

final class GenerateUseCaseOutputAdapter extends JsonResponse
{
    public static function ofSuccess(): GenerateUseCaseOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Use Case created Successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateUseCaseOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
