(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
	$(document).ready(function($) {
		$('#target_person').hide();
		$('#target_radio').change(function() {
			var selected_value = $("input[name='target_radio_type']:checked").val();
			if(selected_value == "specific"){
				$('#target_person').show();
			}else{
				$('#target_person').hide();
			}
		});

		//save assignment
		$("#create_assignment").click(function() {
			var title = ($("#assignment_title").val()).trim();
			var address = ($("#loc_address").val()).trim();
			var lat_lon = ($("#lat_lon_input").val()).trim();
			var deadline = ($("#assignment_date").val()).trim();

			//check for required fields
			if(title == ""){
				alert("Title is required!");
				return;
			}
			if(deadline == ""){
				alert("Date is required!");
				return;
			}
			//check if valid date
			if(!moment(deadline).isValid()){
				alert("Invalid date format!");
				return;
			}
		});
	});

})( jQuery );
