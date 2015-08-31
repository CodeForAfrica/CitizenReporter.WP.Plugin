<?php

/**
 * Dynamically increase allowed memory limit for XML-RPC only.
 *
 * @param array $methods
 * @return array
 */
function higher_mem_xmlrpc($methods) {
    ini_set('memory_limit', '256M');
    return $methods;
}
add_action('xmlrpc_methods', 'higher_mem_xmlrpc');

function assignment() {
    $labels = array(
        'name'               => _x( 'Assignments', 'post type general name' ),
        'singular_name'      => _x( 'Assignment', 'post type singular name' ),
        'add_new'            => _x( 'Add New', 'assignment' ),
        'add_new_item'       => __( 'Add New Assignment' ),
        'edit_item'          => __( 'Edit Assignment' ),
        'new_item'           => __( 'New Assignment' ),
        'all_items'          => __( 'All Assignments' ),
        'view_item'          => __( 'View Assignments' ),
        'search_items'       => __( 'Search Assignments' ),
        'not_found'          => __( 'No assignments found' ),
        'not_found_in_trash' => __( 'No assignments found in the Trash' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Assignments'
    );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Defines assignment structure',
        'public'        => true,
        'menu_position' => 6,
        'supports'      => array( 'title', 'editor', 'revisions', 'thumbnail'),
        'has_archive'   => true,
    );
    register_post_type( 'assignment', $args );
}
add_action( 'init', 'assignment' );

