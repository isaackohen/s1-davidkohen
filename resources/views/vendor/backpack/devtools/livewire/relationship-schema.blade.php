<relationship-schema>

    <label class="d-block">{{ $field['label'] ?? 'Relationships' }}</label>

    @foreach($relationships as $relationship_index => $relationship)
      <div wire:key="relationship-definition-{{ $relationship_index }}" class="relationship-schemas-container">
        <div class="col-md-12 well migration-schema row m-1 p-2 pl-3">
          <div class="side-button-group">
            <button type="button"
                    @if($relationship['created_by_column'])
                    wire:click="confirmRelationshipDelete('removeRelationshipAndColumn', {{$relationship_index}})"
                    @else
                    wire:click="confirmRelationshipDelete('removeRelationship', {{$relationship_index}})"
                    @endif
                    class="close delete-element ">
                    <span aria-hidden="true">×</span>
            </button>
          </div>

          <div class="form-group col-md-2 mb-1 px-1">
            <label class="mb-1">Relation Type</label>
            <select name="relationships[{{ $relationship_index }}][relationship_type]"
                    wire:model="relationships.{{ $relationship_index }}.relationship_type"
                    wire:change="selectRelationshipType({{$relationship_index}})"
                    class="form-control"
                    @if($relationship['created_by_column'])
                    readonly
                    @endif
                    >
                @foreach ($relation_types as $relation => $configs)
                  <option value="{{$relation}}">{{ $relation }}</option>
                @endforeach
            </select>
          </div>

          

          {{-- Include any relation special fields --}}
          @if (view()->exists('backpack.devtools::livewire.relationship-schema.'.strtolower($relationship['relationship_type'])))
              @include('backpack.devtools::livewire.relationship-schema.'.strtolower($relationship['relationship_type']))
          @endif
          @if($relationship['created_by_column'])
          <input type="hidden" name="relationships[{{$relationship_index}}][created_by_column]" wire:model="relationships.{{$relationship_index}}.created_by_column" />
          <div class="col-md-12 form-group mb-1 px-1">
            <span class="text-muted">* This relationship was inferred from your migration columns.</span>
          </div>
          @endif
        </div>

      </div>

    @endforeach


  <button type="button" wire:click.prevent="addRelationship" class="btn btn-outline-primary btn-sm ml-1 add-relationship-schema-button">+ Add Relationship</button>

</relationship-schema>


@push('crud_fields_styles')
  <!-- no styles -->
  <style type="text/css">
    .relationship-schema {
      border: 1px solid #0028641f;
      background-color: #f0f3f94f;
      border-radius: 3px;
    }

    .relationship-schemas-container .side-button-group {
      position: absolute !important;
      z-index: 2;
      width: 1.5rem;
      top: 0;
      left: 0;
      transform: translateX(-50%);
    }

    .relationship-schemas-container .side-button-group button {
      width: 100%;
      border-radius: 50%;
      text-align: center;
      background-color: #e8ebf0 !important;
      margin-bottom: .3rem;
    }

    .relationship-schemas-container .side-button-group button:focus {
      outline: none;
    }

    .relationship-schemas-container input[type="checkbox"] {
      max-width: 1rem;
      margin: auto;
    }
  </style>
@endpush

@push('after_scripts')
  <script>
    window.addEventListener('confirmRelationshipDelete', (e) => {
      let message = 'Are you sure you want to delete this relationship?';
      if (e.detail.callback == 'removeRelationshipAndColumn') {
        message = 'This relationship has an associated column. Are you sure you want to delete both the relationship and the column?';
      }
      if (!confirm(message)) { return }
      @this[e.detail.callback](...e.detail.argv)
    });
  </script>
@endpush
