<div class="form-group col-md-4 mb-1 px-1">
    <label class="mb-1">Relation name</label>
    @if($relationship['created_by_column'])
    <input
        type="text"
        name="relationships[{{ $relationship_index }}][relationship_column]"
        wire:model="relationships.{{ $relationship_index }}.relationship_column"
        class="form-control"
        readonly />
    @else
        @if(!$relationship['relationship_column'])
        <button
            class="d-inline-block ml-1 btn btn-sm btn-link"
            href="#"
            wire:click.prevent="addNewRelationshipColumn({{$relationship_index}})">
            + Create Column
        </button>
        @endif

        @if(!empty($current_available_columns_for_morphs) || $relationship['relationship_column'])
        <select
            name="relationships[{{ $relationship_index }}][relationship_column]"
            wire:model="relationships.{{ $relationship_index }}.relationship_column"
            class="form-control">
            @if($relationship['relationship_column'])
            <option value="{{$relationship['relationship_column']}}">{{$relationship['relationship_column']}}</option>
            @endif
            <option value="">-</option>
            @if(!empty($current_available_columns_for_morphs))
            
            @foreach ($current_available_columns_for_morphs as $index => $column)
            <option value="{{ $column['args']['morphable'] }}">{{ $column['args']['morphable'] }}</option>
            @endforeach
            @endif
        </select>
        @else
            <br />
            <span> No valid columns to choose from.</span>

            @if($errors->any() && $errors->getBag('default')->has('relationships.'.$relationship_index.'.relationship_column'))
            <br />
            <span class="text-danger"> * {{ $errors->getBag('default')->first('relationships.'.$relationship_index.'.relationship_column') }} </span>
            @endif
        @endif
    @endif
</div>