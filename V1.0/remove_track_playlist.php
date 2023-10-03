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

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['playlist_id']) && $_REQUEST['playlist_id'] != '' && isset($_REQUEST['track_id']) && $_REQUEST['track_id'] != '')
{
    $sel = $con->query("SELECT * FROM tbl_playlist WHERE user_id='".$_REQUEST['user_id']."' AND id='".$_REQUEST['playlist_id']."'");
    if($sel->num_rows > 0)
    {
        $del = $con->query("DELETE FROM tbl_playlist_track WHERE user_id='".$_REQUEST['user_id']."' AND playlist_id='".$_REQUEST['playlist_id']."' AND track_id='".$_REQUEST['track_id']."'");
        if($del)
        {
            $msg["status"] = true;
            $msg["message"] = "Deleted successfully.";
            $msg['data']=new arrayobject();
        }
        else{
            $msg['message']='Failed to delete.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }

        
    }
    else{
        $msg['message']='No playlist found.';
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