<?php

namespace Owlcoder\Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  Class \App\Models\TermBind*/
class TermBind extends Model
{
    protected $table = 'term_bind';

    public function term()
    {
        return $this->morphTo(Term::class, 'item_type', 'item_id');
    }
}
