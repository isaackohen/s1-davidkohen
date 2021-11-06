@if (\Alert::getMessages())
	<script>
	$( document ).ready(function() {
		var $showPublishModalAlerts = @this.get('showPublishModalAlerts');
		if ($showPublishModalAlerts) {
			var $alertsFlashedFromLivewire = JSON.parse('@json(\Alert::getMessages())');
			
			for (var type in $alertsFlashedFromLivewire) {
				let messages = new Set($alertsFlashedFromLivewire[type]);
				messages.forEach(text => new Noty({type, text}).show());
			}
		}
	});
	</script>
@endif