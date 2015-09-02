<?php

/**
 * This file for modification of dashboard look and adding/removing widgets
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/CodeForAfrica/WPCitizenReporter
 *
 * @since      1.0.0
 * @package    WPCitizenReporter
 * @subpackage WPCitizenReporter/includes
 * @author     Nick Hargreaves <nick@codeforafrica.org>
 */

class WPCitizenReporter_Dashboard {

	// Custom Dashboard
	public function summary_widget() {
		$screen = get_current_screen();
		if( $screen->base == 'dashboard' ) {
			?>

			<!-- New Wrap with custom welcome screen-->
			<div class="wrap mjp-dashboard">
				<div id="welcome-panel_custom" class="welcome-panel">
					<?php wp_nonce_field( 'welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
					<?php //do_action( 'welcome_panel' ); ?>
					<div class="mjp-welcome-content">
						<div class="welcome-panel-column-container">
							<div class="welcom-panel-container">
								<div class="col-xs-3">
									<div class="tile">
										<h3 class="tile-title">Stories</h3>
										<span class="fui-new"></span>
										<p><?php $posts = wp_count_posts();
											print "Accepted: ". $posts->publish;
											print " ";
											print "Pending: ". $posts->draft;
											?></p>
										<a class="btn btn-primary btn-large btn-block" href="<?php print admin_url();?>edit.php">View</a>
									</div>
								</div>

								<div class="col-xs-3">
									<div class="tile">
										<h3 class="tile-title">Video</h3>
										<span class="fui-video"></span>
										<p><?php print $this->media_totals('video');?></p>
										<a class="btn btn-primary btn-large btn-block" href="<?php print admin_url();?>upload.php?post_mime_type=video">View</a>
									</div>
								</div>

								<div class="col-xs-3">
									<div class="tile">
										<h3 class="tile-title">Photos</h3>
										<span class="fui-photo"></span>
										<p><?php print $this->media_totals('image');?></p>
										<a class="btn btn-primary btn-large btn-block" href="<?php print admin_url();?>upload.php?post_mime_type=image">View</a>
									</div>
								</div>

								<div class="col-xs-3">
									<div class="tile tile-hot">
										<h3 class="tile-title">Audio</h3>
										<span class="fui-mic"></span>
										<p><?php print $this->media_totals('audio');?></p>
										<a class="btn btn-primary btn-large btn-block" href="<?php print admin_url();?>upload.php?post_mime_type=audio">View</a>
									</div>

								</div>
							</div>
						</div>

					</div>
				</div>
			</div><!-- wrap -->
			<?php
		}
	}

	public function media_totals($mediaType){

		if($mediaType==null){
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => null,
			);
		}else{
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => -1,
				'post_status' => null,
				'post_parent' => null,
				'post_mime_type' => $mediaType,
			);
		}

		$attachments = count(get_posts($args));