//Custom Interaction Messages
function assignment_updated_messages( $messages ) {
    global $post, $post_ID;
    $messages['assignment'] = array(
        0 => '',
        1 => sprintf( __('Assignment updated. <a href="%s">View assignment</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Assignment updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Assignment restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Assignment published. <a href="%s">View assignment</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Assignment saved.'),
        8 => sprintf( __('Assignment submitted. <a target="_blank" href="%s">Preview assignment</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Assignment scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview assignment</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Assignment draft updated. <a target="_blank" href="%s">Preview assignment</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'assignment_updated_messages' );

//Contextual Help
function assignment_contextual_help( $contextual_help, $screen_id, $screen ) {
    if ( 'assignment' == $screen->id ) {

        $contextual_help = '<h2>Assignments</h2>
		<p>Assignments show the details of the assignments collected. You can view/edit the details of each assignment by clicking on its name, or you can perform bulk actions using the dropdown menu and selecting multiple items.</p>';

    } elseif ( 'edit-assignment' == $screen->id ) {

        $contextual_help = '<h2>Editing assignments</h2>
		<p>This page allows you to view/modify assignment details. Please make sure to fill out the available boxes with the appropriate details.</p>';

    }
    return $contextual_help;
}
add_action( 'contextual_help', 'assignment_contextual_help', 10, 3 );

//TODO: Create actual help content
//Custom Help content
function assignments_help_tab() {

    $screen = get_current_screen();

    // Return early if we're not on the assignment post type.
    if ( 'assignment' != $screen->post_type )
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

add_action('admin_head', 'assignments_help_tab');


//Assignment type meta-data
add_action( 'add_meta_boxes', 'assignment_type_box' );
function assignment_type_box() {
    add_meta_box(
        'assignment_type_box',
        __( 'Media type(s) required', 'myplugin_textdomain' ),
        'assignment_type_box_content',
        'assignment',
        'side',
        'high'
    );
}

function assignment_type_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'assignment_type_box_content_nonce' );
    $assignment_type = get_post_meta( get_the_ID(), 'assignment_type', true);
    if(empty($assignment_type)){
        $assignment_type = array();
    }
    ?>
    <input type="checkbox" value="narrative" name="assignment_type[]"<?php if(in_array("narrative", $assignment_type))echo " checked";?>>
    <i class="fa fa-list-alt fa-assignment"></i>
    Narrative
    <br />

    <input type="checkbox" value="image" name="assignment_type[]"<?php if(in_array("image", $assignment_type))echo " checked";?>>
    <i class="fa fa-photo fa-assignment"></i>
    Image
    <br />

    <input type="checkbox" value="audio" name="assignment_type[]"<?php if(in_array("audio", $assignment_type))echo " checked";?>>
    <i class="fa fa-music fa-assignment"></i>
    Audio
    <br />

    <input type="checkbox" value="video" name="assignment_type[]"<?php if(in_array("video", $assignment_type))echo " checked";?>>
    <i class="fa fa-video-camera fa-assignment"></i>
    Video
    <br />


<?php
}

add_action( 'save_post', 'assignment_type_box_save' );

function assignment_type_box_save( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( !wp_verify_nonce( $_POST['assignment_type_box_content_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    $assignment_type= $_POST['assignment_type'];
    update_post_meta( $post_id, 'assignment_type', $assignment_type );

    //send push notification if new assignment
    // if ( !wp_is_post_revision( $post_id ) ){

    $notified = get_post_meta( $post_id, 'notified' );

    if(empty($notified)){
        update_post_meta( $post_id, 'notified', "1" );

        $pushMessage = get_the_title($post_id);

        $reg_ids = users_gcm_ids();
        $deadline = get_post_meta( $post_id, 'assignment_date', true);

        $message = array("assignment" => $pushMessage, "assignmentID"=>$post_id, "assignmentDeadline"=>$deadline);
        send_push_notification($reg_ids, $message);
    }

}



//add location meta data
add_action( 'add_meta_boxes', 'assignment_location_box' );
function assignment_location_box() {
    add_meta_box(
        'assignment_location_box',
        __( 'Assignment Location', 'myplugin_textdomain' ),
        'assignment_location_box_content',
        'assignment',
        'side',
        'high'
    );
}
function assignment_location_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'assignment_location_box_content_nonce' );
    $location = get_post_meta( get_the_ID(), 'assignment_location', true);

    $address = get_post_meta( get_the_ID(), 'assignment_address', true);

    if(empty($location)){
        //nairobi defaults
        $location = "-1.2920659, 36.8219462";
    }else{
        $location = str_replace("(", "", $location);
        $location = str_replace(")", "", $location);
    }
    ?>
    <style>
        #map-canvas {
            height: 250px;
        }
        #lat_lon_input{

            display:none;

        }
        #type-selector{
            display:none;
        }
        .controls {
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #pac-input {
            background-color: #fff;
            padding: 0 11px 0 13px;
            width: 99%;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            text-overflow: ellipsis;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

        .pac-container {
            font-family: Roboto;
        }

        #type-selector {
            color: #fff;
            background-color: #4d90fe;
            padding: 5px 11px 0px 11px;
        }

        #type-selector label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }
        }
        .fa-assignment{
            margin-right:15px;
        }
    </style>
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

<?php
}

add_action( 'save_post', 'assignment_location_box_save' );

function assignment_location_box_save( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( !wp_verify_nonce( $_POST['assignment_location_box_content_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }

    $assignment_location = $_POST['lat_lon_input'];

    $assignment_address = $_POST['loc_address'];

    update_post_meta( $post_id, 'assignment_location', $assignment_location );

    if($assignment_address!=""){
        update_post_meta( $post_id, 'assignment_address', $assignment_address );
    }

}
//show responses meta box
add_action('add_meta_boxes', 'assignment_responses_box');
function assignment_responses_box(){
    add_meta_box(
        'assignment_responses_box',
        __( 'Assignment Responses', 'myplugin_textdomain' ),
        'assignment_responses_box_content',
        'assignment',
        'normal',
        'low'
    );
}
function assignment_responses_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'assignment_responses_box_content_nonce' );
    // args
    $args = array(
        'post_type' => 'post',
        'meta_key' => 'assignment_id',
        'meta_value' => $post->ID
    );

    // get results
    $the_query = new WP_Query( $args );
    //$the_query = query_posts('post_type=post');
    print "<ul>";
    while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
        <li>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </li>
    <?php endwhile;
    print "</ul>";
}
//add end date meta box
add_action( 'add_meta_boxes', 'assignment_date_box' );
function assignment_date_box() {
    add_meta_box(
        'assignment_date_box',
        __( 'Assignment End Date', 'myplugin_textdomain' ),
        'assignment_date_box_content',
        'assignment',
        'side',
        'high'
    );
}
function assignment_date_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'assignment_date_box_content_nonce' );
    $date = get_post_meta( get_the_ID(), 'assignment_date', true);

    echo '<input type="date" id="assignment_date" name="assignment_date"  value="'.$date.'"/>';
    print "<br /> Leave blank if open ended*";
}
add_action( 'save_post', 'assignment_date_box_save' );

