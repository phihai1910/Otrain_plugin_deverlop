<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       check_ed_license
 * @since      1.0.0
 *
 * @package    Check_ed_license
 * @subpackage Check_ed_license/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Check_ed_license
 * @subpackage Check_ed_license/public
 * @author     hai <nguyenphihai1910@gmail.com>
 */
class Check_ed_license_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Check_ed_license_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Check_ed_license_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/check_ed_license-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Check_ed_license_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Check_ed_license_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/check_ed_license-public.js', array( 'jquery' ), $this->version, false );

	}
	
	public function check_data(){
		
		// $license = 'eee2';
		// $plugin_name ='Bulk Purchase';
		
		$license = 'aaf';
		$plugin_name ='Bulk Purchase';
		
		// $license = 'a46';
		// $plugin_name ='WooCommerce Integration';
		$store_url = 'http://edwiser.org/check-update';
		
		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,
			'item_name' => urlencode($plugin_name),
		);
		
		$response = wp_remote_get(add_query_arg($api_params, $store_url), array(
			'timeout' => 15,
			'sslverify' => false,
		));
		var_dump($response);
		exit();
		
		
	}

}
