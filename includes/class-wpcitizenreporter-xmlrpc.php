<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/CodeForAfrica/WPCitizenReporter
 * @since      1.0.0
 *
 * @package    WPCitizenReporter
 * @subpackage WPCitizenReporter/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WPCitizenReporter
 * @subpackage WPCitizenReporter/includes
 * @author     Nick Hargreaves <nick@codeforafrica.org>
 */
class WPCitizenReporter_XMLRPC {

	/**
	 * Short Description.
	 * Increases memory for XMLRPC functions
	 * @since    1.0.0
	 */
	public function __construc(){
		add_action('xmlrpc_methods', 'higher_mem_xlmlrpc');
	}

	public function higher_mem_xlmlrpc($methods){
		init_set('memory_limit', '8M');
		return $methods;
	}

}
