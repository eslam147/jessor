<?php

namespace Jubaer\Zoom\Facades;
use Illuminate\Support\Facades\Facade;
use Jubaer\Zoom\Zoom as ZoomBase;

/**
 * @see \Jubaer\Zoom\Zoom
 */
class Zoom extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ZoomBase::class;
    }
}
