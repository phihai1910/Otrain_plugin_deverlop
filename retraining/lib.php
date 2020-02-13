<?php

/**
 *-----------------------------------------------------------------------------
 * Retraining Notification
 *-----------------------------------------------------------------------------
 * Retraining Notification function library
 *
 * @package     local_retraining
 * @copyright   OTrain Pty Ltd <http://www.otrain.com.au>
 *-----------------------------------------------------------------------------
 **/

define('CERTIFICATE_TABLE', 'certificate');
define('SIMPLECERTIFICATE_TABLE', 'simplecertificate');

/**
 *-----------------------------------------------------------------------------
 * Get course custom data for a specific course ID from the database.
 *-----------------------------------------------------------------------------
 * @param  int  $courseid
 * @return object moodle course
 *-----------------------------------------------------------------------------
 **/

function local_retraining_get_notification_data($course_id, $notification_type = null) {
	global $DB;

	if ($notification_type) { // Get row for specific notification type
		$course_custom_row = $DB->get_record("course_custom", array(
			"courseid" => $course_id,
			"retraining_notification_type" => $notification_type
		));
	}
	else { // Just get the first row for general data e.g. retraining enabled/interval

		$q = "select * from {course_custom} where courseid = :courseid limit 1";

		$course_custom_row = $DB->get_record_sql($q, array("courseid" => $course_id));
	}

	return $course_custom_row;
}

/**
 *-----------------------------------------------------------------------------
 * Display custom course fields for retraining notification.
 * Note these are loaded via course->edit_form.php but to keep the customisation
 * to a minimum this function was created to render the fields.
 *-----------------------------------------------------------------------------
 * @param  object moodle form object for course edit
 * @return void
 *-----------------------------------------------------------------------------
 **/

