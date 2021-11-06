@if ($crud->hasAccess('show'))
	<a href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}" class="btn btn-sm btn-outline-primary"><i class="la la-eye"></i> Details</a>
@endif