# OVERVIEW

What does your plugin do?

# INSTALLATION

1. Copy plugin to `local/retraining` in Moodle home directory
2. Install the plugin
3. Additional steps after installation

Core patches required:

## course/edit_form.php

Add the following to the definition() of the edit form, after the last item (Tags) or as appropriate depending on preferred placement. It creates a separate
retraining notification header.

```
        // -----------------------------------------------------------------------------
        // BEGIN: OTrain Customisation - local_retraining plugin
        // Custom fields retraining notification stored in course_custom table

        require_once('../local/retraining/lib.php');
        local_retraining_custom_course_fields($mform, $course->id);

        // END: OTrain Customisation - local_retraining plugin
        // -----------------------------------------------------------------------------
```

## course/edit.php

Add this just after else / update_course data call for edit form.

```
else {
        // Save any changes to the files used in the editor.
        update_course($data, $editoroptions);

        // -----------------------------------------------------------------------------
        // BEGIN: Customisation - local_retraining plugin

        require_once('../local/retraining/lib.php');
        local_retraining_save_notification_data($data);
 
        // END: Customisation - local_retraining plugin
        // -----------------------------------------------------------------------------
```

# USAGE

The plugin will add a link in the administration menu:
Site Administration > Retraining Notification

# CREDITS

# FOR version 3.5 base asthma online

user/classes/participants_table.php


---------------

private $retraining_enabled = false;

-------------
   public function __construct($courseid, $currentgroup, $accesssince, $roleid, $enrolid, $status, $search,
            $bulkoperations, $selectall) {
        global $CFG,$DB;




-----------
 $canreviewenrol = has_capability('moodle/course:enrolreview', $context);
if ($canreviewenrol && $courseid != SITEID) {
	$columns[] = 'status';
	$headers[] = get_string('participationstatus', 'enrol');
	$this->no_sorting('status');
};


// BEGIN: OTrain Customisation
// Is this a retraining course?
$course_custom = $DB->get_record(
  'course_custom',
  array('courseid' => $courseid, 'retraining_required' => 1)
);

// $retraining_enabled = false;
if ($course_custom) {
  $this->retraining_enabled = true;
  $columns[] = 'archive';
	$headers[] = 'Archive';
}
// END: OTrain Customisation





$this->define_columns($columns);
$this->define_headers($headers);

--------------------


	/* OTrain custome function for archive re-enrol */
	public function col_archive($data){
		
		if( $this->retraining_enabled ){
			
			$args = array('userid' => $data->id, 'courseid' => $this->course->id, 'reenrol' => 1);
			
			return '<a href="'. $CFG->wwwroot.'/local/retraining/start.php?'.http_build_query($args) .'">Re-enrol</a>';
		}
		return '';
	}




© OTrain <http://www.otrain.com.au>
