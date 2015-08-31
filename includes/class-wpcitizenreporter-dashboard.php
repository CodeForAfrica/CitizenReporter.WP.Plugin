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
}





