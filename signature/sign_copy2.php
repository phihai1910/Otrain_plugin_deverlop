<?php 
$tmpfilename = get_temp_file($moduleinstance );
// unlink sau khi xai 
$pdf = new FPDI();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pagecount = $pdf->setSourceFile($tmpfilename);
for( $pageNo = 1 ; $pageNo <= $pagecount ; $pageNo++ ){
	
	$tplIdx = $pdf->importPage($pageNo);
	 $size = $pdf->getTemplateSize($tplIdx);
	
	 if ($size['w'] > $size['h']) {
        $pdf->AddPage('L', array($size['w'], $size['h']));
    } else {
        $pdf->AddPage('P', array($size['w'], $size['h']));
    }
	 $pdf->useTemplate($tplIdx);
	if( $pageNo == $moduleinstance->pagenumber)  {
		// $pdf->SetFont('Helvetica');
		// $pdf->SetTextColor(255, 0, 0);
		$pdf->SetXY($moduleinstance->x, $moduleinstance->y);

		// $pdf->Image($signature, 30 , 30);
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
	}
	
}


@unlink($tmpfilename);


?>