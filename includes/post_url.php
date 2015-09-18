<?php
require(realpath(dirname(__FILE__)).'/../../../../wp-blog-header.php');

/*
 * This file is used to create assignment from an Ajax request
 * and returns a result message
 */

if(!is_user_logged_in()){
    print -1;
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
            'post_type' => 'assignment'
        )
    );

    if($post_id !=0){
        //insert meta values
        update_post_meta( $post_id, 'assignment_type', $_POST['types']);
        update_post_meta( $post_id, 'assignment_address', $_POST['address']);
        update_post_meta( $post_id, 'assignment_location', $_POST['lat_lon']);
        update_post_meta( $post_id, 'assignment_target', $_POST['target']);
        if($_POST['target']=="specific")
           update_post_meta( $post_id, 'assignment_target_person', $_POST['target_person']);
        update_post_meta( $post_id, 'assignment_date', $_POST['deadline']);
        update_post_meta( $post_id, 'assignment_bounty', $_POST['bounty']);
        //return success message!
        print 1;
        //send message to targets if any

        /**
         * if target is specific, send only to target_person
         * if target is everyone, send to everyone
         * if target is nearby, look for nearest users(or users in same town)
         */

        if($_POST['target'] == "everyone"){
            $reg_ids = users_gcm_ids();
            assignment_send_push($_POST['title'], $post_id, $_POST['deadline'], $reg_ids);
        }

    }else{

        print 0;
    }



}
