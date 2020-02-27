<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_signature.
 *
 * @package     mod_signature
 * @copyright   2020 OTrain <tech@otrain.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->libdir.'/pdflib.php');

require_once($CFG->dirroot.'/mod/assign/feedback/editpdf/fpdi/fpdi.php');
global $USER, $DB ;

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id.
$s  = optional_param('s', 0, PARAM_INT);

if ($id) {
    $cm             = get_coursemodule_from_id('signature', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('signature', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($s) {
    $moduleinstance = $DB->get_record('signature', array('id' => $n), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('signature', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_signature'));
}
require_login($course, true, $cm);



$contextcourse = context_course::instance($cm->course);
$modulecontext = context_module::instance($cm->id);
$context = context_system::instance();

$content = $moduleinstance->signature_content;

// $file = signature_pluginfile( $course , $cm ,$context , 'content', array( $moduleinstance->userfile , $moduleinstance->filename ) , false  );

if(isset( $_POST['hiddenSigDataa'] ) ){
	
	$signature	= $_POST['hiddenSigDataa'];
	$timecreate = date("D M j G:i:s T");   
	$timestart 	= $_POST['timestart'];
	$ip 		= $_POST['ip'];
	$mobile 	= isMobile()? 'mobile':'web';
	$name 		= $_POST['firstname'];
	$img = '<img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $signature)  . '">';
	$widthcell = 'width="100"'; 
	$to = $USER->email;
	$to = 'tech@otrain.com.au,'.$USER->email;
	
	include 'sign_copy2.php';
	$filename = $course->fullname.'_'.$USER->firstname.' '. $USER->lastname;
	// include 'sign_copy.php';
	$pdf_copy = $pdf->Output($course->fullname.' copy.pdf', 'E');
	
	

	
	
	
	$signatureid = $DB->insert_record( 'signature_issues', 
		array( 	'userid' => $USER->id, 
				'signatureid' => $cm->id , 
				'signaturename' => $filename,
				'code' => get_issue_uuid(),
				'timecreated' => time(),
				// 'pathnamehash' =>$file->get_pathnamehash()
	));
	$fileinfo = array('contextid' => $cm->id,
			'component' => 'mod_signature',
			'filearea' => 'content',
			'itemid' => $signatureid,
			'filepath' => '/',
			'mimetype' => 'application/pdf',
			'userid' => $USER->id,
			'filename' =>$filename.'.pdf'
	);

	$fs = get_file_storage();
	$file = $fs->create_file_from_string($fileinfo, $pdf->Output('', 'S'));
	
	$signclass = new stdClass();
	$signclass->id = $signatureid ;
	$signclass->pathnamehash =   $file->get_pathnamehash();
	$DB->update_record( 'signature_issues' , $signclass) ;
	
	
	include 'sign_certification.php';
	$pdf_certification = $pdf->Output($course->fullname. ' signature completion certificate.pdf', 'E');
	include 'sign_email.php';
	
}
$PAGE->set_url('/mod/signature/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext); 
$PAGE->requires->jquery();
$PAGE->requires->js('/mod/signature/libs/jSignature.min.noconflict.js');

echo $OUTPUT->header();
?>
<style type="text/css">

	div {
		margin-top:1em;
		margin-bottom:1em;
	}
	input {
		padding: .5em;
		margin: .5em;
	}
	select {
		padding: .5em;
		margin: .5em;
	}
	
	#signatureparent {
		color:darkblue;
		background-color:darkgrey;
		/*max-width:600px;*/
		padding:20px;
		width:200px;
	}
	
	/*This is the div within which the signature canvas is fitted*/
	#signature {
		border: 2px dotted black;
		background-color:lightgrey;
	}

	/* Drawing the 'gripper' for touch-enabled devices */ 
	html.touch #content {
		float:left;
		width:92%;
	}

	html.borderradius #scrollgrabber {
		border-radius: 1em;
	}
	#signaturea img {
		display:none!important;
	}
	 
</style>
<div>
<div id="content">
	<h1><?php echo $course->fullname; ?></h1>
	<?php echo $content; ?>
	<?php if(isset( $_POST['hiddenSigDataa'] ) ){
		
		$template = '
		<table>
		<tr>
			<td '.$widthcell.' >Name: </td>
			<td>'.	$name .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Date: </td>
			<td>'. 	$timecreate .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Signature: </td>
			<td><img src="'. $signature.'"</td>
		</tr>
	</table>';
		
		echo $template;
		
	}else{	?>
	
	<form action="" method="post">
		<p>
		<label for="firstname">Name: </label><input type="text" name="firstname" placeholder="First name" >
		</p>
		<p>
		<label for="date-sign">Date: </label><input type="date" id="date-sign" name="date-sign" >
		</p>
		<p>Signature</p>
	   <div id="signatureparent">
         <div id="signaturea"></div>
      </div>
		<input type="hidden" name="timestart" value="<?php echo date("D M j G:i:s T");    ?>">
		<input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>" >
		<input type="submit" id="submit" name="submit" value="Sign">
      <input type="hidden" id="hiddenSigDataa" name="hiddenSigDataa" />
      <script type="text/javascript">
	  (function($){
         $(document).ready(function() {
         var $sigdiv = $("#signaturea").jSignature({'UndoButton':false});

         // -- i explain from here...
         $('#submit').click(function(){
            var sigData = $('#signaturea').jSignature('getData','default');
            $('#hiddenSigDataa').val(sigData);
         });
        // -- ... to here.
		 })
        })(jQuery)
     </script>
	  </form> 
	<?php } ?>
</div>

</div>




<?php
echo $OUTPUT->footer();
