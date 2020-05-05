<?php

namespace Omatech\Hexagon\Application\UseCase\Exception;

class UseCaseAlreadyExistsException extends \Exception
{
    public static function fromException(\Exception $e): UseCaseAlreadyExistsException
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
