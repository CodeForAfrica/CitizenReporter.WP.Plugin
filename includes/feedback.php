<?php

/*
 * Create Feedback Content Type
 */

function register_feedback() {
    $labels = array(
        'name'               => _x( 'Feedback', 'post type general name' ),
        'singular_name'      => _x( 'Feedback', 'post type singular name' ),
        'add_new'            => _x( 'Compose New', 'feedback' ),
        'add_new_item'       => __( 'Compose New Feedback' ),
        'new_item'           => __( 'New Feedback' ),
        'all_items'          => __( 'All Feedback' ),
        'view_item'          => __( 'View Feedback' ),
        'search_items'       => __( 'Search Feedbacks' ),
        'not_found'          => __( 'No feedback found' ),
        'not_found_in_trash' => __( 'No feedback found in the Trash' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Feedback'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Defines feedback structure',
        'public'        => true,
        'menu_position' => 6,
        'supports'      => array( 'title', 'custom-fields'),
        'has_archive'   => false,
    );
    register_post_type( 'feedback', $args );
}

add_action( 'init', 'register_feedback' );

?>