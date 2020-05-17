<?php 


	$signatureid = $DB->insert_record( 'signature_issues', 
		array( 	'userid' => $USER->id, 
				'signatureid' => $cm->id , 
				'signaturename' => $filename,
				'code' => get_issue_uuid(),
				'timecreated' => time(),
				// 'pathnamehash' =>$file->get_pathnamehash()
	));
	$fileinfo = array('contextid' => $cm->id,
			'component' => 'mod_signature',
			'filearea' => 'content',
			'itemid' => $signatureid,
			'filepath' => '/',
			'mimetype' => 'application/pdf',
			'userid' => $USER->id,
			'filename' =>$filename.'.pdf'
	);

	$fs = get_file_storage();
	$file = $fs->create_file_from_string($fileinfo, $pdf->Output('', 'S'));
	
	$signclass = new stdClass();
	$signclass->id = $signatureid ;
	$signclass->pathnamehash =   $file->get_pathnamehash();
	
	$fileinfo = array('contextid' => $cm->id,
			'component' => 'mod_signature',
			'filearea' => 'content_copy',
			'itemid' => $signatureid,
			'filepath' => '/',
			'mimetype' => 'application/pdf',
			'userid' => $USER->id,
			'filename' =>$filename.'_signature_completion_certificate.pdf'
	);
	
	$fs2 = get_file_storage();
	
	$file2 = $fs2->create_file_from_string($fileinfo, $pdf_certificate->Output('', 'S'));
	
	$signclass->pathnamehash2 =   $file2->get_pathnamehash();
	$signclass->coursemodule = $moduleinstance->name;
	$DB->update_record( 'signature_issues' , $signclass) ;
	
	
	?>