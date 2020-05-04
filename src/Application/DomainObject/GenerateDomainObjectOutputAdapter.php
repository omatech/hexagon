<?php

namespace Omatech\Hexagon\Application\DomainObject;

use Illuminate\Http\JsonResponse;

final class GenerateDomainObjectOutputAdapter extends JsonResponse
{
    public static function ofSuccess(): GenerateDomainObjectOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'Domain Object created successfully!'
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateDomainObjectOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
