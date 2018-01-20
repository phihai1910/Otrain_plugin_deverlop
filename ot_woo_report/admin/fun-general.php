<?php
if( !function_exists('pr') ){
	function pr($array){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}
if( !function_exists('csv_to_array') ){
	function csv_to_array($csv_file){
		$results = array();
		$row = 0;
		if (($handle = fopen($csv_file, "r")) !== FALSE) {
			$fields = fgetcsv($handle, 1000, ";");	
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				$num = count($data);
				for ($c=0; $c < $num; $c++) {
					$results[$row][$fields[$c]] =  $data[$c];
				}
				$row++;
			}
			fclose($handle);
		}
		return $results;
	}
}

class TM_general {
	static function write_log_cron($log){
		$log = $log."\n";
		$put = @file_put_contents(TML_DIR.'/tmp/log.txt', $log, FILE_APPEND);
		if( $put ){
			return 1;
		}
		return 0;
	}
	static function write_log_user($log){
		$log = $log."\n";
		$put = @file_put_contents(TML_DIR.'/tmp/customer_log.txt', $log, FILE_APPEND);
		if( $put ){
			return 1;
		}
		return 0;
	}
	
	static function create_products_cvs_file( $products ,$i = 0 ){
		$csv 		= new arrayToCsv();
		$csv_string = $csv->convert($products);
		$put = @file_put_contents(TML_DIR.'/tmp/products_'.$i.'.csv', $csv_string);
		if( $put ){
			return 1;
		}
		return 0;
	}
	static function create_categories_cvs_file( $categories){
		$csv 		= new arrayToCsv();
		$csv_string = $csv->convert($categories);
		$put = @file_put_contents(TML_DIR.'/tmp/categories.csv', $csv_string);
		if( $put ){
			return 1;
		}
		return 0;
	}

	static function csv_to_array($csv_file){
		$results = array();
		$row = 0;
		if (($handle = fopen($csv_file, "r")) !== FALSE) {
			$fields = fgetcsv($handle, 1000, ";");	
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				$num = count($data);
				for ($c=0; $c < $num; $c++) {
					$results[$row][$fields[$c]] =  $data[$c];
				}
				$row++;
			}
			fclose($handle);
		}
		return $results;
	}
}

class arrayToCsv{
		protected $delimiter;
		protected $text_separator;
		protected $replace_text_separator;
		protected $line_delimiter;
		public function __construct($delimiter = ";", $text_separator = '"', $replace_text_separator = "'", $line_delimiter = "\n"){
			$this->delimiter              = $delimiter;
			$this->text_separator         = $text_separator;
			$this->replace_text_separator = $replace_text_separator;
			$this->line_delimiter         = $line_delimiter;
		}
		public function convert($input) {
			
			$lines = array();
			$fields = array();
			foreach ($input as $key => $v) {
				if( count( $fields ) == 0 ){
					foreach ($v as $field => $value){
						$fields[0][] = $field;
					}
				}
			}
			
			$input = array_merge($fields,$input);
			foreach ($input as $key => $v) {
				$lines[] = $this->convertLine($v);
			}
			return implode($this->line_delimiter, $lines);
		}
		private function convertLine($line) {
			$csv_line = array();
			foreach ($line as $v) {
				$csv_line[] = is_array($v) ? 
						$this->convertLine($v) : 
						$this->text_separator . str_replace($this->text_separator, $this->replace_text_separator, $v) . $this->text_separator;
			}
			
			return implode($this->delimiter, $csv_line);
		}
	}

