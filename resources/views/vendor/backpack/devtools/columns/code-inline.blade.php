{{-- regular object attribute --}}
@php
	$value = data_get($entry, $column['name']);
    $value = is_array($value) ? json_encode($value) : $value;

    $column['limit'] = $column['limit'] ?? 120;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['text'] = $column['prefix'] . Str::limit($value, $column['limit'], '[...]') . $column['suffix'];
    $column['path'] = $entry->{$column['filePath'] ?? ''} ?? $value;
@endphp

<span>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        <span class="code-inline">
            {{ $column['text'] }}
        </span>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>

<style>
    span.code-inline {
        font-family: monospace;
        padding: .2em .4em;
        margin: 0;
        font-size: 95%;
        background-color: #1b1f230d;
        border-radius: 6px;
    }
</style>