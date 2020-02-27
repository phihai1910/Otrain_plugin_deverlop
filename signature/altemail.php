<?php 
require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once($CFG->libdir.'/pdflib.php');
$id = required_param('id', PARAM_TEXT); // Issued Code.
$issuedcert = $DB->get_record("signature_issues", array('id' => $id));
if (!$issuedcert) {
    echo 'error';
} else {
    send_certificate_file($issuedcert);
?>