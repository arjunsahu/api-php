<?php

require_once "Constants.php";
require_once "functions.php";
require_once 'web/functions.php';
 
date_default_timezone_set('Asia/Kolkata');

$current_time=new DateTime("now");
$now=$current_time->format('Y-m-d H:i:s');

function send_email($report_id,$message){
				
				
			$r_email="dileep@icommute.in";
				
				
				
				include "email/classes/class.phpmailer.php";

		
				
$mail=new PHPMailer(true);
		try {
  // your mail code here
} catch (phpmailerException $e) {
  echo $e->getMessage();
}

$mail->IsSMTP();

$mail->SMTPDebug = 1;

$mail->SMTPAuth=true;
$mail->SMTPSecure= "tls";

$mail->Host = "smtp.gmail.com";
$mail->Port = 587;
$mail-> IsHTML(true);



$mail-> IsHTML(true);

$subject=string_limit_words($_POST['Message'], 6);

$mail->Username = "icommuteapp@gmail.com";
$mail->password = "nomad@68";


$mail->SetForm=("icommuteapp@gmail.com");

$mail->Subject= ($subject .' #NewReport- iCommute');

$mail->Body = 'new report come  , <br/> <br/>  Report-Id:'.$report_id.' <br>Report-Content<br>'.$message.'';



$mail->AddAddress($r_email);

if(!$mail->Send())
{
echo "Mailer Error:".$mail->ErrorInfo;
}

				
				}
if (isset($_POST['User_Id']) && isset($_POST['Category_Id']) && isset($_POST['City_Id'])) 
{
		 $user_id=$_POST['User_Id']; 
		 $category_id=$_POST['Category_Id'];
		 $name=$_POST['name'];
		 $email=$_POST['email'];
		
		
		
		if($category_id==0)
			$category="News";
		
		if($category_id==1)
			$category="Traffic";
		
		if($category_id==2)
			$category="Bus Breakdown";
		
		if($category_id==3)
			$category="Query";
		
		if($category_id==4)
			$category="Missing Route";
		
		if($category_id==5)
			$category="Feedback";
		
		
		$category_id = $category_id + 1;
		
		
		
		//$category_id=$_POST['Category_Id'];
		//$location=$_POST['Location'];
		 $city_id=$_POST['City_Id'];
		//$image=$_POST['Image'];
		
		 $message=$_POST['Message'];

		//Generate hash tags
		$hashtag=gethashtags($_POST['Message']);

        //$hashtag2=gethashtags2($_POST['Message']);
		if(str_word_count($_POST['Message'])>5){
			$newtitle=string_limit_words($_POST['Message'], 6); // First 6 words
		}
		else
		{
			$newtitle=$_POST['Message'];
		}
        $urltitle=preg_replace('/[^a-z0-9]/i',' ', $newtitle);
        $newurltitle2=str_replace(" ","-",$newtitle);
		 
		$newurltitle=str_replace("#","",$newurltitle2);
		
		$hashtag3=str_replace(",","-",$hashtag);
		
		$url=$newurltitle.'-'.$hashtag3.'.html'; 
		$url = implode("-", array_unique(explode("-", $url)));
		$url = ltrim($url, '-');

		$response = array();
	
	//Success Flag	and Error Msg Initialization
		$success=0;
		$err_msg="";
	
	//Connecting to DB

		
		require_once "pdo_connect.php";
		$pdo_con=new DB_CONNECT();
		$pdo=$pdo_con->connect();

	$message=$message."&nbsp;-&nbsp;".$name;
	
	
		$functionObject=new DB_FUNC();
		
		$Report_Ins = $pdo->prepare("INSERT INTO reports(user_id,category,category_id,message,city_id,report_ts,hashtag,url) VALUES(?,?,?,?,?,?,?,?) RETURNING report_id");
		$Report_Ins->execute(array($user_id,$category,$category_id,$message,$city_id,$now,$hashtag,$url));
		
		$Report_Row= $Report_Ins->fetch(PDO::FETCH_ASSOC);
		 $Report_Id=$Report_Row["report_id"];
		
		//xml file creation start
		
       send_email($Report_Id,$message);
		
		
		
		
		$loc='http://www.icommute.in/web/report/'.$Report_Id.'/'.$url;
		
	
function c_element($e_name,$parent){
 global $xml;
 $node=$xml->createElement($e_name);
 $parent->appendChild($node);
 return $node;
 }
function c_value($value,$parent){
 global $xml;
 $value = $xml->createTextNode($value);
 $parent->appendChild($value);
 return $value;
 
 }


$lastmode=date('Y-m-d');
$changefreq='always';
$priority='0.8';
		
		
		
		
		$loc='http://www.icommute.in/web/report/'.$Report_Id.'/'.$url;

//echo $s_id. '<br>' .$s_name;
$xml =new DOMDocument("1.0");
$xml->load("sitemap.xml");

$root=$xml->getElementsByTagName("urlset")->item(0);
$url=c_element("url",$root);

$t_loc=c_element("loc", $url);
c_value("$loc",$t_loc);

$t_lastmode = c_element("lastmode", $url);
c_value("$lastmode",$t_lastmode);


$t_changefreq = c_element("changefreq", $url);
c_value("$changefreq",$t_changefreq);

$t_priority = c_element("priority", $url);
c_value("$priority",$t_priority);

//$xml= new DOMDocument("1.0","utf-8");
//$employee = $xml->createElement("employee");
//$employee = $xml->appendChild($employee);

//$empname = $xml->createElement("empname",$name);
//$empname = $employee->appendChild($empname);

//$empemail = $xml->createElement("empemail",$email);
//$price= $employee->appendChild($empemail);

$xml->FormatOutput=true;
//$string_value=$xml->saveXML();
$xml->save("sitemap.xml");




		
		
		
		
		//xml file creation end
		

			
	
			/* Increase the points in the User Profile */
			$Res_UP=$pdo->prepare("Update user_info 
						  set U_tot_pts=U_tot_pts+5,U_wk_pts=U_wk_pts+5,U_tdy_pts=U_tdy_pts+5,U_last_ts=?
						  where U_Id=?");
			$Res_UP->execute(array($now,$user_id));
		
	
		

}
if ($city_id==0)
{
 header("location: http://icommutedev.herokuapp.com/web/chennai-reports.html");
                       exit();
}
if ($city_id==1)
{
 header("location: http://icommutedev.herokuapp.com/web/bengaluru-reports.html");
                       exit();
}
if ($city_id==2)
{
 header("location: http://icommutedev.herokuapp.com/web/hyderabad-reports.html");
                       exit();
}

?>