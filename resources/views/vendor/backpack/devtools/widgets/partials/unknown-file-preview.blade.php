<table class="table table-striped mb-0">
    <tbody>
        <tr>
            <td>
                <strong>File path from base:</strong>
            </td>
            <td colspan="1">
                @include('backpack.devtools::columns.file-link', [
                    'column' => [
                        'name' => $key,
                        'path' => $item->file_path,
                    ],
                    'value' => $item->file_path_from_base,
                ])
            </td>
        </tr>
        <tr>
            <td colspan="2">
                @php
                if(!empty($item) && $item->isClass()) {
                    $error = $item->getErrors();
                }
                @endphp
                @if (isset($error))
                <p class="badge-warning rounded p-1">
                    <strong>{{ ucfirst($error->getMessage()) }}</strong> on line <strong>{{ $error->getLine() }}</strong>
                </p>
                @endif

                @include('backpack.devtools::columns.code', [
                    'column' => [
                        'name' => $key,
                    ],
                    'value' => $item->file_contents,
                ])
            </td>
        </tr>
    </tbody>
</table>
