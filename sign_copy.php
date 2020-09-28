<?php 



$pdf = new pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Signature Copy');
$pdf->SetSubject('Signature Copy');

$pdf->SetHeaderData($CFG->root.'/mod/signature/demo.png' , 30  );

$pdf->AddPage();


$pdf->WriteHTML('<h1 style="text-align:center">'.$course->fullname.'</h1>');
$pdf->Ln(5);
$pdf->WriteHTML($content);
$pdf->Ln(5);
$pdf->WriteHTML('
	<table>
		<tr>
			<td '.$widthcell.' >Name: </td>
			<td>'.	$name .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Date: </td>
			<td>'. 	$timecreate .'</td>
		</tr>
		<tr>
			<td '.$widthcell.' >Signature: </td>
			<td>'. 	$img  .'</td>
		</tr>
	</table>
');
