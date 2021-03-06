<?php 

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
global $USER;
$code = required_param('id', PARAM_INT); // Issued Code.
if( isset( $_GET['search'] ) ){
	// $issuedsign = $DB->get_records("signature_issues" , array ( 'signatureid' => $code ,'signaturename' =>  ) );
	$issuedsign = $DB->get_records_sql( "select * from mdl_signature_issues where signatureid = :signatureid and signaturename like :search" , array('signatureid' => $code , 'search' => '%'.$_GET['search'].'%' ) );
	
}
else{
	$issuedsign = $DB->get_records("signature_issues" , array ( 'signatureid' => $code ) );
}

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
$PAGE->set_url('/mod/signature/view2.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext); 
echo $OUTPUT->header();



// check role
$roles = get_user_roles(context_course::instance($course->id), $USER->id);
if( count ( $roles ) > 0  || $admin = is_siteadmin($USER->id)){
	$student = true ;
	foreach( $roles as $k => $r ){
		if( $r->roleid != 5 ){
			$student = false ;
			break;
		}
	}
	if( !$student ){
	echo html_writer::start_tag('form', array('method' => 'get', 'action' => 'view2.php',
                'class' => 'quizsavegradesform form-inline'));
	echo html_writer::label('Search user: ', 'search');
	echo html_writer::empty_tag('input', array('type' => 'text', 'name' => 'search',
					'value' => (isset($_GET['search'])? $_GET['search']:'' ) , 'id' => 'search'));
	echo html_writer::empty_tag('input', array('type' => 'submit', 'class' => 'btn btn-secondary m-l-1',
					'name' => 'searchbutton', 'value' => 'Search'));
	echo html_writer::empty_tag('input', array( 'type' => 'hidden', 'name' => 'id' , 'value' => $id) );				
	echo html_writer::end_tag('form');
	}
	
	$table = new html_table();
	$table->width = "95%";
	$table->tablealign = "center";

	$table->head = array(' ', get_string('fullname'), get_string('grade'));
	$table->align = array("left", "left", "center");
	$table->size = array('1%', '89%', '10%');

	$table = new html_table();
	$table->width = "95%";
	$table->tablealign = "center";
	$table->head = array('User', 'Signature copy', 'Signature complete certification' ,  'Code');
	$table->align = array("left", "left", "left", "center");
	$table->size = array('5%', '30%', '30%', '25%');
	
	foreach( $issuedsign as $sign ){
		if( $student && $sign->userid != $USER->id && !$admin ){
			continue;
		}
		$table->data[] = array(  '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$sign->userid.'">'.$sign->userid.'</a>' , '<a href="'.$CFG->wwwroot.'/mod/signature/wmsendfile.php?code='.$sign->code.'">'.$sign->signaturename.'.pdf</a>' , '<a href="'.$CFG->wwwroot.'/mod/signature/wmsendfile_certificate.php?code='.$sign->code.'">'.$sign->signaturename.'_signature_completion_certificate.pdf </a>', $sign->code );
		
	}
	echo html_writer::table($table);
}
 // Create the table for the users.
           
			
			


// $context = context_course::instance($course->id);

// $a = user_has_role_assignment($USER->id, 5 ,$context->__get('id') );


echo $OUTPUT->footer();


