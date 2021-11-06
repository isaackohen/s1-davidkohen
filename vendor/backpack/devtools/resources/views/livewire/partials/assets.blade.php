@push('before_styles')
    @livewireStyles
@endpush

@push('after_scripts')
    @livewireScripts

	<script>
		window.addEventListener('focus', (e) => {
			window.livewire.emit('updateModelList')
		});
	</script>

    	@include('backpack.devtools::livewire.components.modal', [
    		'componentName' => 'publish-modal',
    		'componentParameters' => [],
    	])
@endpush