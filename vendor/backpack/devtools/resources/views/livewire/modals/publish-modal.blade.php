<livewire-publish-modal id="livewire-publish-modal">

	<!-- Modal Content -->
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title">Publish {{ ucfirst($selectedFileType) }}</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      	<div class="form-group row">
                <label class="col-md-12 col-form-label" for="file-to-publish">What {{ $selectedFileType }} do you want to publish?</label>
                <div class="col-md-12">
                  <select name="selectedFile" wire:model="selectedFile" class="form-control" id="file-to-publish">
              	    {{-- <option value="">-</option> --}}
                  	@foreach($visibleOptions as $key => $option)
                        <option value="{{ $key }}" wire:key="file_option_{{ $loop->index }}">{{ $option }}</option>
                  	@endforeach
                  </select>
	              <div class="text-muted font-sm mt-4">This will copy-paste the blade file from the Backpack package to your <code>{{ $selectedFileTypePath }}</code>, where you can customize it to fit your needs. Backpack will automatically use the published file if present. <br><br>Take into consideration that by publishing (aka overriding) a blade file, you will no longer get the updates for that blade file when you do <code>composer update</code>. For an easy-to-upgrade admin panel, it's recommended that you override blade files as little as possible. In most cases, it would be better to <i>rename</i> the file after it's been published, and only use it inside the Controllers/Views where strictly needed.</div>
                </div>
             </div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-primary" wire:click="publishFile">Publish</button>
	      </div>
	    </div>
	  </div>

	  @include('backpack.devtools::livewire.partials.alerts')

	  <script>
	  	// when a menu item that triggers this modal is clicked, set
	  	// the value of selectedFileType to what the menu intended 
	  	// so that the dropdown gets populated with the blade
	  	// files for that type of file
		$("#devToolsNavBar a.dropdown-item[data-target='#livewire-publish-modal']").click(function() {
			@this.set('selectedFileType', $(this).data('file-type'));
			@this.set('showPublishModalAlerts', true);
			{{-- @this.$refresh(); --}}
		});

		// when the publish modal is closed toggle the visibility of the alerts
		$('#livewire-publish-modal').on('hidden.bs.modal', function () {
			@this.set('showPublishModalAlerts', false);
		});

	  </script>

</livewire-publish-modal>
