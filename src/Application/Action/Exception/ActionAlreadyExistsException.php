<?php

namespace Omatech\Hexagon\Application\Action\Exception;

class ActionAlreadyExistsException extends \Exception
{
    public static function fromException(\Exception $e): ActionAlreadyExistsException
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
