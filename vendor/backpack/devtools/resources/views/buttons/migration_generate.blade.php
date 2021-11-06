{{-- Variables available here: $entry, $crud --}}

@if ($entry->executed)
	<button href="{{ url($crud->route.'/'.$entry->getKey().'/run') }}" class="btn btn-sm btn-warning"><i class="la la-terminal"></i> Generate Model</button>
@else
	<button href="#" class="btn btn-sm btn-warning btn-disabled" title="Cannot run migration once it's already been executed once." disabled><i class="la la-terminal"></i> Generate Models</button>
@endif
