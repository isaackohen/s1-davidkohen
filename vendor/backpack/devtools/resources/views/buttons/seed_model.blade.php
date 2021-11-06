@if ($crud->hasAccess('seedModel'))
<button class="btn btn-sm btn-outline-primary seed-model {{ $entry->canSeed ? '' : 'disabled' }}" onclick="openSeedModelModal('{{ $entry->id }}')">
    <span class="ladda-label">
        <i class="la la-plus"></i>
        <span>Seed</span>
    </span>
    <template>
        <form>
            <p>How many dummy entries do you want to create?</p>
            <input name="count" type="number" placeholder="25" class="form-control" />
            <input name="option" type="hidden" value="1" />
        </form>
    </template>
</button>
@endif

{{-- Button Javascript --}}
<script>
// API fetch
var fetchSeedModel = (form, id) => {
    let action = `{{ url($crud->route) }}/${id}/seed-model`;
    let body = new FormData(form);

    // Fetch the action
    fetch(action, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content,
        },
        body,
    }).then(response => {
        response.json()
            .then(data => new Noty({
                type: response.ok ? 'success' : 'error',
                text: `<strong>${data.title}</strong><br>${data.message}`,
            }).show())
            .catch(error => new Noty({
                type: 'error',
                text: '<strong>Seeder failed</strong><br>One or more entries could not be created.',
            }).show());
    });
}

// Override 
var openSeedModelModal = id => {
    const CREATE = 1;
    const TRUNCATE = 2;

    // Prepare form
    const form = document.querySelector('.seed-model template').content.cloneNode(true).querySelector('form');
    const formInput = form.querySelector('input[name="count"]');

    // Save current count value localy
    formInput.oninput = () => localStorage.setItem('devtools_seed_model_count', formInput.value);

    // Load count value from local storage
    formInput.value = localStorage.getItem('devtools_seed_model_count', 25);

    // Prevent form submit
    form.onsubmit = e => {
        e.preventDefault();
        fetchSeedModel(form, id);
        swal.close();
    };

    // Alert
    swal({
        content: form,
        icon: 'info',
        className: 'seed-model',
        buttons: {
            truncateAndCreate: {
                text: 'Truncate & Create',
                value: TRUNCATE | CREATE,
                visible: true,
                className: 'bg-danger',
            },
            create: {
                text: 'Create',
                value: CREATE,
                visible: true,
                className: 'bg-primary',
            },
        },
    }).then(option => {
        form.option.value = option;

        if (option & TRUNCATE) {
            swal({
                text: 'Are you sure you want to DELETE ALL ENTRIES, then create :number dummy entries?'.replace(':number', formInput.value),
                icon: 'warning',
                buttons: ['Cancel', 'Truncate & Create'],
                dangerMode: true,
            }).then(value => {
                // Proceed with the fetch or go back to previoud modal
                value ? fetchSeedModel(form, id) : openSeedModelModal(id);
            });
        } else if (option & CREATE) {
            fetchSeedModel(form, id);
        }
    });

    // Focus input
    formInput.focus();
}
</script>

{{-- Button Style --}}
<style>
button.seed-model {
    display: inline-block;
    cursor: pointer;
}

button.seed-model.btn.disabled {
    pointer-events: none;
}

.swal-modal.seed-model input {
    max-width: 65%;
    margin: auto;
}

.swal-modal.seed-model .swal-text {
    text-align: center;
}

.swal-modal.seed-model .swal-footer {
    display: flex;
    justify-content: center;
    flex-direction: row-reverse;
}

.swal-modal.seed-model .swal-button-container > button {
    min-width: 10rem;
}
</style>
