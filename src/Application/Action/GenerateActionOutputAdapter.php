<?php

namespace Omatech\Hexagon\Application\Action;

use Illuminate\Http\JsonResponse;

final class GenerateActionOutputAdapter extends JsonResponse
{
    public static function ofSuccess(): GenerateActionOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Action created Successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateActionOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
