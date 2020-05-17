<?php 



$pdf_certificate = new pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf_certificate->SetTitle('Signature certificate');
$pdf_certificate->SetSubject('Signature');

$pdf_certificate->SetHeaderData($CFG->root.'/mod/signature/demo.png' , 30  );

$pdf_certificate->AddPage();


$pdf_certificate->WriteHTML('<h1 style="text-align:center">Certificate of Completion</h1>');
$pdf_certificate->WriteHTML('<h3>Summary</h3>'); 
$pdf_certificate->Ln(5);
$pdf_certificate->WriteHTML('<div>
	<table>
		<tr>
			<td '.$widthcell.' >Sent on</td>
			<td>'.		$timestart .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Completed on</td>
			<td>'.  $timecreate.'</td>
		</tr>
	</table>
</div>');
$pdf_certificate->Ln(10);
$pdf_certificate->WriteHTML('<h3>Recipients</h3>');
$pdf_certificate->Ln(5); 


		
$pdf_certificate->WriteHTML('<div>
	<table border="1" cellpadding="10" >
		<tr><td colspan="2">'. $USER->firstname.' '. $USER->lastname .' ( '. $USER->email.' )</td></tr>
		<tr>
			<td '.$widthcell.' >View on</td>
			<td>'.		$timestart .'</td>
			<td rowspan="4">'. $img .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Signed on</td>
			<td>'. $timecreate  .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Accessed from</td>
			<td>'. $ip .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Device used</td>
			<td>'. $mobile .'</td>
		</tr>
	</table>

</div>');



?>