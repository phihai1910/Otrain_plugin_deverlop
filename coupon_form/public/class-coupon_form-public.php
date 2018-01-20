<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       otrain.com.au
 * @since      1.0.0
 *
 * @package    Coupon_form
 * @subpackage Coupon_form/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Coupon_form
 * @subpackage Coupon_form/public
 * @author     Hai <tech@otrain.net>
 */
class Coupon_form_Public {

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
	public function ot_coupon_form($atts,$content){
		if( !current_user_can('administrator') ){
			
			return ;
		}
		
		wp_enqueue_style('select2');
		wp_enqueue_script('ocp_fancybox');
		wp_enqueue_script('ocp_form');
		$args = array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'product',
			
		);
		$list_product = get_posts($args);
		
		?>
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">
		<form role="form" class=" ocp_form" id="ocp_form" action="" method="POST">
		   <div class="form-group">
			<label class="" for="ocp_product">Select course</label>
			<select class="form-control" required id="ocp_product" name="ocp_product" multiple="multiple">
				<?php 
					if( !empty($list_product) ){
						foreach($list_product as $product){
								echo '<option value="'. $product->ID .'">'.$product->post_title.'</option>';
						}
					}
				?>
			</select>
		  </div>
		  <div class="row">
		  <div class="form-group col-md-4">
			<label class="" for="ocp_price">Value</label>
			<select id="ocp_price" name="ocp_price"  class="form-control">
				<option value="10" >10</option>
				<option value="20" >20</option>
				<option value="30" >30</option>
				<option value="40" >40</option>
				<option value="50" >50</option>
				<option value="60" >60</option>
				<option value="70" >70</option>
				<option value="80" >80</option>
				<option value="90" >90</option>
				<option value="100" selected="selected" >100</option>
			</select>
		  </div>
			<div class="form-group col-md-4">
			<label class="" for="ocp_type">Type</label>
			<select name="ocp_type" class="form-control ocp_type" id="ocp_type">
				<option value="percent_product">% reduce</option>
				<option value="fixed_product" > $ reduce</option>
			</select>
			
		  </div>
		  <div class="form-group col-md-4">
			<label class="" for="ocp_number">Number of uses</label>
			<select name="ocp_number" id="ocp_number" required class="form-control" >
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
			</select>
			
		  </div>
		  </div>
		  <?php wp_nonce_field('otrain_hai_form','otrainform'); ?>
		  <button type="submit" class="btn btn-default">Create</button>
		</form>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
		<script>
		var form = jQuery('#ocp_form');
		var admin_url = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
		
			form.on('submit',function(e){
				e.preventDefault();
				ot_ajax= {
					'action'	: 'ocp_create_coupon',
					'product'	: jQuery('#ocp_product').val(),
					'price'		: jQuery('#ocp_price').val(),
					'number'	: jQuery('#ocp_number').val(),
					'type'		: jQuery('#ocp_type').val(),
					'otrainform': jQuery('#otrainform').val()
				}
				form.find("button[type=submit]").attr('disabled','disabled');
				jQuery.post( admin_url, ot_ajax, function(response) {
					
					jQuery.fancybox.open(response);
					
					form.find("button[type=submit]").removeAttr('disabled');
					
					var userform = jQuery('#ocp_formuser');
					userform.on('submit',function(e){
						e.preventDefault();
						userform.find("button[type=submit]").attr('disabled','disabled');
						ot_ajaxuser = {
							'action'	: 'ocp_user_send_coupon',
							'code'		: jQuery('#ocp_code').val(),
							'code_id'	: jQuery('#ocp_code_id').val(),
							'price'		: jQuery('#ocp_price_discount').val(),
							'user_firstname'		: jQuery('#ocp_user_firstname').val(),
							'user_lastname'			: jQuery('#ocp_user_lastname').val(),
							'email'		: jQuery('#ocp_email').val(),
							'product'	: jQuery('#ocp_product_discount').val(),
							'number'	: jQuery('#ocp_number').val(),
							'otrainuser': jQuery('#otrainuser').val()
						}
						
						jQuery.fancybox.close();
						jQuery.post( admin_url, ot_ajaxuser, function(response) {
							jQuery.fancybox.open(response);
						});
						
						
					});
					
				});
				
			});
		
