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
 * The main mod_signature configuration form.
 *
 * @package     mod_signature
 * @copyright   2020 OTrain <tech@otrain.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_signature
 * @copyright  2020 OTrain <tech@otrain.com.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_signature_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG,$COURSE;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('signaturename', 'mod_signature'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'signaturename', 'mod_signature');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of mod_signature settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('static', 'label1', 'signaturesettings', get_string('signaturesettings', 'mod_signature'));
        $mform->addElement('header', 'signaturefieldset', get_string('signaturefieldset', 'mod_signature'));
		
	
		
		$mform->addElement('editor', 'signature_content',  get_string('signaturecontent', 'mod_signature') , array('rows' => 10), array('maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true, 'context' => $this->context, 'subdirs' => true ,'enable_filemanagement' => true ,  'removeorphaneddrafts' => true ,  'return_types' =>  FILE_INTERNAL
			
			
			));
        $mform->setType('signature_content', PARAM_RAW); // no XSS prevention here, users must be trusted
        if ($required) {
            $mform->addRule('signature_content', get_string('required'), 'required', null, 'client');
        }
		$fileoptions = array('subdirs'=>0,
                                'maxbytes'=>$COURSE->maxbytes,
                                'accepted_types'=>'pdf',
                                'maxfiles'=>1,
                                'return_types'=>FILE_INTERNAL);
		$mform->addElement('filepicker', 'userfile', get_string('file'), null,
                  $fileoptions );
		$mform->addElement('text' , 'pagenumber', 'Page sign' );
		$mform->addElement('text' , 'x' , 'Location x');
		$mform->addElement('text' , 'y' , 'Location Y');
		
		
		// $option = array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 50,
                          // 'accepted_types' => array('pdf') );

        // $mform->addElement('filemanager', 'managerfiles', get_string('selectfiles'), null, $options);
		$mform->addElement('text', 'email', 'Email receive certification');
		$mform->setType( 'email', PARAM_NOTAGS);
		$mform->setDefault('email', 'Please enter email');  
        // Add standard grading elements.
        // $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
	   public function add_completion_rules() {
        $mform =& $this->_form;

        $mform->addElement('advcheckbox', 'completionsubmit', '', get_string('completionsubmit', 'assign'));
        // Enable this completion rule by default.
        $mform->setDefault('completionsubmit', 1);
        return array('completionsubmit');
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionsubmit']);
    }
	   public function data_preprocessing(&$defaultvalues) {
		   
		   $defaultvalues['userfile'] = $defaultvalues['userfile'];
		   $defaultvalues['signature_content'] = array(
			'text' => $defaultvalues['signature_content']
		   );
		   
	   }
}
