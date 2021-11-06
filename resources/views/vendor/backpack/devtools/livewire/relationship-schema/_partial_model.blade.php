<div class="form-group col-md-4 mb-1 px-1">
    <label class="mb-1">Model</label>
    @if($relationship['created_by_column'])
      <input type="text"
              name="relationships[{{ $relationship_index }}][relationship_model]"
              wire:model="relationships.{{ $relationship_index }}.relationship_model"
              class="form-control"
              readonly>
    @else
      <a class="add-relationship" target="_blank" href="{{ route('devtools.model.create') }}" wire:click="creatingModelRelationship({{$relationship_index}})">+ Add</a>
      <select name="relationships[{{ $relationship_index }}][relationship_model]"
              wire:model="relationships.{{ $relationship_index }}.relationship_model"
              wire:change="modelChanged({{ $relationship_index }})"
              class="form-control">
              @foreach ($models as $model)
              <option value="{{$model['name']}}">{{ $model['name'] }}</option>
              @endforeach
      </select>
    @endif
  </div>