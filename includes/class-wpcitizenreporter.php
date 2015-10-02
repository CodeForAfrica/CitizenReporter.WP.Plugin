<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/CodeForAfrica/WPCitizenReporter
 * @since      1.0.0
 *
 * @package    WPCitizenReporter
 * @subpackage WPCitizenReporter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WPCitizenReporter
 * @subpackage WPCitizenReporter/includes
 * @author     Nick Hargreaves <nick@codeforafrica.org>
 */
class WPCitizenReporter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WPCitizenReporter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wpcitizenreporter';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WPCitizenReporter_Loader. Orchestrates the hooks of the plugin.
	 * - WPCitizenReporter_i18n. Defines internationalization functionality.
	 * - WPCitizenReporter_Admin. Defines all hooks for the admin area.
	 * - WPCitizenReporter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcitizenreporter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcitizenreporter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpcitizenreporter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpcitizenreporter-public.php';

		/*
		 * This is class is required for preventing memory limit errors realted to the XMLRPC functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpcitizenreporter-xmlrpc.php';

		/*
		 * This file for modification of dashboard look and adding/removing widgets
		 */
		require_once plugin_dir_path( dirname( __FILE__ )  ) . 'includes/class-wpcitizenreporter-dashboard.php';
		/*
		 * For assignments
		 *
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/assignments.php';
		/*
		 * Lessons
		 */
		require_once plugin_dir_path(  dirname( __FILE__ )  ) . 'includes/lessons.php';
		/*
		 * Handling payment
		 */
		require_once plugin_dir_path(  dirname( __FILE__ )  ) . 'includes/payment.php';
		/*
		 * JSON Stuff
		 */

		require_once plugin_dir_path(  dirname( __FILE__ )  ) . 'includes/json-api-extend.php';

		/**
		 * Return only user's posts
		 */

		require_once plugin_dir_path(  dirname( __FILE__ )  ) . 'includes/users_posts_xmlrpc.php';

		$this->loader = new WPCitizenReporter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WPCitizenReporter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WPCitizenReporter_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WPCitizenReporter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$dashboard_plugin = new WPCitizenReporter_Dashboard();
		//create summary widget
		//$this->loader->add_action('admin_notices', $dashboard_plugin, 'summary_widget');
		//remove default widgets
		$this->loader->add_action('admin_init', $dashboard_plugin, 'remove_default_widgets');
		//quick assignment widget
		$this->loader->add_action( 'wp_dashboard_setup', $dashboard_plugin,'add_quick_draft_assignment_dashboard_widget' );
		//latest media widget
		$this->loader->add_action( 'wp_dashboard_setup', $dashboard_plugin,'latest_media_dashboard_widget' );
		//active assignments
		$this->loader->add_action( 'wp_dashboard_setup', $dashboard_plugin,'active_assignments_dashboard_widget' );
		//quick chat widget
		$this->loader->add_action( 'wp_dashboard_setup', $dashboard_plugin,'quick_chat_dashboard_widget' );
		//remove unnecessary menu items
		$this->loader->add_action( 'wp_dashboard_setup', $dashboard_plugin,'wpse28782_remove_menu_items' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WPCitizenReporter_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

			}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WPCitizenReporter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
