<?php
       ob_start();
	function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
function makeLinks($str) {
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	$urls = array();
	$urlsToReplace = array();
	if(preg_match_all($reg_exUrl, $str, $urls)) {
		$numOfMatches = count($urls[0]);
		$numOfUrlsToReplace = 0;
		for($i=0; $i<$numOfMatches; $i++) {
			$alreadyAdded = false;
			$numOfUrlsToReplace = count($urlsToReplace);
			for($j=0; $j<$numOfUrlsToReplace; $j++) {
				if($urlsToReplace[$j] == $urls[0][$i]) {
					$alreadyAdded = true;
				}
			}
			if(!$alreadyAdded) {
				array_push($urlsToReplace, $urls[0][$i]);
			}
		}
		$numOfUrlsToReplace = count($urlsToReplace);
		for($i=0; $i<$numOfUrlsToReplace; $i++) {
			$str = str_replace($urlsToReplace[$i], "<a style='color:#0000EE;' href=\"".$urlsToReplace[$i]."\">".$urlsToReplace[$i]."</a> ", $str);
		}
		return $str;
	} else {
		return $str;
	}
}

	require_once "../Constants.php";
require_once "../functions.php";
// include "../CommentInfo1.php";

date_default_timezone_set('Asia/Kolkata');

$current_time=new DateTime("now");
$now=$current_time->format('Y-m-d H:i:s');



//Success Flag	and Error Msg Initialization
$success=1;
$err_msg="";

$funcObj=new DB_FUNC();

	
	
	     

		
		
		
			
			require_once "../pdo_connect.php";
			$pdo_con=new DB_CONNECT();
			$pdo=$pdo_con->connect();
		
       
	







//deshboard operation select comment and delete it by selecting checkbox

if(isset($_POST['pin']) && isset($_POST['checkbox']))
{
	
    $comment_list = implode(",", $_POST['checkbox']);
   
	$pin=$_POST['pin'];

	//Connecting to DB
	try{	
		
		require_once "../pdo_connect.php";
		$pdo_con=new DB_CONNECT();
		$pdo=$pdo_con->connect();
	}
	catch(PDOException $e)
	{
			$err_msg=$e->getMessage();
			 
			$response["success"] = 2;
			$response["message"] = "Connection Error, Try again";
			$response["error"]=$err_msg;
			echo json_encode($response);
			exit;
	}
	
	if($pin==2513)
	{
		try{
			$del_res = $pdo->prepare("delete from report_comment where  report_comment_id in($comment_list)");
			$del_res->execute();

			$response["success"] = 1;
			json_encode($response);

			//resetting pdo handler
			$pdo=null;
			

		}	
		catch(PDOException $e)
		{
			$err_msg=$e->getMessage();

			$response["success"] = 6;
			$response["message"] = "Calc error, Try again";
			$response["error"]=$err_msg;
			echo json_encode($response);
			exit;
		}
	}
	else
	{
		$response["success"] = 2;
		$response["message"] = "Pin Number Incorrect";
		echo json_encode($response);
	}

}
?>

<!DOCTYPE html>
<script type="text/javascript" src="jquery.js"></script>
 <script type="text/javascript">
$(function() {

$(".submit").click(function() {

var name = $("#name").val();
var email = $("#email").val();
	var comment = $("#comment").val();
		var post_id = $("#post_id").val();
    var dataString = 'name='+ name + '&email=' + email + '&comment=' + comment + '&post_id=' + post_id;
	
	if(name=='' || email=='' || comment=='')
     {
    alert('Please Give Valide Details');
     }
	else
	{
	$("#flash").show();
	$("#flash").fadeIn(400).html('<img src="ajax-loader.gif" align="absmiddle">&nbsp;<span class="loading">Loading Comment...</span>');
$.ajax({
		type: "POST",
  url: "commentajax.php",
   data: dataString,
  cache: false,
  success: function(html){
 
  $("ul#update").append(html);
  $("ul#update li:last").fadeIn("slow");
  document.getElementById('email').value='';
   document.getElementById('name').value='';
    document.getElementById('comment').value='';
	$("#name").focus();
 
  $("#flash").hide();
	
  }
 });
}
return false;
	});



});