		return $attachments." submissions";
	}

	public function user_totals(){
		// prepare arguments
		$args  = array('role' => 'Author');
		// Create the WP_User_Query object
		$wp_user_query = new WP_User_Query($args);
		// Get the results
		$authors = $wp_user_query->get_results();

		return $authors;
	}

	public function remove_default_widgets(){
			remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
			remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8

	}

	public function add_quick_draft_assignment_dashboard_widget(){


			wp_add_dashboard_widget(
				'quick_draft_assignment_dashboard_widget',         // Widget slug.
				'Create Assignment',         // Title.
				'quick_draft_assignment_dashboard_widget_function' // Display function.
			);
			function quick_draft_assignment_dashboard_widget_function() {
				?>
				
				<div class="quick_assignment">
					<h2>Create Assignment</h2>
				<div class="assignment_summary">
					<input type="text" placeholder="Title">
					<textarea placeholder="Description"></textarea>

					<h3>Media Types</h3>
					<div class="assigmnet_media_type">
						<input type="checkbox" value="narrative" name="assignment_type[]">
						<i class="fa fa-list-alt fa-assignment"></i>
						Narrative
					</div>
				<div class="assigmnet_media_type">

					<input type="checkbox" value="image" name="assignment_type[]" checked>
					<i class="fa fa-photo fa-assignment"></i>
					Image
				</div>

				<div class="assigmnet_media_type">

					<input type="checkbox" value="audio" name="assignment_type[]">
					<i class="fa fa-music fa-assignment"></i>
					Audio
				</div>
				<div class="assigmnet_media_type">

					<input type="checkbox" value="video" name="assignment_type[]">
					<i class="fa fa-video-camera fa-assignment"></i>
					Video
				</div>
				</div>
				<div class="assigment_location">
					<h3>Location</h3>
					<?php

				//nairobi defaults
				$location = "-1.2920659, 36.8219462";

				?>
				<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
				<input type="text" id="lat_lon_input" name="lat_lon_input" value="<?php echo $location;?>" />

				<script>
					function initialize() {
						var mapOptions = {
							center: new google.maps.LatLng(<?php echo $location;?>),
							zoom: 13,
							disableDefaultUI: true,
							mapTypeControl: false,
							draggable: false,
							scaleControl: false,
							scrollwheel: false,
							navigationControl: false,
							streetViewControl: false,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						var map = new google.maps.Map(document.getElementById('map-canvas'),
							mapOptions);

						var input = /** @type {HTMLInputElement} */(
							document.getElementById('pac-input'));

						var lat_lon_input = /** @type {HTMLInputElement} */(
							document.getElementById('lat_lon_input'));

						var types = document.getElementById('type-selector');
						map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
						map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

						var autocomplete = new google.maps.places.Autocomplete(input);
						autocomplete.bindTo('bounds', map);

						var infowindow = new google.maps.InfoWindow();
						var marker = new google.maps.Marker({
							map: map,
							anchorPoint: new google.maps.Point(0, -29)
						});

						google.maps.event.addListener(autocomplete, 'place_changed', function() {
							infowindow.close();
							marker.setVisible(false);
							var place = autocomplete.getPlace();
							if (!place.geometry) {
								return;
							}

							// If the place has a geometry, then present it on a map.
							if (place.geometry.viewport) {
								map.fitBounds(place.geometry.viewport);
							} else {
								map.setCenter(place.geometry.location);
								map.setZoom(17);  // Why 17? Because it looks good.
							}
							lat_lon_input.value = place.geometry.location;
							marker.setIcon(/** @type {google.maps.Icon} */({
								url: place.icon,
								size: new google.maps.Size(71, 71),
								origin: new google.maps.Point(0, 0),
								anchor: new google.maps.Point(17, 34),
								scaledSize: new google.maps.Size(35, 35)
							}));
							marker.setPosition(place.geometry.location);
							marker.setVisible(true);

							var address = '';
							if (place.address_components) {
								address = [
									(place.address_components[0] && place.address_components[0].short_name || ''),
									(place.address_components[1] && place.address_components[1].short_name || ''),
									(place.address_components[2] && place.address_components[2].short_name || '')
								].join(' ');
							}

							infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
							infowindow.open(map, marker);
						});
						//show address on info window if address is not null
						<?php
                            if(!empty($address)){
                        ?>


						show_info_window();

						function show_info_window() {

							var myLatlng = new google.maps.LatLng(<?php echo $location;?>);

							var contentString = '';

							var infowindow = new google.maps.InfoWindow({
								content: contentString
							});

							var marker = new google.maps.Marker({
								position: myLatlng,
								map: map,
								title: '<?php echo $address;?>'
							});

							google.maps.event.addListener(marker, 'click', function() {
								infowindow.open(map,marker);
							});

						}
						<?php
                            }
                        ?>
						// Sets a listener on a radio button to change the filter type on Places
						// Autocomplete.
						function setupClickListener(id, types) {
							var radioButton = document.getElementById(id);
							google.maps.event.addDomListener(radioButton, 'click', function() {
								autocomplete.setTypes(types);
							});
						}

						setupClickListener('changetype-all', []);
						setupClickListener('changetype-address', ['address']);
						setupClickListener('changetype-establishment', ['establishment']);
						setupClickListener('changetype-geocode', ['geocode']);
					}

					google.maps.event.addDomListener(window, 'load', initialize);

				</script>
				<input id="pac-input" class="controls" type="text" name="loc_address" value="<?php echo $address;?>"
					   placeholder="Enter a location">

				<div id="type-selector" class="controls">
					<input type="radio" name="type" id="changetype-all" checked="checked">
					<label for="changetype-all">All</label>

					<input type="radio" name="type" id="changetype-establishment">
					<label for="changetype-establishment">Establishments</label>

					<input type="radio" name="type" id="changetype-address">
					<label for="changetype-address">Addresses</label>

					<input type="radio" name="type" id="changetype-geocode">
					<label for="changetype-geocode">Geocodes</label>
				</div>
				<div id="map-canvas"></div>


				</div>
				<div class="assignment_target">
					<h3>To whom do you want to send the assignment to?</h3>
					<radiogroup>
					<input type="radio" name="type" id="changetype-all" checked="checked">
					<label for="changetype-all">Everyone</label>

					<input type="radio" name="type" id="changetype-all">
					<label for="changetype-all">People near the specified location</label>
					<br />
					<input type="radio" name="type" id="changetype-all">
					<label for="changetype-all">Specific person</label>

					<input type="text" name="taget" id="assignment_target" placeholder="Enter name">

					</radiogroup>
				</div>
				<div class="assignment_bounty">
					<h3>Bounty</h3>
					<input type="text" id="bounty" name="bounty"  value="" placeholder="How much do you want to pay for it?"/>
				</div>
				<div class="assignment_finish">
					<input type="submit" name="save" class="button button-primary" value="Create assignment">
				</div>
				</div>
				<?php
			}

	}
}





