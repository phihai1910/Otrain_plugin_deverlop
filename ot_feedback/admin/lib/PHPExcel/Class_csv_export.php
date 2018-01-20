<?php
include 'PHPExcel.php'; 
Class csv_export{
	
	public $_header = array();
	public $_content = array();
	public $objPHPExcel;
	
	public function __construct($header = array(), $content = array() ) {
		$this->_header = $header;
		$this->_content = $content;
		$this->objPHPExcel = new PHPExcel();
	}
	
	public function create_csv($content, $link){
		
		$this->objPHPExcel->getProperties()->setCreator("Otrain");
		// $this->objPHPExcel  = $this->set_excel_header($this->objPHPExcel,$header);
		$this->objPHPExcel  = $this->set_excel_content($this->objPHPExcel,$content);
		
		$this->objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		$name = 'Order export date '; 
		
		$objWriter->save( $link);
		/*
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="Report.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');

		exit;
		*/
	}
	
	public function set_excel_content($objPHPExcel,$content){
		
		foreach( $content as $row => $value)
		{
			$col = 0;
			foreach($value as $k => $v){
				
				$this->objPHPExcel->setActiveSheetIndex(0)
					->setCellValueByColumnAndRow( $col ,$row+1, $v);
				$col++;	
			}
			
		}
		
		return $this->objPHPExcel;
	}
	
}