</script>
<style type="text/css">
body
{
font-family:Arial, Helvetica, sans-serif;
font-size:14px;
}
.comment_box
{
background-color:#D3E7F5; border-bottom:#ffffff solid 1px; padding-top:3px
}
a
	{
	text-decoration:none;
	color:#d02b55;
	}
	a:hover
	{
	text-decoration:underline;
	color:#d02b55;
	}
	*{margin:0;padding:0;}
	
	
	ol.timeline
	{list-style:none;font-size:1.2em;}
	ol.timeline li{ display:none;position:relative;padding:.7em 0 .6em 0;}ol.timeline li:first-child{}
	
	#main
	{
	 margin-top:0px; margin-left:20px;margin-right:10px;
	font-family:"Trebuchet MS";
	}
	#flash
	{
	margin-left:100px;
	
	}
	.box
	{
	height:85px;
	border-bottom:#dedede dashed 1px;
	margin-bottom:20px;
	}
		input
	{
	color:#000000;
	font-size:14px;
	border:#666666 solid 2px ;
	border-radius: 10px;
	height:34px;
	margin-bottom:10px;
	width:100%;
	padding:5px 10px 5px 10px;
	
	}
	textarea
	{
		padding:5px 5px 5px 10px;
		
	color:#000000;
	font-size:14px;
	border:#666666 solid 2px;
	height:124px;
	margin-bottom:10px;
		width:100%;
	 border-radius: 10px;
	
	}
	.titles{
	font-size:13px;
	padding-left:10px;
	
	
	}
	.star
	{
	color:#FF0000; font-size:16px; font-weight:bold;
	padding-left:5px;
	}
	
	.com_img
	{
	float: left; width: 80px; height: 80px; margin-right: 20px;
	}
	.com_name
	{
	font-size: 16px; color: rgb(102, 51, 153); font-weight: bold;
	}
</style>

<link rel="shortcut icon" href="http://icommutedev.herokuapp.com/web/share/assets/images/favicon.ico"> 
<head><title><?php  $title=str_replace('-', ' ', $_GET["title"]);echo $title=str_replace('.html', '', $title);?></title>


<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" rel="stylesheet">

<link href="http://icommutedev.herokuapp.com/web/main.css" rel="stylesheet" />

<link href='http://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

         <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
         <script src="JS/bootstrap.min.js"></script>
         
        <script src="src/images-grid.js"></script>
        <script src="https://use.fontawesome.com/2dc00f20da.js"></script>
		
         <link id="theme-style" rel="stylesheet" href="http://icommutedev.herokuapp.com/web/share/assets/css/styles-2.css"> 
	 <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/jquery-1.12.3.min.js"></script>
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/isMobile.min.js"></script>       
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/jquery.easing.1.3.js"></script>   
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/plugins/bootstrap/bootstrap.min.js"></script>     
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/jquery.inview.min.js"></script>
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/jquery.fitvids.js"></script>
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/jquery.scrollTo.min.js"></script>    
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/jquery.placeholder.js"></script>
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/plugins/flexslider/jquery.flexslider-min.js"></script>
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/jquery.matchHeight-min.js"></script>
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/js/main.js"></script>
    <script type="text/javascript" src="http://icommutedev.herokuapp.com/web/share/assets/plugins/sweetalert/sweetalert.min.js"></script> 
	
	
	
	
	
   
	




<header id="top" class="header navbar-fixed-top">  
          <div class="container ">               
            <h1 class="logo pull-left">
                <a  href="http://icommutedev.herokuapp.com">
                    <img id="logo-image" class="logo-image" src="http://icommutedev.herokuapp.com/web/share/assets/images/logo.png" alt="Logo">
                    <img id="logo-image" class="logo-image" src="http://icommutedev.herokuapp.com/web/share/assets/images/reportlogo.png" alt="Name">
					
                </a>
            </h1><!--//logo-->              
             <nav id="main-nav" class="main-nav navbar-right" role="navigation">
                <div class="navbar-header">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button><!--//nav-toggle-->
                </div><!--//navbar-header-->            
                <div class="navbar-collapse collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                          
                             
                                
                                   
                       
                        
                       
                        <!-- <li class="nav-item"><a href="sharedMap.html?share_id=415666">User share Map</a></li> -->
                    </ul><!--//nav-->
                </div><!--//navabr-collapse-->
            </nav><!<!--//main-nav-->           
        </div>
    </header>

