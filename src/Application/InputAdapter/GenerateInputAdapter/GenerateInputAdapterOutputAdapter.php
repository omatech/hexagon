<?php

namespace Omatech\Hexagon\Application\InputAdapter\GenerateInputAdapter;

use Illuminate\Http\JsonResponse;

final class GenerateInputAdapterOutputAdapter extends JsonResponse
{
    public static function ofSuccess(): GenerateInputAdapterOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Input Adapter created Successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateInputAdapterOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
