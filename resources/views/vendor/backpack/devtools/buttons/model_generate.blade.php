{{-- Variables available here: $entry, $crud --}}
@php
$entryUrl = url("{$crud->route}/{$entry->getKey()}");
@endphp

<div class="btn-group dropdown">
    <button class="btn btn-sm btn-warning dropdown-toggle {{ $entry->can_generate_factory || $entry->can_generate_seeder || $entry->can_generate_crud ? '' : 'disabled' }}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Generate
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item {{ $entry->can_generate_crud ? '' : 'disabled' }}" href="{{ $entryUrl }}/build-crud">CRUD</a>
        <a class="dropdown-item {{ $entry->can_generate_factory ? '' : 'disabled' }}" href="{{ $entryUrl }}/build-factory">Factory</a>
        <a class="dropdown-item {{ $entry->can_generate_seeder ? '' : 'disabled' }}" href="{{ $entryUrl }}/build-seeder">Seeder</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ $entryUrl }}/build-all">All</a>
    </div>
</div>