</br>
</br>
</br>
</br>



	</head>
	

<body>






<div class="row">


 <div class=" col-md-3 col-lg-3 col-xs-0 col-sm-0">









 
   

   
    </div>
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
   
   
       
          

          
          
     
        
        
	
		
        

 
  <?php
/* This module will get top reports from the report table based on his last report id */

// check for required fields
if(isset($_POST['pin']) && isset($_POST['checkbox']))
{
	
    $comment_list = implode(",", $_POST['checkbox']);
   
	$pin=$_POST['pin'];

	//Connecting to DB
	try{	
		
		require_once "../pdo_connect.php";
		$pdo_con=new DB_CONNECT();
		$pdo=$pdo_con->connect();
	}
	catch(PDOException $e)
	{
			$err_msg=$e->getMessage();
			 
			$response["success"] = 2;
			$response["message"] = "Connection Error, Try again";
			$response["error"]=$err_msg;
			echo json_encode($response);
			exit;
	}
	
	if($pin==2513)
	{
		try{
			$del_res = $pdo->prepare("delete from report_comment where  report_comment_id in($comment_list)");
			$del_res->execute();

			$response["success"] = 1;
			json_encode($response);

			//resetting pdo handler
			
			

		}	
		catch(PDOException $e)
		{
			$err_msg=$e->getMessage();

			$response["success"] = 6;
			$response["message"] = "Calc error, Try again";
			$response["error"]=$err_msg;
			echo json_encode($response);
			exit;
		}
	}
	else
	{
		$response["success"] = 2;
		$response["message"] = "Pin Number Incorrect";
		echo json_encode($response);
	}

}

		

			

				$feed_res = $pdo->prepare("select rep.report_id,rep.message,rep.category,rep.category_id,rep.location,rep.report_ts,rep.url,ui.u_name,rc.report_id as comment_report_id ,rep.image,ui.u_pic,count(*) as count    from reports rep left join report_comment rc on rep.report_id=rc.report_id  inner join user_info ui on rep.user_id=ui.u_id where rep.report_id=? and rep.category_id in (0,1,2,3,4,5,6) group by 1,2,3,4,5,6,7,8,9,10,11");
				
				// $feed_res->execute(array($city_id,NEWS,TRAFFIC,BREAKDOWNS,QUERY,$limit,$start));
				 
