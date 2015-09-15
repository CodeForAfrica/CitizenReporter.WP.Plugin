<?php
require(realpath(dirname(__FILE__)).'/../../../../wp-blog-header.php');

/*
 * This file is used to create assignment from an Ajax request
 * and returns a result message
 */

if(!is_user_logged_in()){
    print "You are not logged in!";
}else{
    //get author
    $author = get_current_user();

    $post_id = wp_insert_post(
        array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_author' => $author->user_login,
            'post_title' => $_POST['title'],
            'post_content' => $_POST['description'],
            'post_status' => 'published',
            'post_type' => 'message'
        )
    );

    if($post_id !=0){
        //insert meta values
        update_post_meta( $post_id, 'assignment_type', $_POST['types']);
        update_post_meta( $post_id, 'assignment_address', $_POST['address']);
        update_post_meta( $post_id, 'assignment_location', $_POST['lat_lon']);
        update_post_meta( $post_id, 'assignment_target', $_POST['target']);
        update_post_meta( $post_id, 'assignment_target_person', $_POST['target_person']);
        update_post_meta( $post_id, 'assignment_date', $_POST['deadline']);
        update_post_meta( $post_id, 'assignment_bounty', $_POST['bounty']);
        //return success message!
        print "Assignment created successfully!"
        //then send message to targets


    }else{

        print "Problem creating assignment! Do you have the right permissions?";
    }



}
