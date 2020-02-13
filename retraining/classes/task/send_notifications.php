<?php

/**
 *-----------------------------------------------------------------------------
 * Retraining Notification
 *-----------------------------------------------------------------------------
 * Extend scheduled task class for custom schedule task
 *
 * @package     local_retraining
 * @copyright   OTrain Pty Ltd <http://www.otrain.com.au>
 *-----------------------------------------------------------------------------
 **/

namespace local_retraining\task;

global $CFG;

require_once("$CFG->libdir/../local/retraining/lib.php");

class send_notifications extends \core\task\scheduled_task {

    public function get_name() {
        return get_string("retrainingnotificationstask", "local_retraining");
    }

    public function execute() {
    	local_retraining_notifications_task();
    }
}