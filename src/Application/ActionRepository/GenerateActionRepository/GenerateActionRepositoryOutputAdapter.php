<?php

namespace Omatech\Hexagon\Application\ActionRepository\GenerateActionRepository;

use Illuminate\Http\JsonResponse;

final class GenerateActionRepositoryOutputAdapter extends JsonResponse
{
    public static function ofSuccess(): GenerateActionRepositoryOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Action Repository created Successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateActionRepositoryOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
