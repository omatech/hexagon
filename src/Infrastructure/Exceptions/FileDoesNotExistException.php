<?php

namespace Omatech\Hexagon\Infrastructure\Exceptions;

class FileDoesNotExistException extends \Exception
{
    public static function fromException(\Exception $e): FileDoesNotExistException
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}