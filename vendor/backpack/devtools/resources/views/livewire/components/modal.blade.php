<div class="modal fade" 
	id="livewire-{{ $componentName }}" 
	tabindex="-1" 
	aria-labelledby="livewire-{{ $componentName }}" 
	aria-hidden="true">
	@livewire($componentName, $componentParameters ?? [])
</div>