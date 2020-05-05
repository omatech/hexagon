<?php

namespace Omatech\Hexagon\Application\Controller\GenerateController;

use Illuminate\Http\JsonResponse;

final class GenerateControllerOutputAdapter extends JsonResponse
{
    public static function ofSuccess(): GenerateControllerOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Controller created Successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateControllerOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
