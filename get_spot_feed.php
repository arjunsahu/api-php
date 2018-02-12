<?php
/* This module will get top spots from the spot table based on his last spot id */

// check for required fields

require_once "../Constants.php";
require_once "../functions.php";
require_once "../DbConstants.php";

date_default_timezone_set('Asia/Kolkata');

$current_time=new DateTime("now");
$now=$current_time->format('Y-m-d H:i:s');

$response = array();
$response["spot"]=array();

//Success Flag	and Error Msg Initialization
$success=1;
$err_msg="";

try{	

	if (isset($_GET['city_id']) && isset($_GET['spot_id']) ) 
	{
	$spot_city = $_GET['city_id'];
	$prev_spot_id=$_GET['spot_id'];
	
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

		$db_object=new DB_FUNC();

		if($prev_spot_id == 0)
		{

			$feed_res = $pdo->prepare("select * from bus_spot where spot_city=? ORDER BY Bus_Spot_Id DESC LIMIT 50");
			$feed_res->execute(array($spot_city));
			$Temp=$feed_res->fetchAll();

		}
		else
		{
			$count_res= $pdo->prepare("select count(*) from bus_spot where spot_city=? and Bus_Spot_Id > ?");
			$count_res->execute(array($spot_city,$prev_spot_id));
			$count=$count_res->fetchColumn();

			if($count > 50)
			{
				$feed_res = $pdo->prepare("select * from bus_spot where spot_city=? ORDER BY Bus_Spot_Id DESC LIMIT 50");
				$feed_res->execute(array($spot_city));
				$Temp=$feed_res->fetchAll();
			}
			else
			{
				$feed_res= $pdo->prepare("select * from bus_spot where spot_city=? and Bus_Spot_Id > ? ORDER BY Bus_Spot_Id DESC");
				$feed_res->execute(array($spot_city,$prev_spot_id));
				$Temp=$feed_res->fetchAll();
			}
		}
		
			foreach($Temp as $row) 
			{
				$spot_info['spot_id']=$row[0];
				$spot_info['bus_no']=$db_object->getBusNo($spot_city,$row[1],$pdo);
				$spot_info['stop_name']=$db_object->getStopName($spot_city,$row[2],$pdo);
				$spot_info['bus_direction']=$db_object->Bus_Dir_New($row[1],$row[3],$spot_city,$pdo);
				$spot_info['spot_ts']=$db_object->timeAgo($row[4]);
				$spot_info['spot_lat']=$row[7];
				$spot_info['spot_lng']=$row[8];
				array_push($response["spot"], $spot_info);
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















