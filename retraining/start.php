<?php

/**
 *-----------------------------------------------------------------------------
 * Retraining Notification
 *-----------------------------------------------------------------------------
 * Link to start retraining for a new user. Archives completion for the given
 * user ID and course ID (required parameters).
 *
 * @package     local_retraining
 * @copyright   OTrain Pty Ltd <http://www.otrain.com.au>
 *-----------------------------------------------------------------------------
 **/

require_once('../../config.php');
require_once('lib.php');

//-----------------------------------------------------------------------------
// Get required parameters
//-----------------------------------------------------------------------------

global $DB;

$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$reenrol = optional_param('reenrol', 0, PARAM_INT);

$r = $DB->get_record('course_custom', array('courseid' => $courseid));
if ($r->course_url) {
  local_retraining_check_completion_data($userid, $courseid, true);
	redirect($r->course_url);
}
else {
  local_retraining_check_completion_data($userid, $courseid, false);
  // Manual re-enrolment
  if ($reenrol == 1) {
    redirect("/user/index.php?id=$courseid");
  }
  else {
    redirect("/");
  }
}