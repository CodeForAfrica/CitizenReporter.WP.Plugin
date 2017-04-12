<?php
/**
 * Created by PhpStorm.
 * User: Ahereza
 * Date: 11/30/16
 * Time: 08:32
 */

/**
 * Dynamically increase allowed memory limit for XML-RPC only.
 *
 * @param array $methods
 * @return array
 */

//xmlrpc stuff



function mw_getRecentPostsUser($args) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape($args);

    $recent_posts = array();

    $username = $args[1];
    $password = $args[2];
    if (isset($args[3]))
        $query = array('numberposts' => absint($args[3]));
    else
        $query = array();


    if ( !$user = $wp_xmlrpc_server->login($username, $password) ) {
        return $wp_xmlrpc_server->error;
    }

    if (!current_user_can('edit_posts'))
        return new IXR_Error(401, __('Sorry, you cannot edit posts on this site.'));

    // This action is documented in wp-includes/class-wp-xmlrpc-server.php
    do_action( 'xmlrpc_call', 'metaWeblog.getRecentPostsUser' );

    $posts_list = wp_get_recent_posts($query);


    if (!$posts_list)
        return array();

    foreach ($posts_list as $entry) {
        if (!current_user_can('edit_post', $entry['ID']))
            continue;

        $post_date = _convert_date($entry['post_date']);
        $post_date_gmt = _convert_date_gmt($entry['post_date_gmt'], $entry['post_date']);
        $post_modified = _convert_date($entry['post_modified']);
        $post_modified_gmt = _convert_date_gmt($entry['post_modified_gmt'], $entry['post_modified']);

        $categories = array();
        $catids = wp_get_post_categories($entry['ID']);
        foreach ($catids as $catid)
            $categories[] = get_cat_name($catid);

        $tagnames = array();
        $tags = wp_get_post_tags($entry['ID']);
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $tagnames[] = $tag->name;
            }
            $tagnames = implode(', ', $tagnames);
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
        if ($entry['post_status'] === 'future')
            $entry['post_status'] = 'publish';

        // Get post format
        $post_format = get_post_format($entry['ID']);
        if (empty($post_format))
            $post_format = 'standard';

        // $user = get_user_by('login', $username);
        // $user_ID = $user->ID;

        $user = wp_get_current_user();
        $user_ID = $user->ID;


        if ($user_ID == $entry['post_author']) {

            $recent_posts[] = array(
                'dateCreated' => $post_date,
                'userid' => $entry['post_author'],
                'postid' => (string)$entry['ID'],
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
                'wp_author_id' => (string)$author->ID,
                'wp_author_display_name' => $author->display_name,
                'date_created_gmt' => $post_date_gmt,
                'post_status' => $entry['post_status'],
                'custom_fields' => $wp_xmlrpc_server->get_custom_fields($entry['ID']),
                'wp_post_format' => $post_format,
                'date_modified' => $post_modified,
                'date_modified_gmt' => $post_modified_gmt,
                'sticky' => ($entry['post_type'] === 'post' && is_sticky($entry['ID'])),
                'wp_post_thumbnail' => get_post_thumbnail_id($entry['ID'])
            );
        }
    }

    return $recent_posts;
}

function my_xmlrpc_methods19($methods)
{
    $methods['metaWeblog.getRecentPostsUser'] = 'mw_getRecentPostsUser';
    return $methods;
}

add_filter('xmlrpc_methods', 'my_xmlrpc_methods19');