<?php

namespace Owlcoder\Taxonomy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  Class \App\Models\Taxonomy*/
class Taxonomy extends Model
{
    protected $table = 'taxonomy';

    use SoftDeletes;

    public function terms()
    {
        return $this->hasManyThrough(Term::class, TermTaxonomy::class, 'taxonomy_id', 'id', 'id', 'term_id');
    }

    public function rootTerms()
    {
        return $this->hasManyThrough(Term::class, TermTaxonomy::class, 'taxonomy_id', 'id', 'id', 'term_id')
            ->whereNull('parent_id')
            ->orderBy('term_taxonomy.position');
    }

    public function _jsTreeSerialize(Term $item)
    {
        $out = [
            'id' => $item->id,
            'children' => array_map([$this, '_jsTreeSerialize'], iterator_to_array($item->getChildren($this->id))),
            'text' => $item->name,
            'expad' => true,
            'data' => [
                'updateUrl' => route('admin.taxonomy.update-term', ['id' => $this->id, 'term_id' => $item->id]),
            ],
        ];

        return $out;
    }

    public function toJsTreeArray()
    {
        $out = [];

        foreach ($this->rootTerms as $rootTerm) {
            $out[] = $this->_jsTreeSerialize($rootTerm);
        }

        return $out;
    }

    /**
     * @return TermTaxonomy[]
     */
    public function getRootNodes()
    {
        return TermTaxonomy::whereNull('parent_id')->where('taxonomy_id', $this->id)->get();
    }

    public function handleRecursiveTermTaxonomies($termTaxonomies, $func)
    {
        $out = [];

        foreach ($termTaxonomies as $one) {
            $cTermTaxonomies = $one->term->getChildrenTermTaxonomy($one->taxonomy_id);
            $children = $this->handleRecursiveTermTaxonomies($cTermTaxonomies, $func);
            $item = $func($one, $children);

            $out[] = $item;
        }

        return $out;
    }

    public function getBindTree($func)
    {
        $rootNodes = $this->getRootNodes();
        return $this->handleRecursiveTermTaxonomies($rootNodes, $func);
    }
}
