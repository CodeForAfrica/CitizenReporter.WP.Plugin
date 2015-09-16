<?php
    // Add a custom controller
    add_filter('json_api_controllers', 'add_my_controller');

    function add_my_controller($controllers) {
        // Corresponds to the class JSON_API_MyController_Controller
        $controllers[] = 'MyController';
        return $controllers;
    }
?>