function local_retraining_custom_course_fields($mform, $course_id) {

	$mform->addElement("header", "local_retraining_header", "Retraining Notification");

	$mform->addElement("static", "local_retraining_first", get_string("retraininggeneralsubheading", "local_retraining"));

	$notification_data = local_retraining_get_notification_data($course_id);

   //-----------------------------------------------------------------------------
   // Course product page
   //-----------------------------------------------------------------------------

	$mform->addElement(
		"text",
		"local_retraining_course_url",
		get_string("retrainingcourseurl", "local_retraining"),
		"size=80"
	);

	$mform->addHelpButton(
		"local_retraining_course_url",
		"retrainingcourseurl",
		"local_retraining"
	);

	$mform->setType(
		"local_retraining_course_url",
		PARAM_TEXT
	);

	if ($notification_data && isset($notification_data->course_url)) {
	   $mform->setDefault("local_retraining_course_url", $notification_data->course_url);
   }

   //-----------------------------------------------------------------------------
	// Retraining required flag
   //-----------------------------------------------------------------------------

	$mform->addElement("selectyesno", "local_retraining_required", get_string("retrainingrequired", "local_retraining"));
	$mform->addHelpButton("local_retraining_required", "retrainingrequired", "local_retraining");
	$mform->setType("local_retraining_required", PARAM_INT);

   if ($notification_data && isset($notification_data->retraining_required)) {
	   $mform->setDefault("local_retraining_required", $notification_data->retraining_required);
   }

   //-----------------------------------------------------------------------------
   // Retraining frequency
   //-----------------------------------------------------------------------------

   $mform->addElement("text", "local_retraining_frequency", get_string("retrainingfrequency", "local_retraining"), "size=5");
   $mform->addHelpButton("local_retraining_frequency", "retrainingfrequency", "local_retraining");
   $mform->setType("local_retraining_frequency", PARAM_INT);
   if ($notification_data && isset($notification_data->retraining_frequency)) {
	   $mform->setDefault("local_retraining_frequency", $notification_data->retraining_frequency);
   }

   //-----------------------------------------------------------------------------
   // Retraining first notice
   //-----------------------------------------------------------------------------

   $first_notification_data = local_retraining_get_notification_data($course_id, 'first');

   $mform->addElement("static", "local_retraining_first", get_string("retrainingfirstnotificationsubheading", "local_retraining"));

   $mform->addElement("text", "local_retraining_first_notice", get_string("retrainingfirstnotice", "local_retraining"), "size=5");
   $mform->addHelpButton("local_retraining_first_notice", "retrainingfirstnotice", "local_retraining");
   $mform->setType("local_retraining_first_notice", PARAM_INT);
   if ($first_notification_data && isset($first_notification_data->retraining_notification_days)) {
	   $mform->setDefault("local_retraining_first_notice", $first_notification_data->retraining_notification_days);
   }
   else {
	   $mform->setDefault("local_retraining_first_notice", 60);
   }

   //-----------------------------------------------------------------------------
   // Retraining first notice subject
   //-----------------------------------------------------------------------------

   $mform->addElement("text", "local_retraining_first_notice_subject", get_string("retrainingfirstnoticesubject", "local_retraining"), "size=82");

   if ($first_notification_data && isset($first_notification_data->retraining_email_subject)) {
	   $mform->setDefault("local_retraining_first_notice_subject", $first_notification_data->retraining_email_subject);
   }
   else {
	   $mform->setDefault("local_retraining_first_notice_subject", get_string("retrainingfirstnoticesubjectdefault", "local_retraining"));
   }

   $mform->addHelpButton("local_retraining_first_notice_subject", "retrainingfirstnoticesubject", "local_retraining");
   $mform->setType("local_retraining_first_notice_subject", PARAM_TEXT);

   //-----------------------------------------------------------------------------
   // Retraining first notice message
   //-----------------------------------------------------------------------------

   $mform->addElement("textarea", "local_retraining_first_notice_message", get_string("retrainingfirstnoticemessage", "local_retraining"),
	   "'wrap='virtual' rows='8' cols='80'");

   if ($first_notification_data && isset($first_notification_data->retraining_email_message)) {
	   $mform->setDefault("local_retraining_first_notice_message", $first_notification_data->retraining_email_message);
   }
   else {
	   $mform->setDefault("local_retraining_first_notice_message", get_string("retrainingfirstnoticemessagedefault", "local_retraining"));
   }

   $mform->addHelpButton("local_retraining_first_notice_message", "retrainingfirstnoticemessage", "local_retraining");
   $mform->setType("local_retraining_first_notice_message", PARAM_RAW);

   //-----------------------------------------------------------------------------
   // Retraining second notice
   //-----------------------------------------------------------------------------

   $second_notification_data = local_retraining_get_notification_data($course_id, 'second');

   $mform->addElement("static", "local_retraining_second", get_string("retrainingsecondnotificationsubheading", "local_retraining"));

   $mform->addElement("text", "local_retraining_second_notice", get_string("retrainingsecondnotice", "local_retraining"), "size=5");
   $mform->addHelpButton("local_retraining_second_notice", "retrainingsecondnotice", "local_retraining");
   $mform->setType("local_retraining_second_notice", PARAM_INT);
   if ($second_notification_data && isset($second_notification_data->retraining_notification_days)) {
	   $mform->setDefault("local_retraining_second_notice", $second_notification_data->retraining_notification_days);
   }
   else {
	   $mform->setDefault("local_retraining_second_notice", 60);
   }

   //-----------------------------------------------------------------------------
   // Retraining second notice subject
   //-----------------------------------------------------------------------------

   $mform->addElement("text", "local_retraining_second_notice_subject", get_string("retrainingsecondnoticesubject", "local_retraining"), "size=82");

   if ($second_notification_data && isset($second_notification_data->retraining_email_subject)) {
	   $mform->setDefault("local_retraining_second_notice_subject", $second_notification_data->retraining_email_subject);
   }
   else {
	   $mform->setDefault("local_retraining_second_notice_subject", get_string("retrainingsecondnoticesubjectdefault", "local_retraining"));
   }

   $mform->addHelpButton("local_retraining_second_notice_subject", "retrainingsecondnoticesubject", "local_retraining");
   $mform->setType("local_retraining_second_notice_subject", PARAM_TEXT);

   //-----------------------------------------------------------------------------
   // Retraining second notice message
   //-----------------------------------------------------------------------------

   $mform->addElement("textarea", "local_retraining_second_notice_message", get_string("retrainingsecondnoticemessage", "local_retraining"),
	   "'wrap='virtual' rows='8' cols='80'");

   if ($second_notification_data && isset($second_notification_data->retraining_email_message)) {
	   $mform->setDefault("local_retraining_second_notice_message", $second_notification_data->retraining_email_message);
   }
   else {
	   $mform->setDefault("local_retraining_second_notice_message", get_string("retrainingsecondnoticemessagedefault", "local_retraining"));
   }

   $mform->addHelpButton("local_retraining_second_notice_message", "retrainingsecondnoticemessage", "local_retraining");
   $mform->setType("local_retraining_second_notice_message", PARAM_RAW);

   //-----------------------------------------------------------------------------
   // Retraining final notice
   //-----------------------------------------------------------------------------

   $final_notification_data = local_retraining_get_notification_data($course_id, 'final');

   $mform->addElement("static", "local_retraining_final", get_string("retrainingfinalnotificationsubheading", "local_retraining"));

   $mform->addElement("text", "local_retraining_final_notice", get_string("retrainingfinalnotice", "local_retraining"), "size=5");
   $mform->addHelpButton("local_retraining_final_notice", "retrainingfinalnotice", "local_retraining");
   $mform->setType("local_retraining_final_notice", PARAM_INT);
   if ($final_notification_data && isset($final_notification_data->retraining_notification_days)) {
	   $mform->setDefault("local_retraining_final_notice", $final_notification_data->retraining_notification_days);
   }
   else {
	   $mform->setDefault("local_retraining_final_notice", 60);
   }

   //-----------------------------------------------------------------------------
   // Retraining final notice subject
   //-----------------------------------------------------------------------------

   $mform->addElement("text", "local_retraining_final_notice_subject", get_string("retrainingfinalnoticesubject", "local_retraining"), "size=82");

   if ($final_notification_data && isset($final_notification_data->retraining_email_subject)) {
	   $mform->setDefault("local_retraining_final_notice_subject", $final_notification_data->retraining_email_subject);
   }
   else {
	   $mform->setDefault("local_retraining_final_notice_subject", get_string("retrainingfinalnoticesubjectdefault", "local_retraining"));
   }

   $mform->addHelpButton("local_retraining_final_notice_subject", "retrainingfinalnoticesubject", "local_retraining");
   $mform->setType("local_retraining_final_notice_subject", PARAM_TEXT);

   //-----------------------------------------------------------------------------
   // Retraining final notice message
   //-----------------------------------------------------------------------------

   $mform->addElement("textarea", "local_retraining_final_notice_message", get_string("retrainingfinalnoticemessage", "local_retraining"),
	   "'wrap='virtual' rows='8' cols='80'");

   if ($final_notification_data && isset($final_notification_data->retraining_email_message)) {
	   $mform->setDefault("local_retraining_final_notice_message", $final_notification_data->retraining_email_message);
   }
   else {
	   $mform->setDefault("local_retraining_final_notice_message", get_string("retrainingfinalnoticemessagedefault", "local_retraining"));
   }

   $mform->addHelpButton("local_retraining_final_notice_message", "retrainingfinalnoticemessage", "local_retraining");
   $mform->setType("local_retraining_final_notice_message", PARAM_RAW);

}

