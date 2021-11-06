@if ($entry->canGenerateCrud)
	<a href="{{ url($crud->route.'/'.$entry->getKey().'/build-crud') }}" class="btn btn-sm btn-warning"><i class="la la-hammer"></i> Build CRUD</a>
@endif