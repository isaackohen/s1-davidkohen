{{-- regular object attribute --}}
@php
    $value = $value ?? data_get($entry, $column['name']);

    $column['limit'] = $column['limit'] ?? 120;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['text'] = $column['prefix'] . ($column['text'] ?? Str::of($value)->after(base_path())->trim('\\')->replace('\\', '/')->limit($column['limit'], '[...]')) . $column['suffix'];
    $column['path'] = $column['path'] ?? $entry->{$column['filePath'] ?? ''} ?? $value;
    $column['href'] = link_to_code_editor($column['path']);
@endphp

<span>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
        @if($column['path'])
        <a
            class="file-link" 
            @if ($column['href'] !== '')
            href="{{ htmlentities($column['href']) }}"
            @endif
            style="padding: .2em .4em; margin: 0; font-size: 95%; background-color: #1b1f230d; border-radius: 6px;"
            >
            {{ $column['text'] }}
            @if ($column['href'] !== '')
            <i class="la la-external-link-alt"></i>
            @endif
        </a>
        @else
        -
        @endif
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>
