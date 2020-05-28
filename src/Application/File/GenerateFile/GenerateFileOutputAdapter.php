<?php

namespace Omatech\Hexagon\Application\File\GenerateFile;

use Illuminate\Http\JsonResponse;

final class GenerateFileOutputAdapter extends JsonResponse
{
    public static function ofSuccess(string $class): GenerateFileOutputAdapter
    {
        return self::create([
            'code' => 'success',
            'message' => 'File created Successfully!',
            'class' => $class
        ], 200);
    }

    public static function ofError(string $message = null, string $code = null): GenerateFileOutputAdapter
    {
        return self::create([
            'message' => $message ?? 'Oops, something happened and the File could not be crated',
            'code' => $code ?? null,
        ], 401);
    }
}
