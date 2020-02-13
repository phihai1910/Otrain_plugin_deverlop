<?php

/**
 *-----------------------------------------------------------------------------
 * Retraining Notification
 *-----------------------------------------------------------------------------
 * Schedule task
 *
 * @package     local_retraining
 * @copyright   OTrain Pty Ltd <http://www.otrain.com.au>
 *-----------------------------------------------------------------------------
 **/

defined('MOODLE_INTERNAL') || die();

// By default the retraining notifications task runs every 24 hrs (every day)
$tasks = array(
    array(
        'classname' => 'local_retraining\task\send_notifications',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    )
);