<?php

namespace Owlcoder\Taxonomy\Models;

use App\Models\TermBind;
use Exception;

trait Termable
{
    public function terms()
    {
        return $this->morphOne(TermBind::class, 'item');
    }
}
