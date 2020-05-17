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
 * Library of interface functions and constants.
 *
 * @package     mod_signature
 * @copyright   2020 OTrain <tech@otrain.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// include_once 'includes/jSignature_Tools_Base30.php';
// include_once 'includes/jSignature_Tools_SVG.php';
defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
 /* not use 
 function base30_to_jpeg($base30_string, $output_file) {

    $data = str_replace('image/jsignature;base30,', '', $base30_string);
    $converter = new jSignature_Tools_Base30();
    $raw = $converter->Base64ToNative($data);
//Calculate dimensions
$width = 0;
$height = 0;
foreach($raw as $line)
{
    if (max($line['x'])>$width)$width=max($line['x']);
    if (max($line['y'])>$height)$height=max($line['y']);
}

// Create an image
    $im = imagecreatetruecolor($width+20,$height+20);


// Save transparency for PNG
    imagesavealpha($im, true);
// Fill background with transparency
    $trans_colour = imagecolorallocatealpha($im, 255, 255, 255, 127);
    imagefill($im, 0, 0, $trans_colour);
// Set pen thickness
    imagesetthickness($im, 2);
// Set pen color to black
    $black = imagecolorallocate($im, 0, 0, 0);   
// Loop through array pairs from each signature word
    for ($i = 0; $i < count($raw); $i++)
    {
        // Loop through each pair in a word
        for ($j = 0; $j < count($raw[$i]['x']); $j++)
        {
            // Make sure we are not on the last coordinate in the array
            if ( ! isset($raw[$i]['x'][$j])) 
                break;
            if ( ! isset($raw[$i]['x'][$j+1])) 
            // Draw the dot for the coordinate
                imagesetpixel ( $im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $black); 
            else
            // Draw the line for the coordinate pair
            imageline($im, $raw[$i]['x'][$j], $raw[$i]['y'][$j], $raw[$i]['x'][$j+1], $raw[$i]['y'][$j+1], $black);
        }
    } 

//Create Image
    $ifp = fopen($output_file, "wb"); 
    imagepng($im, $output_file);
    fclose($ifp);  
    imagedestroy($im);

    return $output_file; 
}
function signature_supports($feature) {
    switch ($feature) {
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

*/
/**
 * Saves a new instance of the mod_signature into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_signature_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function signature_add_instance($moduleinstance, $mform = null) {
    global $DB;
	
    $moduleinstance->timecreated = time();
	/* modifier database field */
	$moduleinstance->signature_content = $moduleinstance->signature_content['text'];
	// $moduleinstance->userfile = $moduleinstance->userfile;	
	
	$moduleinstance->filename = $mform->get_new_filename('userfile');
	// store file 
	// $context = context_course::instance($moduleinstance->course);
	
	 $context = context_system::instance();
	 $contextid = $context->id;
    $id = $DB->insert_record('signature', $moduleinstance);
	
	
	$mform->save_stored_file('userfile',
				    $contextid,
					'mod_signature',
					'content',
					$moduleinstance->userfile,
					'/',
					$mform->get_new_filename('userfile'),
					true);
					
	return $id;
}

/**
 * Updates an instance of the mod_signature in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_signature_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function signature_update_instance($moduleinstance, $mform = null) {
    global $DB,$USER;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;
	$moduleinstance->filename = $mform->get_new_filename('userfile');
	/* modifier database field */
	$moduleinstance->signature_content = $moduleinstance->signature_content['text'];
	
	
	// var_dump($moduleinstance->coursemodule);	
	// var_dump( context_module::instance($moduleinstance->coursemodule));
	// exit();
	// $context = context_system::instance();
	$context =context_module::instance($moduleinstance->coursemodule);
	 $contextid = $context->id;
	$mform->save_stored_file('userfile',
                                                       $contextid ,
                                                        'mod_signature',
                                                        'content',
                                                        $moduleinstance->userfile,
                                                        '/',
                                                        $mform->get_new_filename('userfile'),
                                                        true);
	

	
	
	
	//store file 
	// $data = $mform->get_data()
	$context = context_course::instance($moduleinstance->course);
	$contextid = $context->id;										
	$draftitemid = file_get_submitted_draft_itemid('managerfiles');

	file_save_draft_area_files($draftitemid, $context->id, 'mod_signature', 'attachment', $moduleinstance->id ,
                        array('subdirs' => 0, 'maxbytes' => $maxbytes, 'maxfiles' => 50));

    return $DB->update_record('signature', $moduleinstance);
}

/**
 * Serves the files from the mod_signature file areas.
 *
 * @package     mod_signature
 * @category    files
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The course module object.
 * @param stdClass $context The mod_signature's context.
 * @param string $filearea The name of the file area.
 * @param array $args Extra arguments (itemid, path).
 * @param bool $forcedownload Whether or not force download.
 * @param array $options Additional options affecting the file serving.
 */
function mod_signature_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options = array()) {
    global $DB, $CFG;
	
    // if ($context->contextlevel != CONTEXT_MODULE) {
        // send_file_not_found();
    // }

    require_login($course, true, $cm);
	// Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.
 
    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.
 
    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }
	// $context = context_course::instance($cm->course);
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
	// $file = $fs->get_file_by_id(46076);
	// send_stored_file($file, 86400, 0, $forcedownload, $options);	
    // send_file_not_found();
	// var_dump($file);
	// exit();
	// var_dump($context->id, 'mod_signature', $filearea, $itemid, $filepath, $filename);
    $file = $fs->get_file($context->id, 'mod_signature', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
	}
	send_stored_file($file, 86400, 0, $forcedownload, $options);	
    send_file_not_found();
	return $file;
	
	// send_stored_file($file, 86400, 0, $forcedownload, $options);	
    // send_file_not_found();
}





