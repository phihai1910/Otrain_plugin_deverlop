<?php
define( 'TML_DIR', dirname(__FILE__) );

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       otrain.com.au
 * @since      1.0.0
 *
 * @package    Ot_woo_report
 * @subpackage Ot_woo_report/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ot_woo_report
 * @subpackage Ot_woo_report/admin
 * @author     Hai Phi <tech@otrain.net>
 */
// include 'fun-general.php';
include 'Classes/PHPExcel.php';
class Ot_woo_report_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	 public $page;
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ot_woo_report_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ot_woo_report_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ot_woo_report-admin.css', array(), $this->version, 'all' );

	}
	public function add_menu_link(){
		$this->page = add_submenu_page(
			'woocommerce',
			__( 'Custom CSV  Export', 'woocommerce-customer-order-csv-export' ),
			__( 'Custom', 'woocommerce-customer-order-csv-export' ),
			'manage_woocommerce',
			'custom_report',
			array( $this, 'render_submenu_pages' )
		);
	}
	public function get_export_options(){
		global $wpdb;
		
		$all_product_data = $wpdb->get_results("SELECT ID,post_title,post_content,post_author,post_date_gmt FROM `" . $wpdb->prefix . "posts` where post_type='product' and post_status = 'publish'");
		$product = array();
	
		if( $all_product_data ){
			
			foreach($all_product_data as $p){
				
				$product[$p->ID] = $p->post_title;
			}
		}
		
		$order_statuses     = wc_get_order_statuses();
		
		// $product = 
		$options = array(

			'export_section_title' => array(
				'name' => __( 'Otrain Custom Export', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			),


			'export_section_end' => array( 'type' => 'sectionend' ),

			'export_options_section_title' => array(
				'name' => __( 'Export Options', 'woocommerce-customer-order-csv-export' ),
				'type' => 'title',
			),

			'statuses' => array(
				'id'                => 'statuses',
				'name'              => __( 'Order Statuses', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with these statuses will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'multiselect',
				'options'           => $order_statuses,
				// 'default'           => 'wc-completed',
				'class'             => 'wc-enhanced-select show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => array(
					'data-placeholder' => __( 'Leave blank to export orders with any status.', 'woocommerce-customer-order-csv-export' ),
				),
			),


			'products' => array(
				'id'                => 'products',
				'name'              => __( 'Products', 'woocommerce-customer-order-csv-export' ),
				'desc_tip'          => __( 'Orders with these products will be included in the export.', 'woocommerce-customer-order-csv-export' ),
				'type'              => 'multiselect',
				'default'           => '',
				'options'           => $product,
				'class'             => 'wc-product-search show_if_orders',
				'css'               => 'min-width: 250px',
				'custom_attributes' => array(
					'data-multiple'    => 'true',
					'data-action'      => 'woocommerce_json_search_products_and_variations',
					'data-placeholder' => __( 'Leave blank to export orders with any products.', 'woocommerce-customer-order-csv-export' ),
				),
			),

			'start_date' => array(
				'id'   => 'start_date',
				'name' => __( 'Start Date', 'woocommerce-customer-order-csv-export' ),
				'desc' => __( 'Start date of customers or orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', 'woocommerce-customer-order-csv-export' ),
				'type' => 'text',
			),

			'end_date' => array(
				'id'   => 'end_date',
				'name' => __( 'End Date', 'woocommerce-customer-order-csv-export' ),
				'desc' => __( 'End date of customers or orders to include in the exported file, in the format <code>YYYY-MM-DD.</code>', 'woocommerce-customer-order-csv-export' ),
				'type' => 'text',
			),

			'export_options_section_end' => array( 'type' => 'sectionend' ),

		);
		return apply_filters( 'wc_customer_order_csv_export_options', $options );
	}
	public function get_coupon($order){
		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon ) {

			$_coupon     = new WC_Coupon( $coupon['name'] );
			$coupon_post = get_post( $_coupon );

			$coupon_item = array(
				'id'          => $coupon_item_id,
				'code'        => $coupon['name'],
				'amount'      => wc_format_decimal( $coupon['discount_amount'], 2 ),
				'description' => is_object( $coupon_post ) ? $coupon_post->post_excerpt : '',
			);

			/**
			 * CSV Order Export Coupon Line Item.
			 *
			 * Filter the individual coupon line item entry
			 *
			 * @since 4.0.0
			 * @param array $coupon_item {
			 *     line item data in key => value format
			 *     the keys are for convenience and not necessarily used for exporting. Make
			 *     sure to prefix the values with the desired refund line item entry name
			 * }
			 *
			 * @param array $coupon WC order coupon item
			 * @param WC_Order $order the order
			 * @param \WC_Customer_Order_CSV_Export_Generator $this, generator instance
			 */
			$coupon_item = apply_filters( 'wc_customer_order_csv_export_order_coupon_item', $coupon_item, $coupon, $order, $this );

			$coupon_items[] = $coupon_item ;
		}
		return $coupon_items;
	}
	public function check_export(){
		// security check
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) ) {

			// wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-customer-order-csv-export' ) );
		}
		else
		{
			
			$status = isset($_POST['statuses'])?$_POST['statuses']: 'any';
			$products = isset($_POST['products'])?$_POST['products']:array();
			$start = isset($_POST['start_date'])?$_POST['start_date']:date( 'Y-m-d 00:00', 0 );
			$end = isset($_POST['end_date'])?$_POST['end_date']. ' 23:59:59.99':date( 'Y-m-d 23:59', current_time( 'timestamp' ) );
			$args = array(
				'post_type'      => 'shop_order',
				'post_status'    => ($status),
				'posts_per_page' => -1,
				'date_query'  => array(
					array(
						'before'    => $end,
						'after'     => $start,
						'inclusive' => true,
					),
				),
			);
			$query = new WP_Query($args);
			
			$row = array();
			
			if( $query->have_posts() ){
				while ($query->have_posts() ){
					$query->the_post();
					$order = new WC_Order(get_the_ID() );
					$order_item = $order->get_items();
					
					$coupon = $this->get_coupon($order);
					
					if( $coupon){
						$coupon = $coupon[0]['code'].' '.$coupon[0]['amount']. get_woocommerce_currency();
					}
			
					$customer = get_post_meta('customer');
					foreach($order_item as $p){
						
						$product_id = $p->get_product_id();
						
						if( $products){
							if( in_array($product_id,$products ) ){
								$email = $order->get_billing_email();
								$row[] = array(
									$order->get_billing_first_name(),
									$order->get_billing_last_name(),
									$order->get_billing_company(),
									$order->get_billing_address_1().' '. $order->get_billing_address_2(),
									$order->get_billing_city(),
									$order->get_billing_state(),
									$order->get_billing_postcode(),
									$order->get_billing_phone(),
									$email,
									$p->get_quantity(),
									$p->get_subtotal(),
									$p->get_total(),
									$coupon,
									null,
									$order->order_date
								);
							}
						}
						else
						{
							$email = $order->get_billing_email();
							$row[] = array(
								$order->get_billing_first_name(),
								$order->get_billing_last_name(),
								$order->get_billing_company(),
								$order->get_billing_address_1().' '. $order->get_billing_address_2(),
								$order->get_billing_city(),
								$order->get_billing_state(),
								$order->get_billing_postcode(),
								$order->get_billing_phone(),
								$email,
								$p->get_quantity(),
								$p->get_subtotal(),
								$p->get_total(),
								$coupon,
								'',
								$order->order_date
							);
						}
						
					}
					
				}
			}
			$this->create_excel($row);

			
		}
	}
	public function create_excel($data){
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Otrain");
		
		$header = array(
				'A' 			=> 'first_name',
				'B'			=> 'last_name',
				'C'			=> 'company'	,
				'D'			=> 'address',	 
				'E'			=> 'suburb'	,
				'F'			=> 'state'	 ,
				'G'			=> 'postcode'	,
				'H'			=> 'phone'		,
				'I'			=> 'email'		 ,
				'J'			=> 'quantity',
				'K'			=> 'price',
				'L'			=> 'price after coupon'	,
				'M'			=> 'coupon' 	,
				'N'			=> 'receipt_number',
				'O'			=> 'date_created'
		
		);
		$key = array_keys($header);
		$value = array_values($header);
		
		$objPHPExcel  = $this->set_excel_header($objPHPExcel,$header);
		$objPHPExcel  = $this->set_excel_content($objPHPExcel,$data,$header);
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$name = 'Order export date '.date( 'Y-m-d', current_time( 'timestamp' )); 
		$objWriter->save(TML_DIR.'/tmp/'.$name.'.xlsx');
		echo '<h1 style="color:#92bb00;">Download excel export file <a href="'.plugin_dir_url( __FILE__ ).'/tmp/'.$name.'.xlsx">here</a></h1>';
		// Create new PHPExcel object
			
	}
	
	public function set_excel_content($objPHPExcel,$content,$header){
		$col = array_keys($header);
		foreach($content as $key => $value){
			$number = $key + 2;
			foreach($col as $k=>$c){
				
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($c.$number, $value[$k]);
			}
			
		}
		
		return $objPHPExcel;
	}
	public function set_excel_header($objPHPExcel, $headers){
		foreach($headers as $key => $value){
			$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($key.'1', $value);
					
		}
		
		return $objPHPExcel;
	}
	public function render_submenu_pages(){
		// permissions check
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$this->check_export();
		?>
		<div class="wrap woocommerce">
		<form method="post" id="mainform" action="" enctype="multipart/form-data">
			<?php
		// show export form
		woocommerce_admin_fields( $this->get_export_options() );

		wp_nonce_field( __FILE__ );
		submit_button( __( 'Export', 'woocommerce-customer-order-csv-export' ) );
		?>
		
		</form>
		
		<?php
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {
		
		if($this->page === $hook_suffix){
			// jQuery Timepicker JS
			
				wp_enqueue_script( 'wc-custom-timepicker', plugin_dir_url( __FILE__ ).'/assets/js/jquery-timepicker/jquery.timepicker.min.js' );

				// datepicker
				wp_enqueue_script( 'jquery-ui-datepicker' );

				// sortable
				wp_enqueue_script( 'jquery-ui-sortable' );
				
				
				
				$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
				wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
			
			
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ot_woo_report-admin.js', array( 'jquery','wc-custom-timepicker','jquery-ui-datepicker' ), $this->version, true );
			// admin JS
			
			
		}
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ot_woo_report_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ot_woo_report_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		
		
		

	}

}
