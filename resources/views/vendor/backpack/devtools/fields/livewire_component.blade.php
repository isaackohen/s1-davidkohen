@include('crud::fields.inc.wrapper_start')
	@livewire($field['component'], array_merge($field['parameters'] ?? [], ['field' => $field]))
@include('crud::fields.inc.wrapper_end')