/**
 * Removes an instance of the mod_signature from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function signature_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('signature', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('signature', array('id' => $id));

    return true;
}

/**
 * Is a given scale used by the instance of mod_signature?
 *
 * This function returns if a scale is being used by one mod_signature
 * if it has support for grading and scales.
 *
 * @param int $moduleinstanceid ID of an instance of this module.
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by the given mod_signature instance.
 */
function signature_scale_used($moduleinstanceid, $scaleid) {
    global $DB;

    if ($scaleid && $DB->record_exists('signature', array('id' => $moduleinstanceid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of mod_signature.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param int $scaleid ID of the scale.
 * @return bool True if the scale is used by any mod_signature instance.
 */
function signature_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('signature', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the given mod_signature instance.
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param bool $reset Reset grades in the gradebook.
 * @return void.
 */
function signature_grade_item_update($moduleinstance, $reset=false) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($moduleinstance->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;

    if ($moduleinstance->grade > 0) {
        $item['gradetype'] = GRADE_TYPE_VALUE;
        $item['grademax']  = $moduleinstance->grade;
        $item['grademin']  = 0;
    } else if ($moduleinstance->grade < 0) {
        $item['gradetype'] = GRADE_TYPE_SCALE;
        $item['scaleid']   = -$moduleinstance->grade;
    } else {
        $item['gradetype'] = GRADE_TYPE_NONE;
    }
    if ($reset) {
        $item['reset'] = true;
    }

    grade_update('/mod/signature', $moduleinstance->course, 'mod', 'mod_signature', $moduleinstance->id, 0, null, $item);
}

/**
 * Delete grade item for given mod_signature instance.
 *
 * @param stdClass $moduleinstance Instance object.
 * @return grade_item.
 */
function signature_grade_item_delete($moduleinstance) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('/mod/signature', $moduleinstance->course, 'mod', 'signature',
                        $moduleinstance->id, 0, null, array('deleted' => 1));
}

/**
 * Update mod_signature grades in the gradebook.
 *
 * Needed by {@link grade_update_mod_grades()}.
 *
 * @param stdClass $moduleinstance Instance object with extra cmidnumber and modname property.
 * @param int $userid Update grade of specific user only, 0 means all participants.
 */
function signature_update_grades($moduleinstance, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    // Populate array of grade objects indexed by userid.
    $grades = array();
    grade_update('/mod/signature', $moduleinstance->course, 'mod', 'mod_signature', $moduleinstance->id, 0, $grades);
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}.
 *
 * @package     mod_signature
 * @category    files
 *
 * @param stdClass $course.
 * @param stdClass $cm.
 * @param stdClass $context.
 * @return string[].
 */
function signature_get_file_areas($course, $cm, $context) {
	$fs = get_file_storage();
	$context = context_course::instance($cm->course);
	$files = $fs->get_area_files($context->id, 'mod_signature', 'content', 0); 
	
    return array();
}

/**
 * File browsing support for mod_signature file areas.
 *
 * @package     mod_signature
 * @category    files
 *
 * @param file_browser $browser.
 * @param array $areas.
 * @param stdClass $course.
 * @param stdClass $cm.
 * @param stdClass $context.
 * @param string $filearea.
 * @param int $itemid.
 * @param string $filepath.
 * @param string $filename.
 * @return file_info Instance or null if not found.
 */
function signature_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Extends the global navigation tree by adding mod_signature nodes if there is a relevant content.
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $signaturenode An object representing the navigation tree node.
 * @param stdClass $course.
 * @param stdClass $module.
 * @param cm_info $cm.
 */
function signature_extend_navigation($signaturenode, $course, $module, $cm) {
}

/**
 * Extends the settings navigation with the mod_signature settings.
 *
 * This function is called when the context for the page is a mod_signature module.
 * This is not called by AJAX so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $signaturenode {@link navigation_node}
 */
function signature_extend_settings_navigation($settingsnav, $signaturenode = null) {
	global $PAGE;
	$url = new moodle_url('/mod/signature/view2.php', array('id'=>$PAGE->cm->id));
$node = navigation_node::create('View issues Certification',
		new moodle_url($url, array('mode'=>'group')),
		navigation_node::TYPE_SETTING, null, 'mod_quiz_groupoverrides');
$signaturenode->add_node($node, 1);
	
}


function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}

function get_temp_file( $moduleinstance ){
	global $DB;
	$pathnamehash = $DB->get_record_sql("SELECT pathnamehash FROM {files} WHERE itemid = ? AND filename <> '.'" , array( $moduleinstance->userfile ) );
	
	if( $pathnamehash ){
		$fs = get_file_storage();
		$file = $fs->get_file_by_hash( $pathnamehash->pathnamehash);
		 $tmpfilename = $file->copy_content_to_temp();
		 
		 return $tmpfilename ;
	}
	
	return false ;
}

function get_issue_uuid() {
        global $CFG;
        require_once($CFG->libdir . '/horde/framework/Horde/Support/Uuid.php');
        return (string)new Horde_Support_Uuid();
    }