/**
 *-----------------------------------------------------------------------------
 * Process custom course data from a course edit and save it
 * to the custom table course_custom which is included in the
 * schema for the local_retraining plugin.
 *-----------------------------------------------------------------------------
 * @param  object $course
 * @return void
 *-----------------------------------------------------------------------------
 **/

function local_retraining_save_notification_data($course_data) {

	global $DB;

	// First notification data
	$first_notification_data = new stdclass();
	$first_notification_data->courseid = $course_data->id;
	$first_notification_data->course_url = $course_data->local_retraining_course_url;
	$first_notification_data->retraining_required = $course_data->local_retraining_required;
	$first_notification_data->retraining_frequency = $course_data->local_retraining_frequency;
	$first_notification_data->retraining_notification_type = 'first';
	$first_notification_data->retraining_notification_days = $course_data->local_retraining_first_notice;
	$first_notification_data->retraining_email_subject = $course_data->local_retraining_first_notice_subject;
	$first_notification_data->retraining_email_message = $course_data->local_retraining_first_notice_message;

	upsert_notification_data($first_notification_data, $first_notification_data->retraining_notification_type);

	// Second notification data
	$second_notification_data = new stdclass();
	$second_notification_data->courseid = $course_data->id;
	$second_notification_data->course_url = $course_data->local_retraining_course_url;
	$second_notification_data->retraining_required = $course_data->local_retraining_required;
	$second_notification_data->retraining_frequency = $course_data->local_retraining_frequency;
	$second_notification_data->retraining_notification_type = 'second';
	$second_notification_data->retraining_notification_days = $course_data->local_retraining_second_notice;
	$second_notification_data->retraining_email_subject = $course_data->local_retraining_second_notice_subject;
	$second_notification_data->retraining_email_message = $course_data->local_retraining_second_notice_message;

	upsert_notification_data($second_notification_data, $second_notification_data->retraining_notification_type);

	// Final notification data
	$final_notification_data = new stdclass();
	$final_notification_data->courseid = $course_data->id;
	$final_notification_data->course_url = $course_data->local_retraining_course_url;
	$final_notification_data->retraining_required = $course_data->local_retraining_required;
	$final_notification_data->retraining_frequency = $course_data->local_retraining_frequency;
	$final_notification_data->retraining_notification_type = 'final';
	$final_notification_data->retraining_notification_days = $course_data->local_retraining_final_notice;
	$final_notification_data->retraining_email_subject = $course_data->local_retraining_final_notice_subject;
	$final_notification_data->retraining_email_message = $course_data->local_retraining_final_notice_message;

	upsert_notification_data($final_notification_data, $final_notification_data->retraining_notification_type);
}

