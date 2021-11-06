@if($entry->disabled === 0 || $entry->disabled === null)
<a href="{{ url($crud->route.'/'.$entry->provider.'/disableProvider') }} " class="btn btn-sm btn-link"><i class="la la-history"></i> Disable Provider</a>
@else
<a href="{{ url($crud->route.'/'.$entry->provider.'/enableProvider') }} " class="btn btn-xs btn-primary"><i class="la la-history"></i> Enable Provider</a>
@endif