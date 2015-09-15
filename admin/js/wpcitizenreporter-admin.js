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
		$('#assignment_target_person').hide();
		$('#assignment_target').change(function() {
			var selected_value = $("input[name='target_radio_type']:checked").val();
			if(selected_value == "specific"){
				$('#assignment_target_person').show();
			}else{
				$('#assignment_target_person').hide();
			}
		});

		//save assignment
		$("#create_assignment").click(function() {
			var title = ($("#assignment_title").val()).trim();
			var description = ($("#assignment_description").val()).trim();
			var address = ($("#loc_address").val()).trim();
			var lat_lon = ($("#lat_lon_input").val()).trim();

			var types = new Array();
			$.each($("input[name='assignment_type[]']:checked"), function() {
				types.push($(this).val());
			});

			var target = ($("input[name='target_radio_type']:checked").val()).trim();
			var target_person = ($("#assignment_target_person").val()).trim();
			var deadline = ($("#assignment_date").val()).trim();
			var bounty = ($("#assignment_bounty").val()).trim();

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

			//perform ajax request
			var post_url = jQuery("#post_url").attr("href");
			jQuery.post(post_url, {
				title: title,
				description: description,
				types:	types,
				address: address,
				lat_lon: lat_lon,
				target: target,
				target_person: target_person,
				deadline: deadline,
				bounty: bounty
			})
				.done(function( data ) {
					var output = "";

					if(data == 1){

						output = "Assignment created successfully!"
						//now clear the input boxes
						$(".quick_assignment_form").val("");

						//clear assignment checkboxes
						$('.quick_assignment_form_check').prop('checked', false);

						//restore defaults
						$('.default_check').prop('checked', true);
						$('#assignment_target_person').hide();

					}else if(data == 0){
						output = "Problem creating assignment! Do you have the right permissions?";
					}else{
						output = "You are not logged in!";
					}

					jQuery("#assignment_created").html(output);

					//fade out response message after 2 seconds
					setTimeout(function(){
						$("#assignment_created").fadeOut("slow");
					},2000)
			});

		});
	});

})( jQuery );
