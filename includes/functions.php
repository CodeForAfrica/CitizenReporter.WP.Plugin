<?php
/**
 * Created by PhpStorm.
 * User: Ahereza
 * Date: 5/9/17
 * Time: 10:46
 */

add_action('init', 'unpublishedRSS');
function unpublishedRSS(){
    add_feed('unpublished', 'unpublishedRSS_render');
}

function unpublishedRSS_render(){
    get_template_part('rss', 'unpublished');
}