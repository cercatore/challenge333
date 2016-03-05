<?php
include_once("simplehtmldom_1_5/simple_html_dom.php") ;

class Compare{
	
public function confronta($file_pdf, $linkedin_url){

// Create DOM from URL or file
//$linkedin_url = 'https://www.linkedin.com/in/drvosanova?trk=tab_pro';

$http = substr($linkedin_url, 0 , 4);
if ($http !== 'http') $linkedin_url = 'http://' . $linkedin_url;
$options = array(
    'https' => array(
    ),
	
);
$context  = stream_context_create($options);
try{
	$result = file_get_contents($linkedin_url, true,  $context);
}
catch(Exception $ex){return -1;}
	$filename = time();
	$fp = fopen($filename,"a");
	fwrite($fp, $result);
	if (strlen($result)==34970)
		echo "<b>ERROR: linkedin not public</b>";
	else
	echo "<br>FILEURL linkedin: readed " . strlen($result);
	fclose($fp);

$html = file_get_html($filename);
$a=array();
// Find all images 
$cumulati = 0;

$diff =file_get_contents( $file_pdf, false);
echo "<br>COMPARE with CV: readed " . strlen($diff);
$count = 0;
echo "<br/>";
foreach($html->find('section') as $element) 
       if (preg_match('/^experience$/' , $element->id) ){
		   
		   $clearText = preg_replace( "/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($element))) );
		   //echo $clearText;
		   array_push($a, $clearText);
		   similar_text($clearText, $diff, $percentMatch); 
		   echo "<br/>". $count . " " . $percentMatch ;
		   $cumulati = $cumulati + $percentMatch;
		   $count = $count +1;
	   }
echo "<br>";
$social = null;
foreach ($html->find('div[class=member-connections]') as $element) {
	print $element;
	$social = $element->text();
	}



/*** round off the match and echo ***/
echo $linkedin_url;
return round($cumulati) . " " . $social; 
//foreach($a as $item)
	//echo $item . "\n";
}

}
       
?>