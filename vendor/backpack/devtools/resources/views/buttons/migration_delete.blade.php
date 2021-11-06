@if ($entry->executed)
	<a 	href="{{ url($crud->route.'/'.$entry->getKey().'/delete-migration') }}" 
		class="btn btn-sm btn-outline-danger" 
		onclick="return confirm('CAUTION! This migration has already been executed. Are you super-sure you want to do this?')">
		<i class="la la-terminal"></i> Delete
	</a>
@else
	<a 	href="{{ url($crud->route.'/'.$entry->getKey().'/delete-migration') }}" 
		class="btn btn-sm btn-outline-warning"
		onclick="return confirm('This migration hasn\'t been executed yet. So it\'s safe to delete. But... are you sure you want to do this?')">
		<i class="la la-terminal"></i> Delete
	</a>
@endif
