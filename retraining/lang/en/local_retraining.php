<?php

/**
 *-----------------------------------------------------------------------------
 * Retraining Notification
 *-----------------------------------------------------------------------------
 * Language pack
 *
 * @package     local_retraining
 * @copyright   OTrain Pty Ltd <http://www.otrain.com.au>
 *-----------------------------------------------------------------------------
 **/

$string["pluginname"] = "Retraining Notification";

$string["retrainingnotificationstask"] = "Retraining notifications schedule task";

// Retraining general settings
$string["retraininggeneralsubheading"] = "<h5 class='retraining'>General settings</h5>";

$string["retrainingcourseurl"] = "Course URL to purchase course";
$string["retrainingcourseurl_help"] = "Enter the course product url to
send the user to purchase the course again once the retraining notification has been sent.";

$string["retrainingrequired"] = "Retraining required?";
$string["retrainingrequired_help"] = "Does this course require regular retraining?";

$string["retrainingfrequency"] = "Retraining frequency (days)";
$string["retrainingfrequency_help"] = "How often is retraining required (days since completing last training)";

// -----------------------------------------------------------------------------
// First notification settings
// -----------------------------------------------------------------------------

$string["retrainingfirstnotificationsubheading"] = "<h5 class='retraining'>First notification settings</h5>";

$string["retrainingfirstnotice"] = "First notice (days prior)";
$string["retrainingfirstnotice_help"] = "How many days before retraining due date should the first notice be sent? (Default 60 days before)";

$string["retrainingfirstnoticesubject"] = "First notification subject";
$string["retrainingfirstnoticesubjectdefault"] = "{site} Retraining: First notification for {course}";
$string["retrainingfirstnoticesubject_help"] = "Enter the subject to appear as the email subject for the first notification email.\n
You can use placeholders such as:\n
- {site} the short name of the site\n
- {course} the name of the course
";

$string["retrainingfirstnoticemessage"] = "First notification message";
$string["retrainingfirstnoticemessagedefault"] = "Hello {firstname}\n
This is to advise that you are 60 days from being required to commence retraining in {course}.\n
Please login to {retrainingurl} and commence your retraining today.
"; // TODO
$string["retrainingfirstnoticemessage_help"] = "Enter the message to appear as the email body/message for the first notification email.\n
You can use placeholders such as:\n
- {firstname} the user's first name\n
- {lastname} the user's last name\n
- {site} the short name of the site\n
- {retrainingurl} the retraining URL to commence retraining (archives completion data)\n
- {course} the name of the course
"; // TODO

// -----------------------------------------------------------------------------
// Second notification setttings
// -----------------------------------------------------------------------------

$string["retrainingsecondnotificationsubheading"] = "<h5 class='retraining'>Second notification settings</h5>";

$string["retrainingsecondnotice"] = "Second notice (days prior)";
$string["retrainingsecondnotice_help"] = "How many days before retraining due date should the second notice be sent? (Default 30 days before)";

$string["retrainingsecondnoticesubject"] = "Second notification subject";
$string["retrainingsecondnoticesubjectdefault"] = "{site} Retraining: Second notification for {course}";
$string["retrainingsecondnoticesubject_help"] = "Enter the subject to appear as the email subject for the second notification email.\n
You can use placeholders such as:\n
- {site} the short name of the site\n
- {course} the name of the course
";

$string["retrainingsecondnoticemessage"] = "Second notification message";
$string["retrainingsecondnoticemessagedefault"] = "Hello {secondname}\n
This is to advise that you are 30 days from being required to commence retraining in {course}.\n
Please login to {retrainingurl} and commence your retraining today.
"; // TODO
$string["retrainingsecondnoticemessage_help"] = "Enter the message to appear as the email body/message for the second notification email.\n
You can use placeholders such as:\n
- {secondname} the user's second name\n
- {lastname} the user's last name\n
- {site} the short name of the site\n
- {retrainingurl} the retraining URL to commence retraining (archives completion data)\n
- {course} the name of the course
";

// Third ... Ninth notification settings?

// -----------------------------------------------------------------------------
// Final notification settings
// -----------------------------------------------------------------------------

// Final notification setttings

$string["retrainingfinalnotificationsubheading"] = "<h5 class='retraining'>Final notification settings</h5>";

$string["retrainingfinalnotice"] = "Final notice (days prior)";
$string["retrainingfinalnotice_help"] = "How many days before retraining due date should the final notice be sent? (Default 30 days before)";

$string["retrainingfinalnoticesubject"] = "Final notification subject";
$string["retrainingfinalnoticesubjectdefault"] = "{site} Retraining: Final notification for {course}";
$string["retrainingfinalnoticesubject_help"] = "Enter the subject to appear as the email subject for the final notification email.\n
You can use placeholders such as:\n
- {site} the short name of the site\n
- {course} the name of the course
";

$string["retrainingfinalnoticemessage"] = "Final notification message";
$string["retrainingfinalnoticemessagedefault"] = "Hello {finalname}\n
This is to advise that you are 30 days from being required to commence retraining in {course}.\n
Please login to {retrainingurl} and commence your retraining today.
"; // TODO
$string["retrainingfinalnoticemessage_help"] = "Enter the message to appear as the email body/message for the final notification email.\n
You can use placeholders such as:\n
- {finalname} the user's final name\n
- {lastname} the user's last name\n
- {site} the short name of the site\n
- {retrainingurl} the retraining URL to commence retraining (archives completion data)\n
- {course} the name of the course
";