//           $feed_res->execute(array($city_id,$limit,$start));
		

			

				//$feed_res = $pdo->prepare("select * from reports rep inner join user_info ui on rep.user_id=ui.u_id where report_id=?  ORDER BY report_ts DESC LIMIT 30");
				
				$feed_res->execute(array($_GET["id"]));
				//$Temp=$feed_res->fetchAll();
				
				
    
	while ($mydata = $feed_res->fetch(PDO::FETCH_ASSOC))
{	
			
			
			
			
			?>

  <div class="mainDiv"  >
 <?php
           $Report_Id =$mydata["report_id"]; 
         $User_Name =$mydata["u_name"];
		 $Category =$mydata["category"];
		 $Category_Id =$mydata["category_id"];
		 $Location  =$mydata["location"];
		 $Message  =$mydata["message"];
		 $image = $mydata["image"];
		 $u_pic = $mydata["u_pic"];
		 $comment_report_count=$mydata["count"];
		 
		 $comment_report_id=$mydata["comment_report_id"];
		 
		 
		 $Report_Ts =time_elapsed_string($mydata["report_ts"]);
		 $html=".html";
          $url=$mydata["url"];
		
	
		  
		  
		  if($comment_report_id != null && $comment_report_count>0)

                $total_rows=$comment_report_count;

            else

               $total_rows=0;
       
	
     echo'<div class="content-box">';
	 
	echo'<div class="message-content">';
	echo'<div>';
	
	
	
      echo'<div style="display: inline-block; margin-top:15px; margin-left:10px;color:#cc0000;float:left;" >';
	  if (!empty($u_pic)){
		  $x='https://icommuteprod.s3.ap-south-1.amazonaws.com'.$u_pic;
		  
		  if(checkRemoteFile($x)){
		
		 echo"<img class='post_pro_pic' display=inline-block  width=35 height=35  src='https://icommuteprod.s3.ap-south-1.amazonaws.com".$u_pic."'>";
		  }
		  else
			echo"<img class='post_pro_pic' display=inline-block  width=35 height=35  src='https://icommuteprod.s3.ap-south-1.amazonaws.com/images/users/IMG_40207.jpg'>";  
		 }
		 
	  
	  else {
		 echo"<img class='post_pro_pic' display=inline-block  width=35 height=35  src='https://icommuteprod.s3.ap-south-1.amazonaws.com/images/users/IMG_40207.jpg'>";
	 //echo'&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-user fa-2x" aria-hidden="true"></i>';
	  }
	  echo'</div>';
	  
	  echo'<div style="display: inline-block; margin-top:15px; margin-left:10px;color:#cc0000;  class="fontr" >';
	if (!empty($User_Name)){
		
	  echo" 

 $User_Name &nbsp;";
 
	}
	else {
		 echo" 

UNKNOWN &nbsp;&nbsp;";
	}
	echo '<font size="2" >';
	if (!empty($Location)){
echo"<b><i class='fa fa-map-marker' aria-hidden='true'></i>
</b>";

echo"$Location"; }

	//echo'</font>';
	echo'</br>';
	//echo'<div class="small"></div>';
	//echo'<i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;&nbsp;';
	echo'<font color="#808080" size=1.5>';
          
			echo  $Report_Ts;
			echo  "&nbsp;|&nbsp;".$Category;
			
			
			echo"</br>";
			echo"</br>";
			echo'</font>';
			
     
	echo'</div>';
	
	//put report option here
	 echo'<div style="display: inline-block; margin-top:15px; margin-right:10px; float:right;  " >';
  
  
  
   echo'</div>';
	
	
	echo'</div>';
		
		
	 echo'</font>';
	//echo'<hr width="476">';
	if (!empty($Message)){
		
		
	echo'<div class="wordwrap"style="padding-left:2vw; padding-right:2vw;padding-top:-2vh;padding-bottom:3vh;color:#000000; ">';
	$Message=makeLinks($Message);
	echo "{$Message}";
	echo'</div>';
	//echo"</br>";
	}
	if (!empty($image)){
		echo "<img class='postpic' src='https://icommuteprod.s3.ap-south-1.amazonaws.com". $image."'>";
		echo"</br></br>";
     
    
		}
		if (!empty($url)){
			
		if( $total_rows>1){
			//$total_rows=$total_rows-1;
		echo '<a href="http://icommutedev.herokuapp.com/web/comment/'. $Report_Id .'/'.$url.'"style="text-decoration: none" type="hidden" method="post" >';
		echo'<font color="#808080" size=2>';
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$total_rows."&nbsp;"."Comments";
		echo'</font>';
		echo"</br></br></a>";
		}
		else
		{
		echo '<a href="http://icommutedev.herokuapp.com/web/comment/'. $Report_Id .'/'.$url.'"style="text-decoration: none" type="hidden" method="post" >';
			echo'<font color="#808080" size=2>';
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"."Comments here";
		echo'</font>';
		echo"</br></br></a>";
		}
		}
	echo'</div>';
	echo'</div>';

	//echo'</a>';
	
	echo'</div>';
	
	
	}
	
   echo'</br>';
	
 
  ?>
  <div id="main">
 
				<form method="post" action="#">
				
				
			
<div id="general-content">

