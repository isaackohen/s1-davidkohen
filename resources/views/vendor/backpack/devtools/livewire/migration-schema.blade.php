<migration-schema>

    <label class="d-block">{{ $field['label'] ?? 'Migration Schema' }}</label>
    @foreach($columns as $column_index => $column)
      @php
          $modifier_configuration_for_colummn_type = $selectable_column_types[$column['column_type']]['configs'] ?? [];
      @endphp
      <div wire:key="column-definition-{{ $column_index }}" class="migration-schemas-container">
        <div class="col-md-12 well migration-schema row m-1 p-2 pl-3">
          <div class="side-button-group">
            <button type="button"
                  @if(is_int($column['has_relationship']))
                  wire:click="confirmColumnDelete('removeColumnAndRelationship', {{$column_index}})"
                  @else
                  wire:click="confirmColumnDelete('removeColumn', {{ $column_index }})"
                  @endif
                  class="close delete-element ">
              <span aria-hidden="true">×</span>
            </button>

            @if(!$loop->first && !in_array($column['column_type'], ['timestamps', 'timestampsTz']))
            <button type="button"
                    wire:click="moveColumnOrderUp({{ $column_index }})"
                    class="close move-up-element">
                    <span aria-hidden="true">&uarr;</span>
            </button>
            @endif
            @if (!$loop->last && !in_array($columns[$column_index+1]['column_type'], ['timestamps', 'timestampsTz']))
            <button type="button"
                    wire:click="moveColumnOrderDown({{ $column_index }})"
                    class="close move-down-element">
                    <span aria-hidden="true">&darr;</span>
            </button>
            @endif
          </div>
          <div class="form-group col-md-3 mb-1 px-1">
            <input type="hidden" value="{{$column['show_modifiers'] === false ? 0 : 1}}" name="columns[{{$column_index}}][show_modifiers]" />
            <label class="mb-1">Column Name</label>
            <input  type="text"
                    name="columns[{{ $column_index }}][column_name]"
                    wire:model.lazy="columns.{{ $column_index }}.column_name"
                    value="{{$column['column_name'] ?? ''}}"
                    @if(isset($selectable_column_types[$column['column_type']]['configs']) && is_array($selectable_column_types[$column['column_type']]['configs']))
                      @if(isset($selectable_column_types[$column['column_type']]['configs']['placeholder']))
                    disabled
                    placeholder="{{$selectable_column_types[$column['column_type']]['configs']['placeholder']}}"
                      @endif
                    @endif

                    class="form-control " />

          </div>

          <div class="form-group col-md-2 mb-1 px-1">
            <label class="mb-1">Column Type</label>
            <select
              name="columns[{{ $column_index }}][column_type]"
              wire:model="columns.{{ $column_index }}.column_type"
              wire:change="fetchColumnSpecificFields({{$column_index}})"
              class="form-control">
              @foreach($selectable_column_types_order as $label => $column_type)
                @if(is_array($column_type))
                  <optgroup label="{{ ucfirst($label) }}">
                    @foreach($column_type as $value)
                    <option value="{{ $value }}" {{ $this->isColumnTypeEnabled($value, $column) ? '' : 'disabled' }}>{{ ucfirst($value) }}</option>
                    @endforeach
                  </optgroup>
                @else
                  <option value="{{ $column_type }}" {{ $this->isColumnTypeEnabled($column_type, $column) ? '' : 'disabled' }}>{{ ucfirst($column_type) }}</option>
                @endif
              @endforeach
            </select>
          </div>

          {{-- Include any column special fields --}}
          @if (view()->exists('backpack.devtools::livewire.migration-schema.column-types.'.strtolower($column['column_type'])))
              @include('backpack.devtools::livewire.migration-schema.column-types.'.strtolower($column['column_type']))
          @endif

          @if(isset($selectable_column_types[$column['column_type']]) && is_array($selectable_column_types[$column['column_type']]))
            @foreach($selectable_column_types[$column['column_type']] as $ct_type_extra => $ct_type_extra_config)
              @if($ct_type_extra !== 'configs')
                @include('backpack.devtools::livewire.migration-schema.types.'.$ct_type_extra_config['type'], [
                  'label' => $ct_type_extra,
                  'force' => $ct_type_extra_config['force'] ?? false,
                  'attributes' => $ct_type_extra_config['attributes'] ?? [],
                ])
              @endif
            @endforeach
          @endif

          @if(isset($column['modifiers']['nullable']))
            <div class="form-group col-md-1 mb-1 px-1 text-center">
              <label class="mb-1">Nullable</label>
              <input  type="checkbox"
                      name="columns[{{ $column_index }}][modifiers][nullable]"
                      wire:model="columns.{{ $column_index }}.modifiers.nullable"

                      wire:change="selectModifier({{ $column_index }}, 'nullable')"
                      class="form-control"
                      @if(in_array('nullable', $column['invalid_modifiers']))
                              disabled
                              value=""
                          @else
                        @if(isset($modifier_configuration_for_colummn_type['auto_modifiers']) && in_array('nullable', $modifier_configuration_for_colummn_type['auto_modifiers']))
                          checked="checked"
                              disabled
                        @endif
                      @endif
                      />
            </div>
          @endif

          @if (isset($columns[$column_index]['show_modifiers']) && $columns[$column_index]['show_modifiers'] != true)
            <div class="form-group col-md-2 mb-1 px-1 ">
              <label class="mb-1"> &nbsp; </label>
              <button class="d-block mt-1 ml-1 btn btn-sm btn-link"
                      type="button"
                      href="#"
                      wire:click.prevent="showModifiers({{ $column_index }})">
                      More >
              </button>
            </div>
          @endif


              @foreach($column['column_type_modifiers'] as $modifier)
                @if($modifier != 'nullable' && in_array($operation, $column_modifiers[$modifier]['operations']))
                  @php
                    $column_modifier_definition = $column_modifiers[$modifier];
                    if(!isset($column_modifier_definition['type'])) {
                      continue;
                    }
                  @endphp
                    @include('backpack.devtools::livewire.migration-schema.modifiers.'.$column_modifier_definition['type'],[
                            'label' => $modifier,
                            'modifier_config' => $modifier_configuration_for_colummn_type,
                            'invalid_modifiers' => $column['invalid_modifiers'],
                            'type' => isset($column['show_modifiers']) && $column['show_modifiers'] === false ? 'hidden' : $column_modifier_definition['type']
                            ])
                @endif
              @endforeach
              @if($column['show_modifiers'])
              <div class="form-group col-md-2 mb-1 px-1">
                <label class="mb-1">Charset</label>
                <select name="columns[{{ $column_index }}][modifiers][charset]"
                  wire:model="columns.{{ $column_index }}.modifiers.charset"
                  class="form-control">
                  <option value="">-</option>
                  @foreach ($charset_and_collation as $charset => $collations)
                  <option
                      value="{{ $charset }}">
                      {{ $charset }}
                  </option>
                  @endforeach
                </select>
              </div>
            @else
              <input type="hidden" name="columns[{{ $column_index }}][modifiers][charset]" wire:model="columns.{{ $column_index }}.modifiers.charset">
            @endif
            @if($column['show_modifiers'])
              <div class="form-group col-md-3 mb-1 px-1">
                <label class="mb-1">Collation</label>
                <select name="columns[{{ $column_index }}][modifiers][collation]"
                  wire:model="columns.{{ $column_index }}.modifiers.collation"
                  class="form-control">
                  <option value="">-</option>
                  @if(!empty($column['modifiers']['charset']))
                    @foreach ($charset_and_collation[$column['modifiers']['charset']] as $collation)
                    <option
                        value="{{ $collation }}">
                        {{ $collation }}
                    </option>
                    @endforeach
                  @endif
                </select>
              </div>
            @else
              <input type="hidden" name="columns[{{ $column_index }}][modifiers][collation]" wire:model="columns.{{ $column_index }}.modifiers.collation">
            @endif

            <div class="form-group col-md-2 mb-1 px-1 @if(isset($column['show_modifiers']) && $column['show_modifiers'] === false) d-none @endif">
              <label class="mb-1"> &nbsp; </label>
              <button class="d-block mt-1 ml-1 btn btn-sm btn-link"
                      type="button"
                      href="#"
                      wire:click.prevent="hideModifiers({{ $column_index }})">
                      < Hide
              </button>
            </div>

           

            </div>
            @if(!$loop->last && in_array($columns[$column_index+1]['column_type'], ['timestamps', 'timestampsTz']))
              <button type="button" wire:click.prevent="addColumn" class="btn btn-primary btn-sm ml-1 mt-3 mb-3 add-migration-schema-button">+ Add Column</button>
            @elseif($loop->last && 
                        empty(array_filter($columns, function($column) { 
                                return in_array($column['column_type'], ['timestamps', 'timestampsTz'] );
                              })) || (count($columns) === 1 && in_array($column['column_type'], ['timestamps', 'timestampsTz'] ))
            )
              <button type="button" wire:click.prevent="addColumn" class="btn btn-primary btn-sm ml-1 mt-3 mb-3 add-migration-schema-button">+ Add Column</button>
            @endif
      </div>
    @endforeach
    
    @if (empty($columns))
        <button type="button" wire:click.prevent="addColumn" class="btn btn-primary btn-sm ml-1 mt-3 mb-3 add-migration-schema-button">+ Add Column</button>
    @endif
  
  
