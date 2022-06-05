<?php

namespace Owlcoder\Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  Class \App\Models\TermTaxonomy*/
class TermTaxonomy extends Model
{
    protected $table = 'term_taxonomy';

    protected $fillable = [
        'term_id',
        'taxonomy_id',
        'parent_id',
        'position',
    ];

    public function term()
    {
        return $this->hasOne(Term::class, 'term_id');
    }

    public function parent()
    {
        return $this->hasOne(Term::class, 'parent_id');
    }

    public function getChildren()
    {
        return $this->term->getChildren($this->taxonomy_id);
    }
}
