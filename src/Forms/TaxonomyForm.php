<?php

namespace Owlcoder\Taxonomy\Forms;

use App\Models\Taxonomy;
use Owlcoder\Forms\Fields\Field;
use Owlcoder\Forms\Fields\SelectField;
use Owlcoder\Forms\Fields\TextAreaField;
use Owlcoder\Forms\Fields\TextField;
use Owlcoder\Sengo\Forms\BsForm;

class TaxonomyForm extends BsForm
{
    public function getFields()
    {
        return [
            'name',
            'description' => [
                'class' => TextAreaField::class,
            ],
            'slug',
            // 'parent' => [
            //     'class' => SelectField::class,
            //     'options' => ['' => 'Не выбрано'] + Taxonomy::all()->pluck('id', 'name')->toArray(),
            //     'events' => [
            //         Field::EVENT_BEFORE_APPLY => function (&$field) {
            //             if (empty($field->value)) {
            //                 $field->value = 0;
            //             }
            //         }
            //     ],
            // ],
        ];
    }

    // public function save()
    // {
    //     $retVal = parent::save();

    //     $termTaxonomyParams = [
    //         'taxonomy_id' => $this->taxonomy_id,
    //         'term_id' => $this->instance->id,
    //     ];

    //     $this->instance->termTaxonomy()->updateOrCreate($termTaxonomyParams, $termTaxonomyParams);

    //     return $retVal;
    // }
}
