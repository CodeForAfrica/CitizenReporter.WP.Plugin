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

							</div>
						</div>

					</div>
				</div>
			</div><!-- wrap -->
			<?php
		}
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
	public function quick_chat_dashboard_widget()
	{

		wp_add_dashboard_widget(
			'quick_chat_dashboard_widget',         // Widget slug.
			'Payments',         // Title.
			'payments_dashboard_widget_function' // Display function.
		);

		function payments_dashboard_widget_function()
		{
			$args = array(
				'posts_per_page'   => 500000,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => '',
				'orderby'          => 'date',
				'order'            => 'DESC',
				'include'          => '',
				'exclude'          => '',
				'post_type'        => 'payment',
				'post_mime_type'   => '',
				'post_parent'      => '',
				'post_status'      => 'published',
				'suppress_filters' => true
			);
			$payments = get_posts($args);
			?>
			<table class="fancy_table" cellspacing="0" summary="Payments">
				<caption></caption>
				<tbody>
				<tr>
					<th scope="col" class="nobg">Post</th>
					<th scope="col">User</th>
					<!--
						<th scope="col">Receipt</th>
					-->
					<th scope="col">Amount</th>
					<th scope="col">Status</th>
				</tr>
				<?php
				foreach($payments as $payment){
					//get meta values
					$post_id = get_post_meta($payment->ID, "post_id", true);
					$post = get_post($post_id);
					$pay_amount = get_post_meta($payment->ID, "pay_amount", true);
					$user_id = get_post_meta($payment->ID, "user", true);
					$user = get_user_by('id', $user_id);
					$receipt = get_post_meta($payment->ID, "receipt", true);
					$confirm = get_post_meta($payment->ID, "confirm", true);


					if($confirm==null){
						$btn = " btn-default";
						$confirm = "None";
					}else if($confirm=="1"){
						$btn = " btn-success";
						$confirm = "Confirmed";
					}else{
						//disputed
						$btn =" btn-danger";
						$confirm = "Disputed!";
					}


					//color code due date
					print '<tr class="edit_assignment" data-href="'.$payment->ID.'">
									<th scope="row" class="spec"><a href="post.php?post='.$post->ID.'&action=edit">'.$post->post_title.'</a></th>
									<td><a href="user-edit.php?user_id='.$user_id.'">'.$user->user_nicename.'</a></td>
									<td>'.$pay_amount.'</td>
									<td><button class="assign-btn btn btn-xs'.$btn.'">'.$confirm.'</button></td>
								</tr>';

				}
				?>
				?>

				</tbody></table>
			<?php

		}

	}
	public function active_assignments_dashboard_widget(){


		wp_add_dashboard_widget(
			'active_assignments_dashboard_widget',         // Widget slug.
			'Active Assignments',         // Title.
			'active_assignments_dashboard_widget_function' // Display function.
		);
		function active_assignments_dashboard_widget_function() {
			$args = array(
				'posts_per_page'   => 500000,
				'offset'           => 0,
				'category'         => '',
				'category_name'    => '',
				'orderby'          => 'date',
				'order'            => 'DESC',
				'include'          => '',
				'exclude'          => '',
				'post_type'        => 'assignment',
				'post_mime_type'   => '',
				'post_parent'      => '',
				'post_status'      => 'published',
				'suppress_filters' => true
			);
			$assignments = get_posts($args);
			?>
			<script>
				jQuery(document).ready(function($) {
					$(".edit_assignment").click(function() {
						window.document.location = "post.php?post=" + $(this).data("href") + "&action=edit";
					});
				});
			</script>
			<table id="active_assignments" class="fancy_table" cellspacing="0" summary="Active assignments">
				<caption></caption>
				<tbody>
				<tr>
					<th scope="col" class="nobg">Assignments</th>
					<th scope="col">Deadline</th>
					<th scope="col">Responses</th>
				</tr>
				<?php
					foreach($assignments as $assignment){
						//find total responses
						$args = array(
							'post_type' => 'post',
							'meta_key' => 'assignment_id',
							'meta_value' => $assignment->ID
						);
						$responses = count(query_posts( $args ));

						//find due date & set color code
						$assignment_date = get_post_meta($assignment->ID, "assignment_date", true);
						$deadline = strtotime($assignment_date);
						$today = strtotime(date('Y-m-d'));

						if($deadline==$today){
							$btn = " btn-danger";
						}else if($deadline<$today){
							//deadline has passed
							$btn = " btn-default";
						}else if($deadline < strtotime('+1 week')){
							$btn = " btn-warning";
						}else{
							//deadline is way in the future
							$btn =" btn-primary";
						}


						//color code due date
						print '<tr class="edit_assignment" data-href="'.$assignment->ID.'">
									<th scope="row" class="spec">'.$assignment->post_title.'</th>
									<td><button class="assign-btn btn btn-xs'.$btn.'">'.$assignment_date.'</button></td>
									<td>'.$responses.'</td>
								</tr>';

						}
				?>

				</tbody></table>
			<?php

		}
	}
	public function latest_media_dashboard_widget(){


		wp_add_dashboard_widget(
			'latest_media_dashboard_widget',         // Widget slug.
			'Latest Media',         // Title.
			'latest_media_dashboard_widget_function' // Display function.
		);
		function latest_media_dashboard_widget_function() {
			?>

			<link href="//cdn.rawgit.com/noelboss/featherlight/1.3.3/release/featherlight.min.css" type="text/css" rel="stylesheet" />
			<script src="//code.jquery.com/jquery-latest.js"></script>
			<script src="//cdn.rawgit.com/noelboss/featherlight/1.3.3/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
			<div id="latest-media-thumbnials">
			<?php
				$args = array(
					'post_type' => 'attachment',
					'numberposts' => -1,
					'post_status' => null,
					'post_parent' => null,
				);
				$media = get_posts($args);

				$i = 0;
				foreach($media as $m){
					if($i<6) {
						print "<div class='show_thumb'>";
						if (strpos($m->post_mime_type, "image") !== false) {
							print '<div class="show_img_thumb"><a href=""  data-featherlight="' . $m->guid . '"><img src="' . $m->guid . '"/></a></div>';
						} else if (strpos($m->post_mime_type, "audio") !== false) {
							print "<div class='show_audio_thumb'>" . do_shortcode("[audio mp3='" . $m->guid . "'][/audio]") . "</div>";
						} else if (strpos($m->post_mime_type, "video") !== false) {
							print "<div class='show_video_thumb'>" . do_shortcode("[video height='145px' width='145px' mp4='" . $m->guid . "'][/video]") . "</div>";
						}
						//find parent post
						if($m->post_parent !=0 ){
							$parent = get_post($m->post_parent);
							print "<div class='show_thumb_caption'><a href='post.php?post=".$parent->ID."&action=edit'>".$parent->post_title."</a></div>";
						}else{
							print "<div class='show_thumb_caption'>No caption</div>";
						}

						$i++;
						print "</div>";
					}
				}
			?>
			</div>

			<div class="media-totals">
					<?php $posts = wp_count_posts();
					?>
					<table class="latest-media-tr">
						<tr>
							<td class="btn btn-default latest-media-btn">
								<a href="<?php print admin_url();?>edit.php" class="latest_media_text"><span class="fui-new img"></span><?php print $posts->publish;?></a>
							</td>
							<td class="btn btn-default latest-media-btn">
								<a href="<?php print admin_url();?>upload.php?post_mime_type=video" class="latest_media_text"><span class="fui-video"></span><?php print media_totals('video');?></a>
							</td>
							<td class="btn btn-default latest-media-btn">
								<a href="<?php print admin_url();?>upload.php?post_mime_type=image" class="latest_media_text"><span class="fui-image"></span><?php print media_totals('video');?></a>
							</td>
							<td class="btn btn-default latest-media-btn">
								<a href="<?php print admin_url();?>upload.php?post_mime_type=audio" class="latest_media_text"><span class="fui-mic"></span><?php print media_totals('video');?></a>
							</td>
						</tr>
					</table>
			</div>

			<?php
			}
			function media_totals($mediaType){

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

			return $attachments;
		}
	}
	//remove unrequired menu items
	function wpse28782_remove_menu_items() {
		remove_menu_page( 'edit.php?post_type=message' );
		remove_menu_page( 'edit.php?post_type=payment' );
	}

	//add assignment
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
					<input type="text" placeholder="Title" id="assignment_title" class="quick_assignment_form">
					<textarea placeholder="Description" id="assignment_description" class="quick_assignment_form"></textarea>

					<h3>Media Types</h3>
					<div class="assigmnet_media_type">
						<input type="checkbox" value="narrative" name="assignment_type[]" class="quick_assignment_form_check">
						<i class="fa fa-list-alt fa-assignment"></i>
						Narrative
					</div>
				<div class="assigmnet_media_type">

					<input type="checkbox" value="image" name="assignment_type[]" checked class="quick_assignment_form_check default_check">
					<i class="fa fa-photo fa-assignment"></i>
					Image
				</div>

				<div class="assigmnet_media_type">

					<input type="checkbox" value="audio" name="assignment_type[]" class="quick_assignment_form_check">
					<i class="fa fa-music fa-assignment"></i>
					Audio
				</div>
				<div class="assigmnet_media_type">

					<input type="checkbox" value="video" name="assignment_type[]" class="quick_assignment_form_check">
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
				<input type="text" id="lat_lon_input" name="lat_lon_input" value="<?php echo $location;?>" class="quick_assignment_form"/>

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
							document.getElementById('loc_address'));

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
				<input id="loc_address" class="quick_assignment_form controls" type="text" name="loc_address" placeholder="Enter a location">

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
					<h3>Who do you want to send the assignment to?</h3>
					<radiogroup id="assignment_target">

					<input type="radio" name="target_radio_type" id="changetype-all" checked="checked" value="all" class="quick_assignment_form_check default_check">
					<label for="changetype-all">Everyone</label>

					<input type="radio" name="target_radio_type" id="changetype-all" value="nearby" class="quick_assignment_form_check">
					<label for="changetype-all">Nearby</label>

					<input type="radio" name="target_radio_type" id="changetype-all" value="specific" class="quick_assignment_form_check">
					<label for="changetype-all">Specific person</label>
						<datalist id="users_list">
						<?php
							//get list of users
							$current_user = get_current_user();
							$blog_users = get_users( 'blog_id=1&orderby=nicename' );
							foreach($blog_users as $user){
								if($user->user_nicename != $current_user->user_nicename)
									print "<option value='".$user->user_nicename."'>";
							}

						?>
						</datalist>
					</radiogroup>
					<input type="text"  list="users_list" id="assignment_target_person" placeholder="Enter name" name="target_person">

				</div>
				<div class="assignment_input_group">
					<h3>Deadline</h3>
					<input type="date" id="assignment_date" name="assignment_date" placeholder="Deadline" class="quick_assignment_form"/>
				</div>
				<div class="assignment_input_group">
					<h3>Bounty</h3>
					<input type="text" id="assignment_bounty" name="bounty"  value="" placeholder="How much do you want to pay for it?" class="quick_assignment_form"/>
				</div>
				<div class="assignment_finish">
					<input id="create_assignment" type="submit" name="save" class="button button-primary" value="Create assignment">
				</div>
					<label id="post_url" href="<?php print plugins_url( '', __FILE__ );?>/post_url.php">
					<div id="assignment_created"></div>
				</div>
				<?php
			}

	}
}





