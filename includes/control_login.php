<?php
function control_login($user_login, $user) {
    if ( !in_array( 'administrator', (array) $user->roles ) ) {
        //log them out
        wp_logout();
    }
}
add_action('wp_login', 'control_login', 10, 2);
?>