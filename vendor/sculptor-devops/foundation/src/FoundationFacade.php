<?php

namespace Sculptor\Foundation;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sculptor\Fooundation\Skeleton\SkeletonClass
 */
class FoundationFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fooundation';
    }
}
