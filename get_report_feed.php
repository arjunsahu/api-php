<?php
/* This module will get top reports from the report table based on his last report id */

// check for required fields

require_once "../Constants.php";
require_once "../functions.php";

date_default_timezone_set('Asia/Kolkata');

$current_time=new DateTime("now");
$now=$current_time->format('Y-m-d H:i:s');

$response = array();
$response["report"]=array();

//Success Flag	and Error Msg Initialization
$success=1;
$err_msg="";

$funcObj=new DB_FUNC();

try{	
	
	if (isset($_GET['city_id']) && isset($_GET['report_id']) ) 
	{

		$city_id = $_GET['city_id'];
		$prev_report_id=$_GET['report_id'];
		
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

		try{

			if($prev_report_id == 0)
			{

				$feed_res = $pdo->prepare("select * from reports rep inner join user_info ui on rep.user_id=ui.u_id 
											where city_id=? and category_id in (?,?,?) ORDER BY report_ts DESC LIMIT 30");
				$feed_res->execute(array($city_id,NEWS,TRAFFIC,BREAKDOWNS));
				$Temp=$feed_res->fetchAll();

			}
			else
			{
				$count_res= $pdo->prepare("select count(*) from reports rep inner join user_info ui on rep.user_id=ui.u_id 
											where city_id=? and category_id in (?,?,?) and report_id > ?");
				$count_res->execute(array($city_id,NEWS,TRAFFIC,BREAKDOWNS,$prev_report_id));
				$count=$count_res->fetchColumn();

				if($count > REPORT_COUNT)
				{
					$feed_res = $pdo->prepare("select * from reports rep inner join user_info ui on rep.user_id=ui.u_id 
												where city_id=? and category_id in (?,?,?) ORDER BY report_ts DESC LIMIT 30");
					$feed_res->execute(array($city_id,NEWS,TRAFFIC,BREAKDOWNS));
					$Temp=$feed_res->fetchAll();
				}
				else
				{
					$feed_res= $pdo->prepare("select * from reports rep inner join user_info ui on rep.user_id=ui.u_id 
													where city_id=? and category_id in (?,?,?) and report_id > ? ORDER BY report_ts DESC");
					$feed_res->execute(array($city_id,NEWS,TRAFFIC,BREAKDOWNS,$prev_report_id));
					$Temp=$feed_res->fetchAll();
				}
			}
		
			foreach($Temp as $row) 
			{
				$report_info['Report_Id']=$row['0'];
				
				//User Name check
				if(isset($row['9']) || (!empty($row['9'])))
					$report_info['User_Name']=$row['9'];
				else
					$report_info['User_Name']=ANONYMOUS_USER;
				
				$report_info['Category']=$row['2'];
				$report_info['Category_Id']=$row['7'];
				
				//Location check
				if(isset($row['3']) || (!empty($row['3'])))
					$report_info['Location']=$row['3'];
				else
					$report_info['Location']='';
				
				$report_info['Message']=$row['4'];
				$report_info['Report_Ts']=$funcObj->timeAgo($row['5']);
				
				array_push($response["report"], $report_info);
			}
			
			$response["success"] = 1;
			echo json_encode($response);

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

	 // required field is missing
		$response["success"] = 4;
		$response["message"] = "Incorrect parameters";
	 
		// echoing JSON response
		echo json_encode($response);
	 }

}
catch(Exception $e)
{

	$err_msg=$e->getMessage();
	
    $response["success"] = 4;
    $response["message"] = "Unknown Issue";
	$response["error"]=$err_msg;
 
    // echoing JSON response
    echo json_encode($response);
 }
?>