/**
 *-----------------------------------------------------------------------------
 * Upsert (update or insert) notification data into course custom table
 *-----------------------------------------------------------------------------
 * @param  object $data
 * @param  string $notification_type
 * @return none
 *-----------------------------------------------------------------------------
 **/

function upsert_notification_data($data, $notification_type) {

	global $DB;

	// Check if row exists for the course and notification type
	$existing_data = local_retraining_get_notification_data($data->courseid, $notification_type);

	if ($existing_data) { // Row exists, update data
		$data->id = $existing_data->id;
		$DB->update_record("course_custom", $data);
	}
	else { // Row doesn"t exist, insert data
		$DB->insert_record("course_custom", $data);
	}
}

/**
 *-----------------------------------------------------------------------------
 * Identify users that require retraining notification for a given course.
 *-----------------------------------------------------------------------------
 * @param  int $notice_days
 * @return object
 *-----------------------------------------------------------------------------
 **/

function local_retraining_identify_users_for_retraining($notice_days = 60) {

	global $DB;

	$q = "
		select distinct * from (
			select
				@id:=@id+1 as id,
				ue.id as userenrolmentid,
				ue.userid as userid,
				e.courseid,
				cc.retraining_frequency,
				cc.retraining_notification_type,
				cc.retraining_notification_days,
				cc.retraining_email_subject,
				cc.retraining_email_message,
				comp.timecompleted,
				from_unixtime(comp.timecompleted) as timecompleted_human,
				from_unixtime(comp.timecompleted + (cc.retraining_frequency * 86400)) as next_retraining_date,
				round(
					( (comp.timecompleted + (cc.retraining_frequency * 86400)) - unix_timestamp(curdate()) )
					/ 86400
				) as days_to_next_retraining
			from
				(select @id := 0) id,
				{enrol} e inner join {user_enrolments} ue
				on e.id = ue.enrolid
				inner join {course_custom} cc
				on cc.courseid = e.courseid
				and cc.retraining_required = 1
				inner join {course_completions} comp
				on comp.course = e.courseid
				and comp.userid = ue.userid
			where
			   comp.timecompleted is not null
				and not exists (
					select 1
					from {retraining_notifications} rn
					where rn.userid = ue.userid
					and rn.courseid = e.courseid
					and rn.userenrolmentid = ue.id
					and rn.notification_type = cc.retraining_notification_type
			)
			order by
				days_to_next_retraining
		) retraining
		where
			retraining.days_to_next_retraining <= retraining_notification_days
	   ";

	$r = $DB->get_records_sql($q);

	return $r;

}