		</script>
		<?
		
		
	}
	public function ocp_user_send_coupon(){
		if(!current_user_can('administrator') ||  ! wp_verify_nonce( $_POST['otrainuser'], 'otrain_hai_form_user' ) ){ 
			echo 'wrong hole';
			die();
		}
		$code 		= $_POST['code'];
		$code_id 	= $_POST['code_id'];
		$user_firstname 		= $_POST['user_firstname'];
		$user_lastname 			= $_POST['user_lastname'];
		$email 		= $_POST['email'];
		$price 		= $_POST['price'];
		$product 	= $_POST['product'];
		$products = explode(',',$product);
		$number 	= $_POST['number'];
		$title = '';
		foreach( $products as $product_id ){
			 $title .= get_the_title( $product_id ). '<br />' ;
			 
		}
		update_post_meta($code_id, 'customer_email', $email);
		
		$msg = '<div style="background-color: #efefef; width: 100%; -webkit-text-size-adjust: none !important; margin: 0; padding: 70px 70px 70px 70px;">
<table id="template_container" style="padding-bottom: 20px; box-shadow: 0 0 0 3px rgba(0,0,0,0.025) !important; border-radius: 6px !important; background-color: #dfdfdf;" border="0" width="600" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td style="background-color: #465c94; border-top-left-radius: 6px !important; border-top-right-radius: 6px !important; border-bottom: 0; font-family: Arial; font-weight: bold; line-height: 100%; vertical-align: middle;">
<h1 style="color: white; margin: 0; padding: 28px 24px; text-shadow: 0 1px 0 0; display: block; font-family: Arial; font-size: 30px; font-weight: bold; text-align: left; line-height: 150%;">Your Coupon Code</h1>
</td>
</tr>
<tr>
<td style="padding: 20px; background-color: #dfdfdf; border-radius: 6px !important;" align="center" valign="top">
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">Hello ' . $user_firstname . '</div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">We have created a discount Coupon Code <strong>' . $code . '</strong> for you to use when purchasing enrolments in the following course:</div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">' . $title . '</div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">Please visit <a href="'. home_url('/') .'">' . home_url('/') . '</a> to purchase the above course, at checkout apply the Coupon Code to receive the agreed discount.</div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;"></div>
<div style="font-family: Arial; font-size: 14px; line-height: 150%; text-align: left;">This Coupon Code can only be used for '. $number .' purchase.</div></td>
</tr>
<tr>
<td style="text-align: center; border-top: 0; -webkit-border-radius: 6px;" align="center" valign="top"><span style="font-family: Arial; font-size: 12px;">' . get_bloginfo('name') . '</span></td>
</tr>
</tbody>
</table>
</div>';
		
		
		/*
		$msg = '<p>Hello ' . $user_firstname . '</p>';
		$msg .= '<p>We have created a discount Coupon Code ' . $code . ' for you to use when purchasing enrolments in the following course:</p>
		' . $title . '
		<p>Please visit ' . home_url('/') . ' to purchase the above course, at checkout apply the Coupon Code to receive the agreed discount.</p>
		<p>This Coupon Code can only be used for [number] purchase.</p>
		<p>Epilepsy Foundation </p>'
		;
		*/
		
		
		
		add_filter( 'wp_mail_content_type', function( $content_type ) {
			return 'text/html';
		});
		$sendmail = wp_mail($email, 'Your coupon code', $msg);
		if($sendmail ){
			echo '<h3>Send Email success</h3>';
		}else{
			echo '<h3>Send Email false </h3>';
		}
		die();
		
	}
	
	public function ocp_create_coupon(){
		
		if(!current_user_can('administrator') ||  ! wp_verify_nonce( $_POST['otrainform'], 'otrain_hai_form' ) ){ 
			echo 'wrong hole';
			die();
		}
		
		$product 	= $_POST['product'];
		$price 		= $_POST['price'];
		$number 	= $_POST['number'];
		$type 		= $_POST['type'];
		$random = rand ( 100000,99999999);
		$code = md5($random);
		$code = substr($code,0,8);
		
		
		$coupon_code = $code; // Code
		$amount = $price; // Amount
		$discount_type = $type; // Type: fixed_cart, percent, fixed_product, percent_product
							
		$coupon = array(
			'post_title' => $coupon_code,
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type'		=> 'shop_coupon'
		);
							
		$new_coupon_id = wp_insert_post( $coupon );
						
		// Add meta
		update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
		update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
		update_post_meta( $new_coupon_id, 'individual_use', 'no' );
		update_post_meta( $new_coupon_id, 'product_ids', implode(",", $product) );
		update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
		update_post_meta( $new_coupon_id, 'usage_limit', '' );
		update_post_meta( $new_coupon_id, 'expiry_date', '' );
		update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
		update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
		$this->send_coupon_form($new_coupon_id,$code,$price,$product,$discount_type);
		die();
	}
	public function send_coupon_form($new_coupon_id,$code,$price,$products,$discount_type){
		
		$title = '';
		
		foreach( $products as $product_id ){
			 $title .= '<br />' . get_the_title( $product_id ) ;
			 
		}
		if( $discount_type == 'percent_product'){
			$discount_type = '%';
		}
		else{
			$discount_type = '$';
		}
		?>
		<form role="form" class="ocp_formuser" id="ocp_formuser" action="" method="POST">
		   <div class="form-group">
			<h1>Coupon code: <?php echo $code; ?></h1>
			<input type="hidden" name="ocp_code" id="ocp_code" value="<?php echo $code ?>" >
			<input type="hidden" name="ocp_code_id" id="ocp_code_id" value="<?php echo $new_coupon_id ?>" >
			<h3>Price discount: <?php echo $price ?><?php echo $discount_type ?></h3>
			<input type="hidden" name="ocp_price_discount" id="ocp_price_discount" value="<?php echo $price.$discount_type; ?>"> 
			<h3>Product apply: <?php echo $title; ?></h3>
			<input type="hidden" name="ocp_product_discount" id="ocp_product_discount" value="<?php echo  implode(',',$products); ?>" >
			<p>Please input name and email below to send email with coupon code to customer</p>
		  </div>
		  <div class="row">
			  <div class="form-group col-md-6">
				<label class="" for="ocp_user_firstname" > First Name</label>
				<input type="text" class="form-control" required  id="ocp_user_firstname" name="ocp_user_firstname" placeholder="">
				
			  </div>
			  <div class="form-group col-md-6">
				<label class="" for="ocp_user_lastname" > Last Name</label>
				<input type="text" class="form-control" required  id="ocp_user_lastname" name="ocp_user_lastname" placeholder="">
				
			  </div>
		  </div>
		  <div class="form-group">
			<label class="" for="ocp_email">Email</label>
			<input type="email" class="form-control" required  id="ocp_email" name="ocp_email" placeholder="">
		  </div>
		  <div class="form-group">
			<label class="" for="ocp_email_confirm">Confirm Email</label>
			<input type="email" class="form-control" required  id="ocp_email_confirm" name="ocp_email_confirm" placeholder="" onblur="confirmEmail()">
		  </div>
		  <?php wp_nonce_field('otrain_hai_form_user','otrainuser'); ?>
		  <button type="submit" class="btn btn-default">Send</button>
		</form>
		<script>
		function confirmEmail() {
			var email = document.getElementById("ocp_email").value
			var confemail = document.getElementById("ocp_email_confirm").value
			if(email != confemail) {
				alert('Email Not Matching!');
			}
		}
		</script>
		<?php
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
		 * defined in Coupon_form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coupon_form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'ocp_fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coupon_form-public.css', array(), $this->version, 'all' );

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
		 * defined in Coupon_form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coupon_form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_register_script( 'ocp_fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js', array( 'jquery' ), $this->version, false );
		
		wp_register_script( 'ocp_form', plugin_dir_url( __FILE__ ) . 'js/coupon_form-public.js', array( 'jquery','select2' ), $this->version, false );
		

	}

}
