<?php
require_once plugin_dir_path( __FILE__ ).'/lib/PHPExcel/Class_csv_export.php';
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       otrain.com.au
 * @since      1.0.0
 *
 * @package    Ot_feedback
 * @subpackage Ot_feedback/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ot_feedback
 * @subpackage Ot_feedback/admin
 * @author     PhiHai <nguyenphihai1910@gmail.com>
 */
class Ot_feedback_Admin {

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
	
	private $admin_page;
	private $admin_feedback;
	private $dateFormat = 'Y-m-d';
	// $this->table = 'wp_scoreboard';
	private $table = 'wp_scoreboard';
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
		
		
		$this->admin_page = admin_url('admin.php?page=ot-feedback');
		
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
		 * defined in Ot_feedback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ot_feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
  wp_enqueue_style( 'jquery-ui' );   
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ot_feedback-admin.css', array(''), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		
		wp_enqueue_script( $this->plugin_name.'a', plugin_dir_url( __FILE__ ) . 'js/ot_feedback-admin.js', array( 'jquery','jquery-ui-datepicker'), $this->version, false );

	}
	
	public function createSettingsMenu(){
		$wp_logging_list_page = add_menu_page(__('OT Feedback', 'ot-feedback'),
            __('OT Feedback', 'ot_feedback'),
            'manage_options',
            'ot-feedback',
            array($this, 'ot_menu')
            
        );
		

	}
	function formatDate($date) {
		$date = strtotime($date);		
		return date($this->dateFormat ,$date);
	}
	function getMoodResults($date_start = false, $date_end = false) {
			global $wpdb;
			
			
			$date_query = '';		
				
			if($date_start && $date_end) {
				$date_start = $this->formatDate($date_start);
				$date_end = $this->formatDate($date_end);
				
				$date_query = " AND DATE(`date`) >= '".$date_start."' AND DATE(`date`) <= '".$date_end."'";
			} else if(!$date_start && $date_end) {
				$date_query = " AND DATE(`date`) <= '".$date_end."'";
			} else if($date_start && !$date_end) {
				$date_query = " AND DATE(`date`) >= '".$date_start."'";	
			}
				

			$query = "SELECT * FROM `".$this->table."` WHERE `score` > '-3' {$date_query} ORDER BY `date` DESC limit 50";
			$wpdb->get_results ( $query,ARRAY_A   );
			$result = $wpdb->get_results ( $query,ARRAY_A   );
			
			return $result;
		}
	function printSingleDate($date) {
			global $wpdb;
			$date = $this->formatDate($date);
			
			
			$query = "SELECT * FROM `".$this->table."` WHERE DATE(`date`) = '{$date}'" ;
			
			$results = $wpdb->get_results($query);
			
			if(!$results) {
				echo "No records on this date.";
				return;
			}
			
			echo "<div class='single_date_block'><a href='{$this->admin_page}&calendar=1'><b>Back</b></a><br/><h4>Date: {$date} " . 
				"</h4><table border='1' class='single_date_table'>";
			echo "<tr class='first_tr'><td>Score</td><td>IP</td><td>User</td><td>Email</td><td>Comment</td></tr>";
			foreach($results as $data) {
				
				echo "<tr> <td>{$data->score}</td><td>{$data->ip}</td><td>{$data->user}</td><td>{$data->email}</td><td>{$data->comments}</td></tr>";
			}
			echo "</table></div>";
		}
		
		function printMoodTables($date_start = false, $date_end = false) {
			
			$result = $this->getMoodResults($date_start, $date_end);
			
			if(!$result) {
				echo "No records in these dates.";
				return;	
			}
			
			$data_by_dates = array();
			# Restructure array to make easy access
			
			foreach($result as $data) {
				$date = date_parse($data['date']);
				
				if(!isset($data_by_dates[$date['year']][$date['month']][$date['day']])) {
					$data_by_dates[$date['year']][$date['month']][$date['day']] = 0;	
				}		
				
				$data_by_dates[$date['year']][$date['month']][$date['day']] += $data['score'];
			}	
			
			foreach($data_by_dates as $year => $month_data) {
				echo "<div class='clear'></div><h2><a href=\"#\" >Year {$year}</a></h2>";
				echo '<div id="'.$year. '" '; 
				
				echo '>';
				foreach($month_data as $month => $days_data) {
					$month_name = date("F", mktime(0, 0, 0, $month, 10));
					echo "<div class='mood_month_block'><h4>{$month_name}</h4><table border='1' class='mood_table'>";
					
					$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
					for($i = 1; $i <= $days_in_month; $i++) {
						if($i == 1) {
							echo "<tr>";	
						}
						
						$mood_score = (isset($days_data[$i])) ? $days_data[$i] : '';
						$color = ($mood_score !== '') ? $this->getMoodColor($mood_score) : '#FFF';
						$date = $year.'-'.$month.'-'.$i;
						$day_text = ($mood_score !== '') ? "<a href='{$this->admin_page}&calendar=1&date={$date}'>{$i}</a>" : $i;
						echo "<td style='background-color: {$color}'>{$day_text}</td>";
						
						
						if($i%7 == 0) {
							echo "</tr><tr>";	
						}
						
						if($i == $days_in_month) {
							# Fill with empty td's rest of month block.
							$days_left = 35-$days_in_month;
							for($j = 0; $j < $days_left; $j++) {
								echo "<td>&nbsp;</td>";	
							}
							echo "</tr>";	
						}
					}
					echo "</table></div>";
				}	
				echo '</div>';
			}		
		}
	
	public function getMoodColor($score) {
			$score = (int)$score;
			$color = '';
			if($score >= 8) {
				$color = '#0040FF';	
			} else if($score >= 1 && $score <= 7) {
				$color = '#99B3FF';	
			} else if($score == 0) {
				$color = '#DFDFD0';	
			} else if($score <= -1 && $score >= -7) {
				$color = '#FFBFBF';
			} else {
				$color = '#FF0000';	
			}
			
			return $color;
		}
	function get_feedback($args){
		global $wpdb;
		// $default = array(
			// 'test' => 13,
			// 'order_by' => 'id'
		// );
		// $args = array_merge ($args , $default);
		$ob ='';
		switch($_GET['orderby']) {
			case 'date': 
			$ob = ' order by `date` DESC';
			
			break;
			case 'name': $ob = ' order by `user`';
			break;
			case 'score': $ob = ' order by `score`';
			break;
			default: $ob = ' order by `date` DESC';
			
		}
		if( isset( $_GET['score'] ) ) {
			switch($_GET['score']) {
				case '1': $ob = ' where`score` = 1  order by `date` DESC';
				break;
				case '0': $ob = ' where `score` = 0  order by `date` DESC';
				break;
				case '-1': $ob = ' where `score` = -1  order by `date` DESC';
				break;
				default: $ob = '';
			}
		}
		if( $ob === '' ){
			$ob = ' order by `date` DESC';
		}
		$sql = "select * from $this->table $ob limit 50";
		
		return $wpdb->get_results($sql);
		
		
	}
	
	function ot_menu(){
		global $wpdb;
		$order_by = $_GET['orderby'];
		$filter = $_GET['filter'];
		if(isset($_GET['date_from'])) {
			$date_from =  $date_start = $_GET['date_from']	;
			
		}
		
		if(isset($_GET['date_to'])) {
			$date_to = $date_end = $_GET['date_to']	;
		}
		
		if (( isset($_GET['calendar']) && $_GET['calendar'] ) ) { 
		 
		 
			if(isset($_GET['date'])){
				
				$date = $this->formatDate($_GET['date']);
			
			
				$query = "SELECT * FROM `".$this->table."` WHERE DATE(`date`) = '{$date}'" ;
				
				$results = $wpdb->get_results($query);
				
			}
			else
			{
				$results = $this->getMoodResults($date_from, $date_to);
			}	
			
		 
			
			
		}
		else
		{
			$results = $this->get_feedback($filter);
		}
		
		
		
		if( $results && $_GET['csv'] ){
			
		
			$results_csv = json_decode(json_encode($results), true);
			$ar_k = array_combine (array_keys($results_csv[0]),array_keys($results_csv[0]));
	
			array_unshift($results_csv,$ar_k);
			
			 
			$a = new csv_export();

			$link = plugin_dir_path( __FILE__ ).'/export.xlsx';
			
			$a->create_csv($results_csv,$link);
			echo '<a class="download" style="display:none" href="'.plugin_dir_url(__FILE__).'export.xlsx" download >download</a>';
		}
		
		
		if (( isset($_GET['calendar']) && $_GET['calendar'] ) ) {
		?>
		<div role="main">
			<span id="maincontent"></span><h1>Feedback Review</h1> 
			<fieldset style="border:double">
				<table width="100%" border="1" style="margin-bottom:0px;">
					<tr>
					<td width="30%" valign="top" padding="5px">
					<form action='<?php echo $this->admin_page; ?>' method='get'>
						<strong>Select Date Range:</strong><br /><br />
						<div style="text-align:left;width:200px;">
							<p>From <span style="float:right">
							<input type='text' name='date_from' class='datepicker' value='<?=$date_from;?>' /></span>
						 </p> 
						 <p>To <span style="float:right"><input type='text' name='date_to' class='datepicker' value='<?=$date_to;?>' /></span>
						 </p> 
						</div>
					   
							<input type='submit' name='show_mood' value='Show' />
						<input type="hidden" value="ot-feedback" name="page" />
						<input type="hidden" value="1" name="calendar" />
					</form>
					<hr style="border-bottom: 1px solid" />
					<a href="<?php echo $this->admin_page; ?>&calendar=1">RESET VIEW</a>
    <br /><br />
					<a href ="<?php echo $this->admin_page; ?>" >LIST VIEW</a>
					</td>
					<td valign="top" align="left" width="30%" style="padding-left:30px"><a href="<?php echo $this->admin_page.'&'.$_SERVER['QUERY_STRING'].'&csv=1'; ?>">Download CSV</a></td>
					<td></td>
					<tr>
				</table>
			</fieldset>
			<table width="100%" border="0" style="margin-bottom:0px;"><tr>
				<td style="text-align:right">
					<img src="http://otrainu.com/office/images/chat_on.jpg" />
				<td valign="center;"> <span>Happy</span>
				</td><td style="text-align:right">
					<img src="http://otrainu.com/office/images/chat_fan.jpg" />
				<td valign="center"> <span>Fan</span>
				</td><td style="text-align:right">
					<img src="http://otrainu.com/office/images/chat_raving.jpg" />
				<td valign="center"> <span>Raving</span>
				</td><td style="text-align:right">
					<img src="http://otrainu.com/office/images/favicon.jpg" />
				<td valign="center"> <span>Neutral </span> 
				</td><td style="text-align:right">
					<img src="http://otrainu.com/office/images/chat_off.jpg" />
				<td valign="center"> <span>Sad </span>
				</td><td style="text-align:right">
					<img src="http://otrainu.com/office/images/chat_comments.jpg" />
				<td valign="center"> <span">No Comments </span>
				</td></tr></table> 
			
			<div class='mood_container'>  
			<?php 
			if(isset($_GET['date'])){	
			
				$this->printSingleDate($_GET['date'], $site);
			} else {
		
			?>
				
				<?php
				$this->printMoodTables($date_from, $date_to, $site);	
			}
			?>
			</div>
			
		</div>

		<?php
		}
		else
		{
			
		
		?>
		<div role="main">
			<span id="maincontent"></span><h1>Feedback Review</h1> 
			<fieldset style="border:double">
			<table width="100%" border="1" style="margin-bottom:0px;">
				<tbody>
					<tr>
						<td width="30%" valign="top">
							<p><strong>Sort feedback by:</strong>
							<a href="<?php echo $this->admin_page.'&orderby=date'; ?>">Date</a>&nbsp; |  &nbsp;<a href="<?php echo $this->admin_page.'&orderby=name'; ?>">Name</a>&nbsp; | &nbsp;<a href="<?php echo $this->admin_page.'&orderby=score'; ?>">Score</a></p><hr style="border-bottom: 1px solid"><br><a href="<?php echo $this->admin_page; ?>">RESET VIEW</a>
							<br><br>
							<a href="<?php echo $this->admin_page.'&calendar=1'; ?>">CALENDAR VIEW</a></td>
							
							<td valign="top" align="right"><a href="<?php echo $this->admin_page.'&csv=1'; ?>">Download CSV</a></td>
							<td valign="top" width="20%">
							<p></p>Filter feedback by: 
							<a href="<?php echo $this->admin_page.'&score=1'; ?>">
								<img width="32px" height="32px" src="<?php echo plugin_dir_url( __FILE__ ) . 'img/happy.png' ?>"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/chat_on.jpg'?>">
								</a><br>
							<a href="<?php echo $this->admin_page.'&score=0'; ?>">
								<img width="32px" height="32px" src="<?php echo plugin_dir_url( __FILE__ ) . 'img/normal.png' ?>"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/favicon.jpg'?>">
								</a><br>
							<a href="<?php echo $this->admin_page.'&score=-1'; ?>">
								<img width="32px" height="32px" src="<?php echo plugin_dir_url( __FILE__ ) . 'img/sad.png' ?>"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/chat_off.jpg'?>"> 
								</a>
							</td><td valign="top" align="left"></td></tr></tbody></table></fieldset>
							<?php if( $results ) { ?>
							<div width="95%" style="height:500px;overflow:auto;">
								<table style="border-collapse: collapse;text-align:center;" class="table">
								<tbody><tr><th style="text-align:left">Date</th><th style="text-align:left">Name</th><th style="text-align:left">Email</th><th style="text-align:left">Comments</th></tr>
		
								<?php foreach($results as $rs) {
									switch($rs->score) {
										case '1': echo '<tr bgcolor="#99B3FF" style="border-bottom:1pt solid white!important;">';
										break;
										case '0': echo '<tr bgcolor="#DFDFD0" style="border-bottom:1pt solid white!important;">';
										break;
										case '-1': echo '<tr bgcolor="#FFBFBF" style="border-bottom:1pt solid white!important;">';
										break;
										default:echo '<tr>';
									}
									
									// echo '<tr bgcolor="#99B3FF" style="border-bottom:1pt solid white!important;">';
									echo '<td width="15%">' .$rs->date. '</td><td width="20%">'. $rs->user .'</td><td>' .$rs->email . '</td><td>'.$rs->comments.'</td>';
									echo '</tr>';
								} ?>

								</tbody>
								</table>
								
							</div>
							<?php }else{ echo 'Not found'; } ?>
	</div>
	<?php 
		}
	}
}