/**
 *-----------------------------------------------------------------------------
 * Parse email variables
 *-----------------------------------------------------------------------------
 * @param  string $input text of email message
 * @param  object $user to send email to
 * @param  int $courseid
 * @return string
 *-----------------------------------------------------------------------------
 **/

function local_retraining_parse_email_text($input, $to, $courseid) {

	global $CFG, $DB;

	$output = $input;

	// {site}
	$frontpage_course = $DB->get_record('course', array('id' => 1));
	$output = str_replace('{site}', $frontpage_course->shortname, $output);

	// {retrainingurl}
	$output = str_replace('{retrainingurl}', "$CFG->wwwroot/local/retraining/start.php?userid=$to->id&courseid=$courseid", $output);

	// {firstname}
	$output = str_replace('{firstname}', $to->firstname, $output);

	// {lastname}
	$output = str_replace('{lastname}', $to->lastname, $output);

	// {email}
	$output = str_replace('{email}', $to->email, $output);

	// {idnumber}
	$output = str_replace('{idnumber}', $to->idnumber, $output);

	// {course} or {courseshortname}
	$course = $DB->get_record('course', array('id' => $courseid));
	$output = str_replace('{course}', $course->shortname, $output);
	$output = str_replace('{courseshortname}', $course->shortname, $output);

	// {coursefullname}
	$output = str_replace('{coursefullname}', $course->fullname, $output);

	return $output;
}

/**
 *-----------------------------------------------------------------------------
 * Send the retraining notification email
 *-----------------------------------------------------------------------------
 * @param  object $notification_data
 * @return bool
 *-----------------------------------------------------------------------------
 **/

function local_retraining_send_notification_email($notification_data) {

	global $DB;

	$courseid = $notification_data->courseid;
	$to = $DB->get_record('user', array('id' => $notification_data->userid));
	$from = core_user::get_support_user();
	$subject = local_retraining_parse_email_text($notification_data->retraining_email_subject, $to, $courseid);
	$message = local_retraining_parse_email_text($notification_data->retraining_email_message, $to, $courseid);

	if (email_to_user($to, $from, $subject, $message)) {
		$type = $notification_data->retraining_notification_type;
		mtrace("...... Sending $type retraining notification to: $to->firstname $to->lastname (email=$to->email userid=$to->id)");
		local_retraining_save_notification_sent($notification_data, true);
	}
	else {
		local_retraining_save_notification_sent($notification_data, false);
	}
}

/**
 *-----------------------------------------------------------------------------
 * Save notification sent to database in mdl_retraining_notifications table.
 *-----------------------------------------------------------------------------
 * @param  object $notification_data
 * @param  bool $email_sent flag
 * @return bool
 *-----------------------------------------------------------------------------
 **/

function local_retraining_save_notification_sent($notification_data, $email_sent) {

	global $DB;

	$retraining_notification_data = new stdclass();
	$retraining_notification_data->userid = $notification_data->userid;
	$retraining_notification_data->courseid = $notification_data->courseid;
	$retraining_notification_data->userenrolmentid = $notification_data->userenrolmentid;
	$retraining_notification_data->notification_type = $notification_data->retraining_notification_type;
	$retraining_notification_data->notification_days = $notification_data->retraining_notification_days;
	$retraining_notification_data->notification_timestamp = date('U');
	$retraining_notification_data->email_sent = $email_sent;

	 // Check if row exists for the course and notification type
	$existing_data = $DB->get_record('retraining_notifications', array (
		'userenrolmentid' => $notification_data->userenrolmentid,
		'notification_type' => $notification_data->retraining_notification_type
	));

	if ($existing_data) { // Row exists, update data
		$retraining_notification_data->id = $existing_data->id;
		$DB->update_record("retraining_notifications", $retraining_notification_data);
	}
	else { // Row doesn"t exist, insert data
		$DB->insert_record("retraining_notifications", $retraining_notification_data);
	}

	return true;
}

