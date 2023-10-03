<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

if($timezone!='')
{
  date_default_timezone_set($_REQUEST['timezone']);
}
$cur_date = date('Y-m-d H:i:s');

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['track_id']) && $_REQUEST['track_id'] != '')
{
   
    $sel = $con->query("SELECT * FROM tbl_mistake_track WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$_REQUEST['track_id']."'");
    if($sel->num_rows == 0)
    {
        $ins = $con->query("INSERT INTO tbl_mistake_track(`user_id`,`track_id`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['track_id']."','".$cur_date."')");
        if($ins)
        {
            $msg['message']='Success.';
            $msg['data']=new arrayobject();
            $msg['status']=true;
        }
    }
    else{
        $msg['message']='failed.';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
    
}
else{
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
?>