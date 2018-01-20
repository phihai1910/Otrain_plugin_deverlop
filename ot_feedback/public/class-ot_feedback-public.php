<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       otrain.com.au
 * @since      1.0.0
 *
 * @package    Ot_feedback
 * @subpackage Ot_feedback/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ot_feedback
 * @subpackage Ot_feedback/public
 * @author     PhiHai <nguyenphihai1910@gmail.com>
 */
class Ot_feedback_Public {

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
	private $table;

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
		$this->table = 'wp_scoreboard';
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
		 * defined in Ot_feedback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ot_feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ot_feedback-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.css', array(), $this->version, 'all' );

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
		 * defined in Ot_feedback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ot_feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( 'fancybox', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.2.5/jquery.fancybox.min.js', array( 'jquery' ), $this->version, false );
		
		wp_enqueue_script('fancybox');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ot_feedback-public.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'ot',
            array( 'ajaxurl' => admin_url( 'admin-ajax.php' )) );

	}
	public function ot_wp_footer(){
		if( isset ( $_GET['F0hDxrEqK|4z822t']) ){
			update_option('ot_feedback',true); 
		} 
		
		?>
		<div class="ot-feedback">How was your experience today? 
		<a href="#" ><img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/happy.png' ?>" data-scorce="1" title="Good" /></a>
		<a href="#"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/normal-2.png' ?>" data-scorce="0" title="Ok" /></a>
		<a href="#"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/sad.png' ?>" data-scorce="-1" title="Not Good" /></a>
		</div>
		<?php
		
	}
	
	public function ot_ajax(){
		ob_start();
		$status = (int)$_POST['status'];
		$user = wp_get_current_user();
		
		if( $user->ID !== 0){
			$name = $user->first_name . ' ' .$user->last_name;
			$email = $user->user_email;
			$username = $user->user_login;
			
		}
		else
		{
			$name = 'GUEST';
			$email = '';
			$username = 'GUEST';
			
		}	
		$ip = $_SERVER['REMOTE_ADDR'];
		$brown =  $_SERVER['HTTP_USER_AGENT'];
		
		$exit = false;
		echo '<div>';
		switch( $status ){
			case 0:
			
			case 1: {
				echo '<p>Thank you for your feedback.</p>
					  <p>This window will close shortly.</p>';
					
				$this->insert_feedback($username,$status,$ip,$email,'','', $brown);
				
				$exit = true;
			}
			break;
			default :
				?>
			<span class="Arial">We're sorry your experience wasn't good.
			Would you like to tell us about it?</span>	
			<form name="form1" id="ob_form" action="#">
			<input type="hidden" name="n" class="n" value="<?php echo $name ?>">
			<input type="hidden" name="s" class="s" value="<?php echo $status ?>">
			<input type="hidden" name="i" class="i" value="<?php echo $ip ?>">
			<input type="hidden" name="a" class="a" value="<?php echo $brown ?>">
			<input type="hidden" name="e" class="e" value="<?php echo $email ?>">
			<input type="hidden" name="u" class="u" value="<?php echo $username ?>">
			<textarea name="c" class="c" cols="30"></textarea><br><input type="submit" value="Send Feedback">
			<input type="reset" name="reset" value="No Thanks, I've said enough"></form>	
			
			<?php 
		}
		echo '</div>';
		$html = ob_get_clean();
		$return = array(
			'html' 		=> $html,
			'exit'		=> $exit
		);
		wp_send_json($return);
		die();
	}
	public function insert_feedback($user,$score,$ip,$email,$url,$comment,$browse){
		global $wpdb;
		$wpdb->insert(
			$this->table,
			array(
				'user' 		=> $user,
				'score' 	=> $score,
				'ip'		=> $ip,
				'email'		=> $email,
				'url'		=> $url,
				'comments'	=> $comment,
				'date'		=> current_time( 'mysql' ),
				'agent'		=> $browse,
			)
		);
	}
	public function ot_ajax_submit(){
		global $wpdb;
		
		$name = $_POST['n'];
		$email = $_POST['e'];
		$username = $_POST['u'];
		
		$ip = $_POST['i'];
		$brown =  $_POST['a'];
		$status = $_POST['s'];
		$comment = $_POST['c'];
		// $this->table = 'wp_scoreboard';
		$this->insert_feedback($name,$status,$ip,$email,'',$comment, $brown);
		
		add_filter( 'wp_mail_content_type', function( $content_type ) {
			return 'text/html';
		});
		
		if( $status == 0){
			$subject = 'Average User Experience Feedback';
			$msg = '<p>Hi admin,</p>
			<p>One of your students reported an average score from his/her user experience on ' . home_url('/') . '</p>
			</p>Their email is ' . $email . '.</p>
			<p>They left this comment:</p>
			<p>' . $comment . '</p>

			<p>The team at OTrainU.</p>';
		}
		else
		{
			$subject = 'Negative User Experience Feedback';
			$msg = '<p>Hi admin,</p>
			<p>A website user has negative score from his/her user experience on ' . home_url('/') . '</p>
			</p>Their email is ' . $email . '.</p>
			<p>They left this comment:</p>
			<p>' . $comment . '</p>

			<p>The team at OTrainU.</p>';
		}
		
		add_filter('wp_mail_from',array($this,'change_feedback_email') );
		wp_mail(get_option('admin_email').',shane@otrain.com.au', $subject,$msg);
		$html = '<div><p>Thank you for your feedback.</p>
					  <p>This window will close shortly.</p></div>';
		remove_filter('wp_mail_from',array($this,'change_feedback_email') );
		$return = array( 
			'html' => $html,
			'exit' => true	
		);
		wp_send_json($return);
		die();
	}
	public function change_feedback_email($email){
		
		return 'feedback@epilepsyfoundation.org.au';
	}

}
