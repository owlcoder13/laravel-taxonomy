<?php

namespace Owlcoder\Taxonomy\Controllers\Admin;

use Owlcoder\Sengo\Controllers\CrudController;
use Owlcoder\Taxonomy\Forms\TaxonomyForm;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\TermTaxonomy;
use Illuminate\Http\Request;
use Owlcoder\Common\Helpers\Html;
use Owlcoder\Taxonomy\Forms\TermForm;
use PDO;
use Route;

class TaxonomyController extends CrudController
{
    public $modelClass = Taxonomy::class;
    public $formClass = TaxonomyForm::class;
    public $baseRoute = 'admin.taxonomy';

    public function getColumns()
    {
        $this->columns = [
            'id',
            'name',
            'desc',
        ];

        return parent::getColumns();
    }

    public static function registerRoutes()
    {
        parent::registerRoutes();

        Route::any('/admin/taxonomy/{id}/view', [static::class, 'view'])->name('admin.taxonomy.view');
        Route::any('/admin/taxonomy/{id}/create-term', [static::class, 'createTerm'])->name('admin.taxonomy.create-term');
        Route::any('/admin/taxonomy/{id}/update-term/{term_id}', [static::class, 'updateTerm'])->name('admin.taxonomy.update-term');
        Route::any('/admin/taxonomy/{id}/delete-term/{term_id}', [static::class, 'deleteTerm'])->name('admin.taxonomy.delete-term');
        Route::any('/taxonomy/{taxonomy_id}/move', [static::class, 'move'])->name('admin.taxonomy.move');
    }

    public function getListButtons()
    {
        $buttons = parent::getListButtons();

        array_unshift(
            $buttons,
            function ($item) {
                return Html::link('view', route('admin.taxonomy.view', ['id' => $item->id]));
            },
        );

        return $buttons;
    }

    public function view(Request $request)
    {
        $model = \App\Models\Taxonomy::find($request->route('id'));
        return view('admin.taxonomy.view', ['model' => $model]);
    }

    public function createTerm(Request $request)
    {
        return $this->saveTerm($request);
    }

    public function updateTerm(Request $request)
    {
        return $this->saveTerm($request, $request->route('term_id'));
    }

    public function saveTerm(Request $request, $id = null)
    {
        // fetch taxonomy
        $taxonomy  = Taxonomy::find($request->route('id'));

        if ($id) {
            $instance = Term::find($id);
        } else {
            $instance = new Term();
        }

        $form = new TermForm([
            'taxonomy_id' => $taxonomy->id,
        ], $instance);

        if ($request->method() == 'POST') {
            \DB::beginTransaction();

            $form->load($request->post());

            if ($form->validate()) {

                $form->save();

                \DB::commit();

                return redirect()->route('admin.taxonomy.view', ['id' => $taxonomy->id]);
            }
        }

        $view = $id == null ? 'admin.taxonomy.create-term' : 'admin.taxonomy.update-term';
        return view($view, ['form' => $form]);
    }

    public function move(Request $request)
    {
        $taxonomy_id = $request->route('taxonomy_id');
        $id = $request->get('id');

        $parent = $request->get('parent');
        $parent = $parent == '#' ? null : $parent;

        $index = $request->get('index');

        /** @var Taxonomy */
        $taxonomy = Taxonomy::find($taxonomy_id);

        /** @var Term */
        $term = Term::find($id);

        $binding = $term->getTermTaxonomy($taxonomy->id);

        // position for other element in the node
        $nextSort = $index + 1;
        foreach (TermTaxonomy::where('position', $index)
            ->where('parent_id', $parent)
            ->where('id', '!=', $term->id)->get() as $sortMore) {
            $sortMore->position = $nextSort++;
            $sortMore->save();
        }

        // set current element position
        $binding->parent_id = $parent;
        $binding->position = $index;
        $binding->save();

        return response()->json(['success' => true]);
    }
}
