@if($column['show_modifiers'])
    <div class="form-group col-md-1 mb-1 px-1 text-center">
    <label class="mb-1">{{ucfirst($label)}}</label>
@endif
	<input
        type="{{$type}}"
		wire:model="columns.{{ $column_index }}.modifiers.{{$label}}"
		name="columns[{{ $column_index }}][modifiers][{{$label}}]"
        class="form-control "
		wire:change="selectModifier({{ $column_index }}, '{{$label}}')"
		@if(in_array($label, $invalid_modifiers))
			readonly
		@else
		@if(isset($modifier_config['auto_modifiers']) && in_array($label, $modifier_config['auto_modifiers']))
		checked="checked"
		disabled
		@endif
		@endif

        />
@if($column['show_modifiers'])
</div>
@endif
