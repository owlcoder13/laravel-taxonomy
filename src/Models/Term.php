<?php

namespace Owlcoder\Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  Class \App\Models\Term*/
class Term extends Model
{
    use SoftDeletes;

    protected $table = 'term';

    public $morphClass = 'Term';

    public function taxonomies()
    {
        return $this->hasManyThrough(Taxonomy::class, TermTaxonomy::class, 'term_id', 'id', 'id', 'taxonomy_id');
    }

    public function taxonomy()
    {
        return $this->hasOneThrough(Taxonomy::class, TermTaxonomy::class, 'term_id', 'id', 'id', 'taxonomy_id');
    }

    public function termTaxonomy()
    {
        return $this->hasMany(TermTaxonomy::class, 'term_id');
    }

    /**
     * Return children in context of taxonomy
     */
    public function getChildren($taxonomy_id)
    {
        $data = Term::leftJoin('term_taxonomy', function ($q) use ($taxonomy_id) {
            $q->on('term.id', '=', 'term_taxonomy.term_id');
        })
            ->where('term_taxonomy.taxonomy_id', $taxonomy_id)
            ->where('term_taxonomy.parent_id', $this->id)
            ->orderBy('position', 'asc')
            ->get();

        return $data;
    }

    /**
     * Return children in context of taxonomy
     */
    public function getChildrenTermTaxonomy($taxonomy_id)
    {
        $data = TermTaxonomy::where('term_taxonomy.taxonomy_id', $taxonomy_id)
            ->where('parent_id', $this->id)
            ->where('term_taxonomy.parent_id', $this->id)
            ->orderBy('position', 'asc');

            exit();
        return $data;
    }

    public static function GetTerms($model)
    {
        return TermBind::where([
            'item_id' => $model->id,
            'item_type' => $model->morphClass
        ])->get();
    }

    public function binds()
    {
        return $this->hasMany(TermBind::class, 'term_id');
    }

    public function bind($item)
    {
        $this->item_id = $item->id;
        $this->item_type = $item->morphClass;
    }

    public function bindTaxonomy($term_id, $taxonomy_id)
    {
        $params = [
            'taxonomy_id' => $taxonomy_id,
            'term_id' => $term_id,
        ];

        $termTaxonomy = TermTaxonomy::where($params)->first();

        if ($termTaxonomy == null) {
            $termTaxonomy = new TermTaxonomy($params);
            $termTaxonomy->save();
        }

        return $termTaxonomy;
    }

    public function getTermTaxonomy($taxonomy_id)
    {
        $termTaxonomy = TermTaxonomy::where('term_id', $this->id)
            ->where('taxonomy_id', $taxonomy_id)->first();

        return $termTaxonomy;
    }
}
