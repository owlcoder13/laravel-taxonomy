<?php

namespace Owlcoder\Taxonomy\Forms;

use App\Models\Term;
use App\Models\TermBind;
use App\Models\TermTaxonomy;
use Owlcoder\Forms\Fields\Field;
use Owlcoder\Forms\Fields\SelectField;
use Owlcoder\Sengo\Forms\BsForm;

class TermForm extends BsForm
{
    /**
     * Таксономия с которой работаем в рамках контекста
     */
    public $taxonomy_id;

    public function configToProps($config)
    {
        $this->taxonomy_id = $config['taxonomy_id'];
    }

    // /**
    //  * Получение бинда терма к родителю
    //  */
    // public function getBindInstance($selected_id = null)
    // {
    //     $bindParams = [
    //         'term_id' => $this->instance->id,
    //         'item_type' => 'Term',
    //     ];

    //     if ($selected_id) {
    //         $bindParams['item_id'] = $selected_id;
    //     }

    //     $bindInstance = TermBind::find($bindParams)->first();

    //     if ($bindInstance == null) {
    //         $bindInstance = new TermBind($bindParams);
    //     }

    //     return $bindInstance;
    // }

    public function getFields()
    {
        return [
            'name',
            'slug',
            'parent_id' => [
                'class' => SelectField::class,
                'canApply' => false,
                'canFetch' => false,
                'options' => function () {
                    $parentTerms = Term::whereHas('termTaxonomy', function ($q) {
                        $q->where('taxonomy_id', $this->taxonomy_id);
                    })->pluck('name', 'id')->toArray();

                    return ['' => ''] + $parentTerms;
                },
                'events' => [
                    Field::EVENT_AFTER_SAVE => function ($field) {
                        /** @var Term */

                        $isNew = false;

                        $model = $field->instance;

                        $termTaxonomy = null;

                        if ($model->id && $this->taxonomy_id) {
                            $termTaxonomy = $model->getTermTaxonomy($this->taxonomy_id);
                        }

                        if ($termTaxonomy == null) {
                            $termTaxonomy = new TermTaxonomy();
                            $termTaxonomy->term_id = $model->id;
                            $termTaxonomy->taxonomy_id = $this->taxonomy_id;

                            $isNew = true;
                        }

                        // set parent id for this term in context of taxonomy
                        $termTaxonomy->parent_id = $field->value;

                        if ($isNew) {
                            $lastPosition = \DB::table('term_taxonomy')->where('parent_id', $termTaxonomy->parent_id)->value('position');
                            $lastPosition = $lastPosition ? $lastPosition + 1 : 1;
                            $termTaxonomy->position = $lastPosition;
                        }

                        $termTaxonomy->save();
                    },
                    Field::EVENT_FETCH_VALUE => function ($field) {
                        /** @var Term */
                        $model = $field->instance;

                        if ($model->id && $this->taxonomy_id) {
                            $termTaxonomy = $model->getTermTaxonomy($this->taxonomy_id);
                            $field->value = $termTaxonomy->parent_id;
                        }
                    }
                ],
            ],
        ];
    }
}
