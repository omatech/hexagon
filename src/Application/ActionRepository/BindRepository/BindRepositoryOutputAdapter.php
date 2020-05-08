<?php

namespace Omatech\Hexagon\Application\ActionRepository\BindRepository;

use Illuminate\Http\JsonResponse;

final class BindRepositoryOutputAdapter extends JSONResponse
{
    public static function ofSuccess(): BindRepositoryOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Use Case created Successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): BindRepositoryOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}