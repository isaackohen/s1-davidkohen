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
    @if(empty($columns[$column_index]['args']['model']))
        <small class="text-danger">A model must be selected</small>
    @endif
    
    @php
        $model = $models->where('name',$columns[$column_index]['args']['model'] ?? '')->first();
    @endphp
    @if($model && !$model['has_index'])
        <small class="text-danger">Model table does not have an index. Can't use BelongsTo column type.</small>
        <input type="hidden" value="true" name="columns[{{ $column_index }}][table_no_index]">
    @endif
</div>
