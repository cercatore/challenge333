<?php

class ParseResult{
	public $fullname;
	public $email;
	public $phone;
	public $linkedin;
	public $nationality;
	public $remainder;
}
class Parser {

 public function parse($spidered){
	$file = file($spidered);
	$count = 0;
	$fullname = null;
	$nationality = null;
	$fullmull = null;
	$education = null;
	$mobile = null;
	$email = null;
	$phone = null;
	$end_education = null;
	$linkedin = null;
	$backup = null;$exit= 0;
	$start_cut = 0;
	$end_cut = null;
	
	$preg_phone = "/^[\+0-9\-\(\)\s]*$/";
	foreach($file as $line){
		
		$fullmull = $fullmull . $line;
		if (preg_match('/education/i', $line) && !$end_education)
			$end_education = strlen($fullmull);
		
		if (preg_match('/education and training/i', $line)){
			$end_education = strlen($fullmull);
		}
		
		if (!$fullname){
			$ind = strpos($line, 'PERSONAL INFORMATION' );
			$fullname = substr($line , $ind + 20 );
			}
		if (preg_match('/PERSONAL INFORMATION/', $line) ){
			$ind_name = preg_match('/PERSONAL INFORMATION/i' , $line);
			$fullname = substr($line, $ind + 20);
		}
				
		if (preg_match("/nationality/i", $line)) {
			$ind = preg_match("[ationality]", $line);
			if ($ind) $nationality = substr($line , $ind + 10, strlen($line )) ;
			$last_save_count = strlen($fullmull);
		}
		if (preg_match("/@/", $line) && !preg_match("/sebastienmalot/",$line) ) $email = $line;
		//if (!$email) {
			//$ind_email = preg_match('/E\-mail|Email/i' , $line);
			//if ($ind_email ) { $email = substr($line, $ind_email + 5, strlen($line));$email = trim($email);}
		//}
			
		if (preg_match("/linkedin/", $line)){
			$ind_start = strpos( $line , "http");
			if ($ind_start)  $linkedin = substr($line, $ind_start, strlen($line) );
				else $linkedin = substr($line , strpos($line, "ro."), strlen($line ));
		} 
		//if (preg_match("/mobile|Mobile[:]*[\+0-9\-\(\)\s]*/" , $line)  ) $mobile = $line; 
		if (preg_match($preg_phone,trim($line)) && $count < 17 ) $phone = $line;
		$ind_mob = preg_match('/mobile|Mobile[:]*[ \+0-9\-\(\)\s]*/', $line);
		if ($ind_mob && $count < 17) {
			$mobile =  $line;
		}
		
		$count = $count + 1;
		if ($count ===3 && preg_match('/[0-9]{3}/',$line) ) $backup = $line;
		if ($count ===4 && preg_match('/[0-9]{3}/',$line) && !$backup) $backup = $line;
		if ($count ===5 && preg_match('/[0-9]{3}/',$line) && !$backup) $backup = $line;
		//echo $count;
		
		
		
		
	}
	$start_experience =  stripos($fullmull, "work experience");
	if ($start_experience > 0) $start_cut = $start_experience;
	if ($end_education >0) $end_cut = $end_education;else $end_cut = strlen($fullmull);
	$resto = substr($fullmull , $start_cut, $end_cut);
	
	//$mydump = substr($fullmull, stripos($fullmull, "experience") , strlen($fullmull));
	//echo $mydump;
	//$ind_education = stripos($mydump, "education");
	//$resto = substr($mydump , 0, $end_education_count);
	echo "<p>";
	echo "<br/>full pdf parsable" . " - " . strlen($fullmull);
	echo "<br/>analisi:" .' inizio_experience ' . $start_cut . " fine " . $end_cut . "<br/>difference output " . strlen($resto);
	echo "</p>";
	$filediff = "diverso.txt";
	$fp = fopen($filediff, "w+");
	fwrite($fp, $resto);
	fclose($fp);
	


	
	$risult = new ParseResult();
	$risult->fullname = $fullname;
	$risult->email = $email;
	$risult->linkedin = $linkedin;
	$risult->mobile = $mobile;
	$risult->phone= $phone;
	
	$risult->nationality = $nationality;	
	$risult->remainder = $resto;
	return $risult;
}

}




?>