<?php 
$feed_res = $pdo->prepare("select * from report_comment rep inner join user_info ui on rep.user_id=ui.u_id where rep.report_id=? ORDER BY comment_ts ");
		$feed_res->execute(array($_GET["id"]));
				//$Temp=$feed_res->fetchAll();
    
	while ($mydata = $feed_res->fetch(PDO::FETCH_ASSOC))
{	
 
         $Report_Id =$mydata["report_id"]; 	
         $User_Name =$mydata["u_name"];  
		 
		
		 $Message  =$mydata["message"];
		
		 $u_pic = $mydata["u_pic"];
		 $report_comment_id = $mydata["report_comment_id"];
		
		 $Report_Ts =time_elapsed_string($mydata["comment_ts"]);
		
        
		  
		
       
	
     echo'<div class="content-box">';
	
	echo'<div class="message-content">';
	echo'<div>';
	
	
	
      echo'<div style="display: inline-block; margin-top:15px; margin-left:10px;color:#cc0000;float:left;" >';
	  if (!empty($u_pic)){
		  $x='https://icommuteprod.s3.ap-south-1.amazonaws.com'.$u_pic;
		  
		  if(checkRemoteFile($x)){
		
		 echo"<img class='post_pro_pic' display=inline-block  width=35 height=35  src='https://icommuteprod.s3.ap-south-1.amazonaws.com".$u_pic."'>";
		  }
		  else
			echo"<img class='post_pro_pic' display=inline-block  width=35 height=35  src='https://icommuteprod.s3.ap-south-1.amazonaws.com/images/users/IMG_40207.jpg'>";  
		 }
		 
	  
	  else {
		 echo"<img class='post_pro_pic' display=inline-block  width=35 height=35  src='https://icommuteprod.s3.ap-south-1.amazonaws.com/images/users/IMG_40207.jpg'>";
	 //echo'&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-user fa-2x" aria-hidden="true"></i>';
	  }
	  echo'</div>';
	  
	  echo'<div style="display: inline-block; margin-top:15px; margin-left:10px;color:#cc0000;  class="fontr" >';
	  echo'<b>';
	if (!empty($User_Name)){
		
	  echo" 

 $User_Name &nbsp;";
 
	}
	else {
		 echo" 

UNKNOWN &nbsp;&nbsp;";
	}
	echo'</b>';

	echo'</br>';
	//echo'<div class="small"></div>';
	//echo'<i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;&nbsp;';
	echo'<font color="#808080"size=1.5 >';
          
			echo  $Report_Ts; 
		
			
			echo'</font>';
			
     
	echo'</div>';
	
	//put report option here
	 echo'<div style="display: inline-block; margin-top:15px; margin-right:10px; float:right;  " >';
  echo'<input name = "checkbox[]" type="checkbox"  id="checkbox[]" value="'.$report_comment_id.'" style="float:right;margin-right:2vw;">';
  
  
   echo'</div>';
	
	
	echo'</div>';
		
		
	 echo'</font>';
	 
	 
	 
	 
	 
	//echo'<hr width="476">';
	if (!empty($Message)){
		
		
	echo'<div class="wordwrap"style="padding-left:2vw; padding-right:2vw;padding-top:-2vh;padding-bottom:3vh;color:#000000; ">';
	
	echo "{$Message}";
	//echo $Report_Id;
	//echo"category";echo $Category_Id;
	echo'</div>';
	//echo"</br>";
	}
	
		
		
	echo'</div>';
	echo'</div>';

	//echo'</a>';
	
	//echo'</div>';



}

?><div style="margin-left:15vw;margin-right:10vw;">
				  <div id="general">
    <i>
        <span class="counter"></span>
    </i>
</div> <button type="button" class="select_all" />Reset</button><br />
				
				
				<input type="text"  name="pin" placeholder="Enter Pin" id="pin" required /> <br>
				<input type="submit" class="submit" value="Delete Comment" />
				<br><br>
				
				</div>

</form>
</div>

<div id="flash" align="left"  ></div>


<br />





</div>



  

	  </br></br></br>
	  </div> </div>
		<div class="col-xs-0 col-sm-0 col-md-3 col-lg-3"></div>
    
     
<script>          
	$('#general i .counter').text(' ');

var fnUpdateCount = function() {
	var generallen = $("#general-content input[name='checkbox[]']:checked").length;
    console.log(generallen,$("#general i .counter") )
	if (generallen > 0) {
		$("#general i .counter").text('(' + generallen + ')');
	} else {
		$("#general i .counter").text(' ');
	}
	
};

$("#general-content input:checkbox").on("change", function() {
			fnUpdateCount();
		});

$('.select_all').click(function() {
			
			var checkboxes = $("#general-content input:checkbox");

			
				checkboxes.prop('checked', false);
			
            fnUpdateCount();
		});
</script>

    
     
     
    


</body>
</html>