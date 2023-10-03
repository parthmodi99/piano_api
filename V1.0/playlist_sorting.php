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

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['playlist_id']) && $_REQUEST['playlist_id'] != '')
{
    //update name
    if(isset($_REQUEST['name']) && $_REQUEST['name'] != ''){
        $upd = $con->query("UPDATE tbl_playlist SET name='".mysqli_real_escape_string($con,trim($_REQUEST['name']))."' WHERE user_id='".$_REQUEST['user_id']."' AND id='".$_REQUEST['playlist_id']."'");
    }

    if(isset($_REQUEST['sorting']) && $_REQUEST['sorting'] != ''){
        $sel = $con->query("SELECT * FROM tbl_playlist_sorting WHERE user_id='".$_REQUEST['user_id']."' AND playlist_id='".$_REQUEST['playlist_id']."'");
        if($sel->num_rows > 0){
            $upd = $con->query("UPDATE tbl_playlist_sorting SET sorting='".mysqli_real_escape_string($con,trim($_REQUEST['sorting']))."' WHERE user_id='".$_REQUEST['user_id']."' AND playlist_id='".$_REQUEST['playlist_id']."'");
        }
        else{
            $ins_sorting = $con->query("INSERT INTO tbl_playlist_sorting(`user_id`,`playlist_id`,`sorting`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['playlist_id']."','".$_REQUEST['sorting']."','".$cur_date."')");
        }
    }

    
    
    $msg["status"] = true;
    $msg["message"] = "Success.";
    $msg['data']=new arrayobject();
    
}
else{
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
?>