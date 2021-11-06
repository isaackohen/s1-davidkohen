<div class="form-group col-md-1 mb-1 px-1">
	<label class="mb-1">{{ ucfirst(str_replace('_', ' ', $label)) }}</label>
	<input
		type="number"
		wire:model="columns.{{ $column_index }}.args.{{ $label }}"
		name="columns[{{ $column_index }}][args][{{ $label }}]"
		@foreach($attributes as $attribute => $value)
		{{ $attribute }}="{{ $value }}"
		@endforeach
		class="form-control"
		@if($force)
		readonly
		@endif
		/>
</div>
