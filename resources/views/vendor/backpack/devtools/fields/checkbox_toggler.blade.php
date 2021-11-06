@php
    $field['wrapper'] = ['class' => ''];
@endphp
@include('crud::fields.checkbox')

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
<script>
    document.querySelectorAll('[toggles]').forEach(toggles => {
        const field = toggles.getAttribute('toggles');
        const checkbox = toggles.querySelector('input[type="checkbox"]');

        const onCheckBoxChange = () => {
            document.querySelectorAll(`[toggler="${field}"]`).forEach(toggler =>
                [toggler, ...toggler.querySelectorAll('input')].forEach(input => {
                    if(input.getAttribute('type') === 'checkbox') {
                        input.checked = checkbox.checked ? input.getAttribute('checked') === 'checked' ? true : false : false;
                        $(input).closest('input[type=hidden]').val(checkbox.checked ? input.getAttribute('checked') === 'checked' ? 1 : 0 : 0)
                    }

                    input.toggleAttribute('disabled', !checkbox.checked)
                })
            );
        }

        checkbox.addEventListener('change', onCheckBoxChange)

        onCheckBoxChange();
    });
</script>
@endpush

{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('crud_fields_styles')
<style>
    .form-group[toggler][disabled] {
        opacity: .5;
        pointer-events: none;
    }
</style>
@endpush
