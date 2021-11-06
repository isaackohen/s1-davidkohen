<table class="table table-striped mb-0">
    <tbody>
    @foreach ($crud->columns() as $column)
        @php
            $code = ($column['type'] == 'view' && isset($column['view']) && $column['view'] == 'backpack.devtools::columns.code');
        @endphp
        <tr>
            @if (!$code)
            <td>
                <strong>{!! $column['label'] !!}:</strong>
            </td>
            @endif
            <td colspan="{{ $code?2:1 }}">
                @if (!isset($column['type']))
                  @include('crud::columns.text')
                @else
                  @if(view()->exists('vendor.backpack.crud.columns.'.$column['type']))
                    @include('vendor.backpack.crud.columns.'.$column['type'])
                  @else
                    @if(view()->exists('crud::columns.'.$column['type']))
                      @include('crud::columns.'.$column['type'])
                    @else
                      @include('crud::columns.text')
                    @endif
                  @endif
                @endif
            </td>
        </tr>
    @endforeach
    @if ($crud->buttons()->where('stack', 'line')->count())
        <tr>
            <td><strong>{{ trans('backpack::crud.actions') }}</strong></td>
            <td>
                @include('crud::inc.button_stack', ['stack' => 'line'])
            </td>
        </tr>
    @endif
    </tbody>
</table>
