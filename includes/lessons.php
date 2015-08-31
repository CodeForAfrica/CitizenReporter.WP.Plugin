<?php

function lesson() {
    $labels = array(
        'name'               => _x( 'Lessons', 'post type general name' ),
        'singular_name'      => _x( 'Lesson', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'lesson' ),
        'add_new_item'       => __( 'Add New Lesson' ),
        'edit_item'          => __( 'Edit Lesson' ),
        'new_item'           => __( 'New Lesson' ),
        'all_items'          => __( 'All Lessons' ),
        'view_item'          => __( 'View Lessons' ),
        'search_items'       => __( 'Search Lessons' ),
        'not_found'          => __( 'No lessons found' ),
        'not_found_in_trash' => __( 'No lessons found in the Trash' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Lessons'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Defines lesson structure',
        'public'        => true,
        'menu_position' => 6,
        'supports'      => array( 'title', 'editor', 'revisions', 'thumbnail'),
        'has_archive'   => true,
    );
    register_post_type( 'lesson', $args );
}
add_action( 'init', 'lesson' );

//Custom Interaction Messages
function lesson_updated_messages( $messages ) {
    global $post, $post_ID;
    $messages['lesson'] = array(
        0 => '',
        1 => sprintf( __('Lesson updated. <a href="%s">View lesson</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Lesson updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Lesson restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Lesson published. <a href="%s">View lesson</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Lesson saved.'),
        8 => sprintf( __('Lesson submitted. <a target="_blank" href="%s">Preview lesson</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Lesson scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview lesson</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Lesson draft updated. <a target="_blank" href="%s">Preview lesson</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'lesson_updated_messages' );

//Contextual Help
function lesson_contextual_help( $contextual_help, $screen_id, $screen ) {
    if ( 'lesson' == $screen->id ) {

        $contextual_help = '<h2>Lessons</h2>
		<p>Lessons show the details of the lessons collected. You can view/edit the details of each lesson by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

    } elseif ( 'edit-lesson' == $screen->id ) {

        $contextual_help = '<h2>Editing lessons</h2>
		<p>This page allows you to view/modify lesson details. Please make sure to fill out the available boxes with the appropriate details.</p>';

    }
    return $contextual_help;
}
add_action( 'contextual_help', 'lesson_contextual_help', 10, 3 );

//TODO: Create actual help content
//Custom Help content
function lessons_help_tab() {

    $screen = get_current_screen();

    // Return early if we're not on the lesson post type.
    if ( 'lesson' != $screen->post_type )
        return;

    // Setup help tab args.
    $args = array(
        'id'      => 'you_custom_id', //unique id for the tab
        'title'   => 'Custom Help', //unique visible title for the tab
        'content' => '<h3>Help Title</h3><p>Help content</p>',  //actual help text
    );

    // Add the help tab.
    $screen->add_help_tab( $args );

}

add_action('admin_head', 'lessons_help_tab');


//xmlrpc stuff
add_filter('xmlrpc_methods', 'lessons_xmlrpc_methods');
function lessons_xmlrpc_methods($methods)
{
    $methods['metaWeblog.getLessons'] = 'mw_getLessons';
    return $methods;
}

function mw_getLessons($args) {
    global $wp_xmlrpc_server;

    $wp_xmlrpc_server->escape($args);

    $blog_ID     = (int) $args[0];
    $username  = $args[1];
    $password   = $args[2];

    if ( isset( $args[3] ) )
        $query = array( 'numberposts' => absint( $args[3] ), 'post_type'=>"lesson");
    else
        $query = array('post_type'=>"lesson");


    // Let's run a check to see if credentials are okay
    if ( !$user = $wp_xmlrpc_server->login($username, $password) ) {
        return $wp_xmlrpc_server->error;
    }

    $posts_list = wp_get_recent_posts($query);

    if ( !$posts_list )
        return array();

    $struct = array();


    foreach ($posts_list as $entry) {
        $post_date = _convert_date( $entry['post_date'] );
        $post_date_gmt = _convert_date_gmt( $entry['post_date_gmt'], $entry['post_date'] );
        $post_modified = _convert_date( $entry['post_modified'] );
        $post_modified_gmt = _convert_date_gmt( $entry['post_modified_gmt'], $entry['post_modified'] );

        $categories = array();
        $catids = wp_get_post_categories($entry['ID']);
        foreach( $catids as $catid )
            $categories[] = get_cat_name($catid);

        $tagnames = array();
        $tags = wp_get_post_tags( $entry['ID'] );
        if ( !empty( $tags ) ) {
            foreach ( $tags as $tag ) {
                $tagnames[] = $tag->name;
            }
            $tagnames = implode( ', ', $tagnames );
        } else {
            $tagnames = '';
        }

        $post = get_extended($entry['post_content']);
        $link = post_permalink($entry['ID']);

        // Get the post author info.
        $author = get_userdata($entry['post_author']);
	$author_email = $author->user_email;

        $allow_comments = ('open' == $entry['comment_status']) ? 1 : 0;
        $allow_pings = ('open' == $entry['ping_status']) ? 1 : 0;

        // Consider future posts as published
        if ( $entry['post_status'] === 'future' )
            $entry['post_status'] = 'publish';

        // Get post format
        $post_format = get_post_format( $entry['ID'] );
        if ( empty( $post_format ) )
            $post_format = 'standard';

        $struct[] = array(

            'dateCreated' => $post_date,
            'userid' => $entry['post_author'],
            'postid' => (string) $entry['ID'],
            'description' => $post['main'],
            'title' => $entry['post_title'],
            'link' => $link,
            'permaLink' => $link,
            // commented out because no other tool seems to use this
            // 'content' => $entry['post_content'],
            'categories' => $categories,
            'mt_excerpt' => $entry['post_excerpt'],
            'mt_text_more' => $post['extended'],
            'wp_more_text' => $post['more_text'],
            'mt_allow_comments' => $allow_comments,
            'mt_allow_pings' => $allow_pings,
            'mt_keywords' => $tagnames,
            'wp_slug' => $entry['post_name'],
            'wp_password' => $entry['post_password'],
            'wp_author_id' => (string) $author->ID,
            'wp_author_display_name' => $author->display_name,
            'date_created_gmt' => $post_date_gmt,
            'post_status' => $entry['post_status'],
            'custom_fields' => $wp_xmlrpc_server->get_custom_fields($entry['ID']),
            'wp_post_format' => $post_format,
            'date_modified' => $post_modified,
            'date_modified_gmt' => $post_modified_gmt,
            'sticky' => ( $entry['post_type'] === 'post' && is_sticky( $entry['ID'] ) ),

        );

	//get user thumbnail
	$avatar = get_gravatar_url( $author_email );

        $entry_index = count( $struct ) - 1;
	//get lesson thumbnail
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($entry['ID']), 'thumbnail' );
	$url = $thumb['0'];
        $struct[ $entry_index ][ 'user_thumbnail' ] = $avatar;
	$struct[ $entry_index ][ 'wp_post_thumbnail' ] = $url; //get_post_thumbnail_id( $entry['ID'] );

    }
    $recent_posts = array();
    for ( $j=0; $j<count($struct); $j++ ) {
        array_push($recent_posts, $struct[$j]);
    }

    return $recent_posts;

}
?>