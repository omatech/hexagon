<?php

namespace Omatech\Hexagon\Domain\Base\Exceptions;

class DirectoryDoesNotExistException extends \Exception
{
    public static function fromException(\Exception $e): DirectoryDoesNotExistException
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
