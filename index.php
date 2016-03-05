<!DOCTYPE html>
<html>
  <head><link href="css/my_form_styles.css" rel="stylesheet"/>
  </head>
<body>

<form id="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">INPUT CANDIDATE CV:</label> <input type="file" name="Filedata" id="Filedata" /> 
<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
<input type="submit" name="submit" value="Submit" />
</form>

<?php

include 'scan.php';
include 'plainhtml.php';
include 'pdfparser-0.9.20/vendor/autoload.php';
// ERROR REPORTING

                                 // MAIN MANIN MAIN
  
  
  $uploadfile = time(); // CHANGE ME
  $uploaded = 0;
     
  if (@move_uploaded_file($_FILES['Filedata']['tmp_name'], $uploadfile)) {
    print "<br/>File was successfully uploaded.\n";
	$uploaded = 1;
	} else {
    //print "<p>UPLOAD FAIL ! \n</p>";
	$uploaded = 0;
	}
    if ($uploaded) {
		$smalot = new \Smalot\PdfParser\Parser();
		$pdf    = $smalot->parseFile($uploadfile);
	
		$mydump  =null;
		$pages  = $pdf->getPages();
		foreach ($pages as $page) {
			$mydump = $mydump . $page->getText();
		}
	}
  //$mydump = substr($mydump, 0, stripos($mydump, "experience") );// debug togli
  
  //  echo $mydump;
  $pdffile = time();
  $fp1 = fopen($pdffile, "w+");
  fwrite($fp1, preg_replace( "/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($mydump))) )); // DEBUG CHANGED mydump
  fclose($fp1);
  $parser = new Parser();
  $risult = $parser->parse($pdffile);
  
  echo '<pre>';
  echo "FULL NAME " . $risult->fullname . '\n'; 
  echo "EMAIL ".$risult->email . '\n'; 
  echo "TELEPHONE ".$risult->phone . '\n'; 
  echo "MOBILE ".$risult->mobile . '\n'; 
  echo "LINKEDIN ".$risult->linkedin . '\n'; 
  echo "LANGUAGE(*) ".$risult->nationality . '\n'; 
  
  //echo "RIMANETNE<br/>". $risult->remainder;
  $nomo = $risult->fullname;
  $phone = $risult->phone;
  $mobile = $risult->mobile;
  $email= $risult->email;
  $linkedin = trim($risult->linkedin);
  $nationality = $risult->nationality;
  // post process for email
  if ( substr( trim($email), 0, 6 ) === "E-mail" ) $email = str_replace('E-mail' , "", $email);
  $email = trim($email);
  echo $email;
  try{
  $url1 = split(" ", $linkedin)[0];
  $url2 = split(" ", $linkedin)[1];
  if (!$url1|| !$url2) {
	  $url1 = split("  ", $linkedin)[0];
      $url2 = split("  ", $linkedin)[1];
  }
  if ( $url1 && preg_match("/linkedin/" , $url1)) $linkedin = $url1;
  if ( $url2 && preg_match("/linkedin/" , $url2)) $linkedin = $url2;
  }catch(Exception $e){echo "\nerrore analisi url. multiple urls?";}
  echo $linkedin;
  $ris_social = null;
  $social = null;
  if ($uploaded){
	  $compare = new Compare();
	  $ris_social = $compare->confronta("./diverso.txt", trim($linkedin));
	  $confidence = split(" " , $ris_social)[0];
	  $social = split(" " , $ris_social)[1];
      echo "<br/>Confidence:" . $confidence;
	  
	  $mysql = new mysqli('localhost','mysql','' , 'test'); 
	  if (!$mysql) die('Could not connect to MySQL: ' . mysql_error());
  
	  $query = "insert into candidate_cv (name, email, phone,mobile, linkedin,language, CV_FILENAME,confidence, social)"."values('".$nomo."','".$email."','".$phone."','".$mobile."','".$linkedin."','".$nationality."','".$_FILES['Filedata']['name']."','".$confidence."','". $social."' );";
	  $mysql->query($query) or die('Error, query failed'. mysql_error()); 
      echo "<br/>SQL SUCCESS: <strong>" . "RECORD STORED" ."</strong>";
      $mysql->close();
  }
  ?>