<?php
require_once("dbconfig.php");
require_once("dbfunction.php");
$msg['status']=false;

$msg['message']="failed";

$msg['data']=new arrayobject();

$user_id = $_POST['user_id'];

if(isset($_POST['user_id']) && $_POST['user_id']!='')
{
	$select = $con->query("UPDATE tbl_user SET device_token='',updated_at=NOW() WHERE id='$user_id'");
	if($select)
	{
		$msg['status']=true;

		$msg['message']="Successfully logout";

		$msg['data']=new arrayobject();
	}
	else 
	{
		$msg['status']=false;

		$msg['message']="Logout failed";

		$msg['data']=new arrayobject();
	}
}
echo json_encode($msg);
?>