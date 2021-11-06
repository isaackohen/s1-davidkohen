<div class="form-group col-md-2 mb-1 px-1">
    <label class="mb-1">Model</label>
    <a class="add-relationship" target="_blank" href="{{ route('devtools.model.create') }}" wire:click="creatingModelColumn({{$column_index}})">+ Add</a>

    <select name="columns[{{ $column_index }}][args][model]"
        wire:model="columns.{{ $column_index }}.args.model"
        wire:change="fetchModelInfo({{$column_index}})"
        class="form-control">
        <option value="">-</option>
        @foreach ($models as $model)
        <option value="{{$model['name']}}">{{ $model['name'] }}</option>
        @endforeach
    </select>
    @php
        $model = $models->where('name',$columns[$column_index]['args']['model'] ?? '')->first();
    @endphp
    @if($model && !$model['has_index'])
        <small class="text-danger">Model table does not have an index. Can't use BelongsTo column type.</small>
        <input type="hidden" value="true" name="columns[{{ $column_index }}][table_no_index]">
    @endif
</div>

<div class="form-group col-md-2 mb-1 px-1">
    <label class="mb-1">Table</label>
    <select name="columns[{{ $column_index }}][args][foreign_table]"
        wire:model="columns.{{ $column_index }}.args.foreign_table"  
        class="form-control">
        <option value="">-</option>
        @foreach ($table_list as $table)
        <option value="{{$table}}">{{ $table }}</option>
        @endforeach
    </select>
    @if(empty($columns[$column_index]['args']['foreign_table']))
        <small class="text-danger">A table must be selected to create database relation</small>
    @endif

    @php
    $table = $schema->where('name', $columns[$column_index]['args']['foreign_table'] ?? '')->first();
    @endphp
    @if($table && !$table['has_index'])
        <small class="text-danger">Table does not have an index. Can't use foreignId column type.</small>
        <input type="hidden" value="true" name="columns[{{ $column_index }}][table_no_index]">
    @endif
</div>
<div class="form-group col-md-2 mb-1 px-1">
    <label class="mb-1">Column</label>
    <select name="columns[{{ $column_index }}][args][foreign_column]"
        wire:model="columns.{{ $column_index }}.args.foreign_column"  
        class="form-control">
        <option value="">-</option>
        @foreach ($column['columns_for_table'] as $column_name => $column_value)
        <option
            value="{{ $column_name }}"
            {{ $column_value['is_index'] ? '' : 'disabled' }}>
            {{ $column_name }} {{ $column_value['is_index'] ? '' : '(not an index)' }}
        </option>
        @endforeach
    </select>
</div>
