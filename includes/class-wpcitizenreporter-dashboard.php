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
				<div id="welcome-panel" class="welcome-panel">
					<?php wp_nonce_field( 'welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
					<?php //do_action( 'welcome_panel' ); ?>
					<div class="mjp-welcome-content">
						<div class="welcome-panel-column-container">
							<div class="welcom-panel-container">
								<div class="col-xs-3">
									<div class="tile">
										<h3 class="tile-title">Web Oriented</h3>
										<p>100% convertable to HTML/CSS layout.</p>
										<a class="btn btn-primary btn-large btn-block" href="http://designmodo.com/flat">Get Pro</a>
									</div>
								</div>

								<div class="col-xs-3">
									<div class="tile">
										<h3 class="tile-title">Easy to Customize</h3>
										<p>Vector-based shapes and minimum of layer styles.</p>
										<a class="btn btn-primary btn-large btn-block" href="http://designmodo.com/flat">Get Pro</a>
									</div>
								</div>

								<div class="col-xs-3">
									<div class="tile">
										<h3 class="tile-title">Color Swatches</h3>
										<p>Easy to add or change elements. </p>
										<a class="btn btn-primary btn-large btn-block" href="http://designmodo.com/flat">Get Pro</a>
									</div>
								</div>

								<div class="col-xs-3">
									<div class="tile tile-hot">
										<h3 class="tile-title">Free for Share</h3>
										<p>Your likes, shares and comments helps us.</p>
										<a class="btn btn-primary btn-large btn-block" href="http://designmodo.com/flat">Get Pro</a>
									</div>

								</div>
							</div>
						</div>

					</div>
				</div>

				<div id="dashboard-widgets-wrap">

					<?php wp_dashboard(); ?>

					<div class="clear"></div>
				</div><!-- dashboard-widgets-wrap -->

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

		$attachments = get_posts($args);

		return $attachments;
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





