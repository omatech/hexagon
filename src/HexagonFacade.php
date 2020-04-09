<?php

namespace Omatech\Hexagon;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Omatech\Hexagon\Skeleton\SkeletonClass
 */
class HexagonFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hexagon';
    }
}