/**
 *-----------------------------------------------------------------------------
 * Identify users that require retraining notification for a given course.
 *-----------------------------------------------------------------------------
 * @param  void
 * @return void
 *-----------------------------------------------------------------------------
 **/

function local_retraining_notifications_task() {

	$notifications = local_retraining_identify_users_for_retraining();

	// Send notifications where required that have not already been sent
	foreach ($notifications as $n) {
		local_retraining_send_notification_email($n);
		return ; // NOTE! ONLY RUNNING ONE AT A TIME HERE
	}

}

/**
 *-----------------------------------------------------------------------------
 * Check completion data
 *-----------------------------------------------------------------------------
 * @param  int $userid
 * @param  int $courseid
 * @param  bool $remove_enrolment flag
 * @return bool
 *-----------------------------------------------------------------------------
 **/

function local_retraining_check_completion_data($userid = NULL, $courseid = NULL, $remove_enrolment = FALSE) {

	global $DB;

	if (empty($userid) || empty($courseid)) {
		debugging("Missing input data userid=$userid and course=$courseid", DEBUG_MINIMAL);
		return false;
	}

  if ($remove_enrolment) {
    local_retraining_remove_enrolment($userid, $courseid);
  }

	$q = "
		select gg.*
		from {grade_grades} gg inner join {grade_items} gi
		on gg.itemid = gi.id
		and gg.userid = :userid
		and gi.courseid = :courseid
	";

	$grades_data = $DB->get_records_sql($q, array("userid" => $userid, "courseid" => $courseid));
	if ($grades_data) {
		local_retraining_archive_grades($userid, $courseid);
	}

	$completions_data = $DB->get_records(
		'course_completions',
		array('userid' => $userid, 'course' => $courseid)
	);

local_retraining_remove_scorm_progress($userid, $courseid);

	if ($completions_data) {
    local_retraining_archive_completions($userid, $courseid);
    local_retraining_archive_certificate_data($userid, $courseid);
		return TRUE;
	}
	else {
		debugging("No completions data found for userid=$userid and course=$courseid", DEBUG_ALL);
		return FALSE;
	}

}

/**
 *-----------------------------------------------------------------------------
 * Archive completions and reset enrolments
 *-----------------------------------------------------------------------------
 * @param  void
 * @return bool
 *-----------------------------------------------------------------------------
 **/

function local_retraining_archive_completions($userid, $courseid) {

	global $DB;

	if (empty($userid) || empty($courseid)) {
		error_log("[local_retraining_archive_completions] Incomplete data User ID=$userid and Course ID=$courseid");
		return false;
	}

	debugging("Archiving completions for userid=$userid in course=$courseid", DEBUG_MINIMAL);

	// Archive course completion into archives table
	$q = "insert into {archive_crse_comp}
		select
			null,
			id,
			userid,
			course,
			timeenrolled,
			timestarted,
			timecompleted,
			reaggregate
		from
			{course_completions}
		where
			userid = :userid
      and course = :courseid
      and timecompleted is not null
	";

	$DB->execute($q, array("userid" => $userid, "courseid" => $courseid));

	// Delete from course completions where archive was successful
	$q = "delete del from
		  {course_completions} del join {archive_crse_comp} arch
		  on del.id = arch.coursecompletionid
		  and del.userid = arch.userid
		  and del.course = arch.courseid
		  where arch.userid = :userid
		  and arch.courseid = :courseid";

	$DB->execute($q, array("userid" => $userid, "courseid" => $courseid));

	// Archive course completions criteria
	$q = "insert into {archive_crse_comp_crit}
		select
			null,
			id,
			userid,
			course,
			criteriaid,
			gradefinal,
			unenroled,
			timecompleted
		from {course_completion_crit_compl}
		where userid = :userid
    and course = :courseid
    and timecompleted is not null";

	$DB->execute($q, array("userid" => $userid, "courseid" => $courseid));

	// Delete from course completions where archive was successful
	$q = "delete del from
		  {course_completion_crit_compl} del join {archive_crse_comp_crit} arch
		  on del.id = arch.coursecompletioncritid
		  and del.userid = arch.userid
		  and del.course = arch.courseid
		  where arch.userid = :userid
		  and arch.courseid = :courseid";

	$DB->execute($q, array("userid" => $userid, "courseid" => $courseid));

	// Reset completions on all course modules for user

	$q = "update {course_modules_completion}
		  set completionstate = 0, viewed = 0
		  where userid = :userid
		  and completionstate = 1
		  and coursemoduleid in (
			select id
			from {course_modules}
			where course = :courseid
		  )
	";

	$DB->execute($q, array("userid" => $userid, "courseid" => $courseid));

	return true;
}

