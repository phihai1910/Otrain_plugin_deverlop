<?php 

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
global $USER;
$code = required_param('id', PARAM_INT); // Issued Code.
$issuedsign = $DB->get_records("signature_issues" , array ( 'signatureid' => $code ) );

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
	
	foreach( $issuedsign as $sign ){
		if( $student && $sign->userid != $USER->id && !$admin ){
			continue;
		}			
		echo '<a href="'.$CFG->wwwroot.'/mod/signature/wmsendfile.php?code='.$sign->code.'">'.$sign->signaturename.'</a>';
		echo '</br>';
		
	}
	
}

// $context = context_course::instance($course->id);

// $a = user_has_role_assignment($USER->id, 5 ,$context->__get('id') );


echo $OUTPUT->footer();


