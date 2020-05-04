<?php

namespace Omatech\Hexagon\Domain\UseCase\Exception;

class UseCaseAlreadyExistsException extends \Exception
{
    public static function fromException(\Exception $e): UseCaseAlreadyExistsException
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
