@extends('base-admin')

@section('content')
    <h1>Taxonomy view</h1>

    <div class="form-group">
        <a class="btn btn-primary" href="{{ route('admin.taxonomy.create-term', ['id' => $model->id]) }}">Добавить терм</a>
    </div>

    <script>
        var treeData = {!! json_encode($model->toJsTreeArray()) !!}
    </script>

    {{-- <ul>
        @foreach ($model->rootTerms as $item)
            @include('admin.taxonomy.tree-item', ['item' => $item])
        @endforeach
    </ul> --}}

    <div id="tree"></div>
@stop


@section('js')

    @parent

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css" />

    <script>
        $(() => {

            let tree = $('#tree').jstree({
                "core": {
                    "animation": 0,
                    "check_callback": true,
                    "themes": {
                        "stripes": true
                    },
                    'data': treeData,
                },
                "types": {
                    "#": {
                        // "max_children": 1,
                        // "max_depth": 4,
                        "valid_children": ["root"]
                    },
                    "root": {
                        "icon": "/static/3.3.12/assets/images/tree_icon.png",
                        "valid_children": ["default"]
                    },
                    "default": {
                        "valid_children": ["default", "file"]
                    },
                    "file": {
                        "icon": "glyphicon glyphicon-file",
                        "valid_children": []
                    }
                },
                "contextmenu": {
                    items: function($node) {
                        return {
                            "asc": {
                                "label": "Редактировать",
                                "icon": "fa fa-pen",
                                "action": function(obj, onemore) {

                                    window.location.href = $node.data.updateUrl;
                                },
                                "_class": "asc"
                            },
                            // "desc": {
                            //     "label": "<span class='desc'>Descending <i class='fa fa-check dir-selected'></i></span>",
                            //     "icon": "fa fa-sort-amount-desc",
                            //     "action": function(obj) {
                            //         fnChangeSortFieldsDirection('desc')
                            //     },
                            //     "_class": "desc"
                            // }
                        }
                    },
                },
                "plugins": [
                    "contextmenu", "dnd", "search",
                    "state", "types", "wholerow"
                ]
            }).bind("loaded.jstree", function(event, data) {
                $(this).jstree("open_all");
            })

            $(document).on('dnd_stop.vakata', function(e, data) {
                var treeInstance = $(tree).jstree(true);
                let $node = treeInstance.get_node(data.element);

                let parent = treeInstance.get_node($node.parent);
                let position = $.inArray($node.id, parent.children)

                $.get('/admin/taxonomy/' + {{ $model->id }} + '/move/', {
                    id: $node.id,
                    parent: $node.parent,
                    index: position,
                }, () => {

                });
            });
        })
    </script>

@endsection
