<?php

namespace Omatech\Hexagon\Application\OutputAdapter;

use Illuminate\Http\JsonResponse;

final class GenerateOutputAdapterOutputAdapter extends JsonResponse
{
    public static function ofSuccess(): GenerateOutputAdapterOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Output adapter created Successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateOutputAdapterOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
