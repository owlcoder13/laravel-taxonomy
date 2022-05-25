<?php

namespace Owlcoder\Taxonomy\Facades;

use Illuminate\Support\Facades\Facade;

class Taxonomy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'taxonomy';
    }
}
