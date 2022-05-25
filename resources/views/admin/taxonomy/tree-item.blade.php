<li>
    #{{ $item->id }} {{ $item->name }}

    <a href="{{ route('admin.taxonomy.update-term', ['id' => $model->id, 'term_id' => $item->id]) }}">
        update
    </a>

    <ul>
        @foreach ($item->getChildren($model->id) as $item)
            @include('admin.taxonomy.tree-item', ['item' => $item])
        @endforeach
    </ul>
</li>