</migration-schema>

@push('crud_fields_styles')
  <!-- no styles -->
  <style type="text/css">
    .migration-schema {
      border: 1px solid #0028641f;
      background-color: #f0f3f94f;
      border-radius: 3px;
    }

    .migration-schemas-container .side-button-group {
      position: absolute !important;
      z-index: 2;
      width: 1.5rem;
      top: 0;
      left: 0;
      transform: translateX(-50%);
    }

    .migration-schemas-container .side-button-group button {
      width: 100%;
      border-radius: 50%;
      text-align: center;
      background-color: #e8ebf0 !important;
      margin-bottom: .3rem;
    }

    .migration-schemas-container .side-button-group button:focus {
      outline: none;
    }

    .migration-schemas-container input[type="checkbox"] {
      max-width: 1rem;
      margin: auto;
    }

    .migration-schemas-container a.add-relationship {
      float: right;
      transform: scale(0.8);
    }
  </style>
@endpush

@push('after_scripts')
  <script>
    // when a new migration column is added
    window.addEventListener('newColumnAdded', (event) => {
      // focus on the first field for that new column
      var columnNamePrefix = 'columns[' + event.detail.columnIndex + ']';

      var columnSelector = 'input[name^="' + columnNamePrefix + '"]:visible' +
                            ', select[name^="' + columnNamePrefix + '"]:visible'+
                            ', checkbox[name^="' + columnNamePrefix + '"]:visible'+
                            ', radio[name^="' + columnNamePrefix + '"]:visible';

      $(columnSelector).first().trigger('focus');
    });

  window.addEventListener('confirmColumnDelete', (e) => {
      let message = 'Are you sure you want to delete this column?';
      if (e.detail.callback === 'removeColumnAndRelationship') {
        message = 'This column has an associated relationship. Are you sure you want to delete both the relationship and the column?';
      }
      if (!confirm(message)) { return }
      @this[e.detail.callback](...e.detail.argv)
  });

  </script>
@endpush