/**
 *-----------------------------------------------------------------------------
 * Is there issued certificate? Which certificate mod is it for?
 *-----------------------------------------------------------------------------
 * @param  int    $userid
 * @param  int    $courseid
 * @return string $tablename of certificate mod if data found
 *-----------------------------------------------------------------------------
 **/

function local_retraining_archive_certificate_data($userid, $courseid) {
    global $DB;
    $dbman = $DB->get_manager();

    if (empty($userid) || empty($courseid)) {
      error_log("[local_retraining_get_certificate_data] Incomplete data User ID=$userid and Course ID=$courseid");
      return "";
    }

    if ($dbman->table_exists(CERTIFICATE_TABLE)) {
      $tablename = CERTIFICATE_TABLE;
      $cert_table = "{".CERTIFICATE_TABLE."}";
      $cert_issues_table = "{".CERTIFICATE_TABLE . "_issues}";
    }
    else if($dbman->table_exists(SIMPLECERTIFICATE_TABLE)) {
      $tablename = SIMPLECERTIFICATE_TABLE;
      $cert_table = "{".SIMPLECERTIFICATE_TABLE."}";
      $cert_issues_table = "{".SIMPLECERTIFICATE_TABLE . "_issues}";
    }
    else {
      return ""; // Neither exist
    }

    // Check for certificate data for the user and course (not call courses have certificates)
		$q = "
			select *
			from
				$cert_issues_table ci inner join $cert_table c
				on ci.certificateid = c.id
			where
				ci.userid = :userid
				and c.course = :courseid
		";

    $certificate_data = $DB->get_records_sql($q, array("userid" => $userid, "courseid" => $courseid));
  	if ($certificate_data) {
		  local_retraining_archive_issued_certificates($userid, $courseid, $tablename);
		}
}

/**
 *-----------------------------------------------------------------------------
 * Archive issued certificates
 *-----------------------------------------------------------------------------
 * @param  int    $userid
 * @param  int    $courseid
 * @param  string $tablename (certificate vs simplecertificate)
 * @return bool
 *-----------------------------------------------------------------------------
 **/

function local_retraining_archive_issued_certificates($userid, $courseid, $tablename = 'certificate') {
  global $DB;

	if (empty($userid) || empty($courseid)) {
		error_log("[local_retraining_archive_issued_certificates] Incomplete data User ID=$userid and Course ID=$courseid");
		return false;
  }

  // For mod/certificate vs mod/simplecertificate
  $cert_table = "{". $tablename ."}";
  $cert_issues_table = "{". $tablename ."_issues}";

	// Archive issue certificate(s) into archive table
	$q = "insert into {archive_issued_certs}
		select
			null,
			ci.id as certissueid,
			ci.userid,
			ci.certificateid,
			ci.code,
			ci.timecreated
		from
			$cert_issues_table ci inner join $cert_table c
			on ci.certificateid = c.id
		where
			ci.userid = :userid
			and c.course = :courseid
	";

	$DB->execute($q, array("userid" => $userid, "courseid" => $courseid));

	// Remove issued certificate once archive has been completed
	// Note this SQL checks for an archive row before deleting, just in case!

	$q = "delete del from
		  $cert_issues_table del join {archive_issued_certs} arch
		  on del.id = arch.certissueid
		  and del.userid = arch.userid
		  and del.certificateid = arch.certificateid
		  and del.timecreated = arch.timecreated
		  where arch.userid = :userid";

	$DB->execute($q, array("userid" => $userid));

	return true;
}

