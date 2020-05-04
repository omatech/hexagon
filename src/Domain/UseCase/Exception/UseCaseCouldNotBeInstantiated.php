<?php

namespace Omatech\Hexagon\Domain\UseCase\Exception;

class UseCaseCouldNotBeInstantiated extends \Exception
{
    public static function fromException(\Exception $e): UseCaseCouldNotBeInstantiated
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
