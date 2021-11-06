@include('backpack.devtools::livewire.relationship-schema._partial_model')

<div class="form-group col-md-3 mb-1 px-1">
    <label class="mb-1">Morphable</label>

    <input
        type="text"
        name="relationships[{{ $relationship_index }}][relationship_relation_name]"
        wire:model="relationships.{{ $relationship_index }}.relationship_relation_name"
        class="form-control"
        />
</div>