function assignment_date_box_save( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( !wp_verify_nonce( $_POST['assignment_date_box_content_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    $assignment_date = $_POST['assignment_date'];
    update_post_meta( $post_id, 'assignment_date', $assignment_date );
}


//add bounty meta data
add_action( 'add_meta_boxes', 'assignment_bounty_box' );
function assignment_bounty_box() {
    add_meta_box(
        'assignment_bounty_box',
        __( 'Assignment Bounty', 'myplugin_textdomain' ),
        'assignment_bounty_box_content',
        'assignment',
        'side',
        'high'
    );
}
function assignment_bounty_box_content( $post ) {
    wp_nonce_field( plugin_basename( __FILE__ ), 'assignment_bounty_box_content_nonce' );
    $bounty = get_post_meta( get_the_ID(), 'assignment_bounty', true);

    if(empty($bounty)){
        $bounty = 'KSH 0';
    }

    echo '<input type="text" id="bounty" name="bounty"  value="'.$bounty.'" placeholder="How much?"/>';
}

add_action( 'save_post', 'assignment_bounty_box_save' );

function assignment_bounty_box_save( $post_id ) {

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    if ( !wp_verify_nonce( $_POST['assignment_bounty_box_content_nonce'], plugin_basename( __FILE__ ) ) )
        return;

    if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    $assignment_bounty = $_POST['bounty'];
    update_post_meta( $post_id, 'assignment_bounty', $assignment_bounty );
}
//xmlrpc stuff
add_filter('xmlrpc_methods', 'my_xmlrpc_methods');
function my_xmlrpc_methods($methods)
{
    $methods['metaWeblog.getRecentAssignments'] = 'mw_getRecentAssignments';
    return $methods;
}

function mw_getRecentAssignments($args) {
    global $wp_xmlrpc_server;

    $wp_xmlrpc_server->escape($args);

    $blog_ID     = (int) $args[0];
    $username  = $args[1];
    $password   = $args[2];

    if ( isset( $args[3] ) )
        $query = array( 'numberposts' => absint( $args[3] ), 'post_type'=>"assignment");
    else
        $query = array('post_type'=>"assignment");


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
	//get assignment thumbnail
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

function _convert_date( $date ) {
    if ( $date === '0000-00-00 00:00:00' ) {
        return new IXR_Date( '00000000T00:00:00Z' );
    }
    return new IXR_Date( mysql2date( 'Ymd\TH:i:s', $date, false ) );
}

function _convert_date_gmt( $date_gmt, $date ) {
    if ( $date !== '0000-00-00 00:00:00' && $date_gmt === '0000-00-00 00:00:00' ) {
        return new IXR_Date( get_gmt_from_date( mysql2date( 'Y-m-d H:i:s', $date, false ), 'Ymd\TH:i:s' ) );
    }
    return _convert_date( $date_gmt );
}
add_filter('xmlrpc_methods', 'my_xmlrpc_methods3');
function my_xmlrpc_methods3($methods){
    $methods['metaWeblog.getPostAttachments'] = 'mw_getPostAttachments';
    return $methods;
}
function mw_getPostAttachments($args){
    global $wp_xmlrpc_server;

    $wp_xmlrpc_server->escape($args);

    $blog_ID     = (int) $args[0];
    $username  = $args[1];
    $password   = $args[2];
    $post_id = $args[3];

    // Let's run a check to see if credentials are okay
    if ( !$user = $wp_xmlrpc_server->login($username, $password) ) {
        return $wp_xmlrpc_server->error;
    }

    $struct = array();


    $images = get_attached_media('image', $post_id);
    $audios = get_attached_media('audio', $post_id);
    $videos = get_attached_media('video', $post_id);


    foreach($images as $image){
        $struct[] = array("file"=>$image['post_title'], "type"=>$image['post_mime_type'], "url"=>$image['guid'], "attachment_id"=>$image['ID']);
    }


    return $struct;
}
add_filter('xmlrpc_methods', 'my_xmlrpc_methods4');
function my_xmlrpc_methods4($methods)
{
    $methods['metaWeblog.getPostObject'] = 'mw_getPostObject';
    return $methods;
}
function mw_getPostObject($args) {
    global $wp_xmlrpc_server;

    $wp_xmlrpc_server->escape($args);

    $attachment_id     = $args[0];
    $username  = $args[1];
    $password   = $args[2];

    // Let's run a check to see if credentials are okay
    if ( !$user = $wp_xmlrpc_server->login($username, $password) ) {
        return $wp_xmlrpc_server->error;
    }

    $item = array();

    $item['id'] = "";
    $item['file'] = "";
    $item['type'] = "";
    $item['url'] = wp_get_attachment_image_src( $attachment_id, "thumbnail", false )[0];

    return $item;
}

add_filter('xmlrpc_methods', 'my_xmlrpc_methods2');
function my_xmlrpc_methods2($methods)
{
    $methods['metaWeblog.getPostsByKeyword'] = 'mw_getPostsByKeyword';
    return $methods;
}
function mw_getPostsByKeyword($args) {
    global $wp_xmlrpc_server;

    $wp_xmlrpc_server->escape($args);

    $blog_ID     = (int) $args[0];
    $username  = $args[1];
    $password   = $args[2];

    if ( isset( $args[3] ) )
        $query = array( 'numberposts' => absint( $args[3] ), 's'=>$args[4]);
    else
        $query = array('post_type'=>"assignment");


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

        $entry_index = count( $struct ) - 1;
        $struct[ $entry_index ][ 'wp_post_thumbnail' ] = get_post_thumbnail_id( $entry['ID'] );

    }


    $recent_posts = array();
    for ( $j=0; $j<count($struct); $j++ ) {
        array_push($recent_posts, $struct[$j]);
    }

    return $recent_posts;

}

function assignmentIcons($post_id){
    $html = "";

    $assignment_type = get_post_meta( $post_id, 'assignment_type', true);

    if(in_array("image", $assignment_type)){
        $html .= '<i class="fa fa-camera"></i>';
    }
    if(in_array("video", $assignment_type)){
        $html .= '<i class="fa fa-video-camera"></i>';
    }
    if(in_array("audio", $assignment_type)){
        $html .= '<i class="fa fa-microphone"></i>';
    }
    if(in_array("narrative", $assignment_type)){
        $html .= '<i class="fa fa-edit"></i>';
    }

    return $html;
}

?>