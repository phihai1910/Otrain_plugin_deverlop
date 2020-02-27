<?php 
	$mime_boundary = md5() ;

	$header .= "MIME-Version: 1.0\r\n";
	$header .= "From: info@otrainu.com \r\n";
	$header .= "To: ".$to."	\r\n";
	$header .= "Content-type: multipart/mixed; boundary=\"".$mime_boundary."\"\r\n\r\n";

	$html_message = 'The document '. $course->fullname.'_copy.pdf is completed. Here is a copy of the completed document.';
	$nmessage = "--".$mime_boundary."\r\n";
	$nmessage .= "Content-type:text/html ;charset=UTF-8\r\n";
	$nmessage .= $html_message."\r\n\r\n"; //Your message



	$nmessage .= "--".$mime_boundary."\r\n";
	$nmessage .= $pdf_copy."\r\n\r\n"; //adding Your PDF file
	// $nmessage .= "--".$mime_boundary."--" ;
	$nmessage .= "--".$mime_boundary."\r\n";
	$nmessage .= $pdf_certification."\r\n\r\n"; //adding Your PDF file
	$nmessage .= "--".$mime_boundary."--" ;
	// var_dump( $nmessage  );
	$mail = mail($to, 'Document '.$course->fullname.'copy.pdf has been completed'  , $nmessage , $header);
	if($email != '' ){
	$mail = mail($email, 'Document '.$course->fullname.'copy.pdf has been completed'  , $nmessage , $header);
	}
		
?>