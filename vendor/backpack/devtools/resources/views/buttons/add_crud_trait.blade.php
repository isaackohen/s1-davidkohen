@if ($entry->canAddCrudTrait)
    <a href="{{ url($crud->route.'/'.$entry->getKey().'/add-crud-trait') }}" class="btn btn-sm btn-outline-primary"><i class="la la-th-list"></i> Add CrudTrait</a>
@endif
