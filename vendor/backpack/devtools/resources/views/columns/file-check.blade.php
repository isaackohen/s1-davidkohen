{{-- checkbox with loose false/null/0 checking --}}
@php
    $file = $column['file']($entry);

    $column['icon'] = $file ? 'check-circle' : 'circle';
    $column['wrapper'] = [
        'title' => $file->file_path_from_base ?? '',
        'class' => "text-decoration-none text-" . ($file ? 'success' : 'danger'),
        'element' => $file ? 'a' : 'div',
        'href' => backpack_url("/devtools/model/{$entry->id}/related-files/{$column['href']}"),
    ];
@endphp

<span>
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start')
    <i class="la la-{{ $column['icon'] }}"></i>
    @if($file && !$file->isValid())
    <i class="la la-warning text-danger" title="File has syntax errors."></i>
    @endif
    @includeWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end')
</span>