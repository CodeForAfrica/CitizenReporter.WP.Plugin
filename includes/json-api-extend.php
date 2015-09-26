<?php
// Add a custom controller
add_filter('json_api_controllers', 'add_cr_controller');

function add_cr_controller($controllers) {
    // Corresponds to the class JSON_API_CR_Controller
    $controllers[] = 'CR';
    return $controllers;
}

//set path for custom controller
function set_cr_controller_path() {
    return  plugin_dir_path(  dirname( __FILE__ )  ) . "includes/json-api-gcm-controller.php";
}
add_filter('json_api_cr_controller_path', 'set_cr_controller_path');


?>