/**
 *-----------------------------------------------------------------------------
 * Archive grades to grade history table
 *-----------------------------------------------------------------------------
 * @param  int  $userid
 * @param  int  $courseid
 * @return bool
 *-----------------------------------------------------------------------------
 **/

function local_retraining_archive_grades($userid, $courseid) {

	global $DB;

	if (empty($userid) || empty($courseid)) {
		error_log("[local_retraining_archive_grade] Incomplete data User ID=$userid and Course ID=$courseid");
		return false;
	}

	$q = "insert into {archive_grades}
		  select
			null,
			gg.itemid,
			gg.userid,
			gg.rawgrade,
			gg.usermodified,
			gg.finalgrade,
			gg.timecreated,
			gg.timemodified,
			gg.aggregationstatus,
			gg.aggregationweight
		  from
			{grade_grades} gg
		  where
			gg.userid = :userid
			and gg.itemid in (
				select gi.id
				from {grade_items} gi
				where gi.courseid = :courseid
		   )
		  ";

	$DB->execute($q, array("userid" => $userid, "courseid" => $courseid));

	// Remove issued certificate once archive has been completed
	// Note this SQL checks for an archive row before deleting, just in case!

	$q = "delete del from
		  {grade_grades} del join {archive_grades} arch
		  on del.itemid = arch.itemid
		  and del.userid = arch.userid
		  where arch.userid = :userid";

	$DB->execute($q, array("userid" => $userid));

	return true;
}

/**
 *-----------------------------------------------------------------------------
 * Remove an enrolment
 *-----------------------------------------------------------------------------
 * @param  (int)	User ID
 * @param  (int)	Course ID
 * @return (bool)	Result of operation
 *-----------------------------------------------------------------------------
 **/

function local_retraining_remove_enrolment($userid, $courseid) {
	global $CFG, $DB, $PAGE;
	require_once($CFG->dirroot . '/enrol/locallib.php');

	$q = "
		select * from mdl_user_enrolments
		where userid = :userid
		and enrolid = (
			select id
			from mdl_enrol
			where courseid = :courseid
			and enrol = 'manual'
		);
	";

	$params = array('userid' => $userid, 'courseid' => $courseid);
	$ue = $DB->get_record_sql($q, $params, '*', MUST_EXIST);
	$instance = $DB->get_record('enrol', array('id'=>$ue->enrolid), '*', MUST_EXIST);

	if ($ue && $instance) {
      $context = context_course::instance($courseid);
      $plugin = enrol_get_plugin($instance->enrol);

      if (!$plugin->allow_unenrol_user($instance, $ue) or !
       has_capability("enrol/$instance->enrol:unenrol", $context)) {
         print_error('erroreditenrolment', 'enrol');
      }

      $plugin->unenrol_user($instance, $userid);
		return true;
	}
	return false;
}

/**
 *-----------------------------------------------------------------------------
 * Remove SCORM progress
 *-----------------------------------------------------------------------------
 * @param		int $userid
 * @param		int $courseid
 * @return  bool
 *-----------------------------------------------------------------------------
 **/

 function local_retraining_remove_scorm_progress($userid, $courseid) {
	global $DB;

	global $CFG, $DB, $PAGE;
	require_once($CFG->dirroot . '/enrol/locallib.php');

	$q = "
		delete from {scorm_scoes_track}
		where userid = :userid
		and scormid in (
			select instance
			from {course_modules}
			where course = :courseid
			and module = (
				select id
				from {modules}
				where name = :modulename
			)
		)
	";

	$p = array(
		'userid' => $userid,
		'courseid' => $courseid,
		'modulename' => 'scorm'
	);

	try {
		$DB->execute($q, $p);
		return true;
	}
	catch (Exception $e) {
		error_log("[local_retraining_remove_scorm_progress] database exception");
		error_log(print_r($e, 1));
		return false;
	}

 }
