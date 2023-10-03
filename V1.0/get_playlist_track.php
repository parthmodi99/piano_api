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
    //$sel = $con->query("SELECT * FROM tbl_track WHERE id IN(SELECT track_id FROM tbl_playlist_track WHERE user_id='".$_REQUEST['user_id']."' AND playlist_id='".$_REQUEST['playlist_id']."') ORDER BY id DESC");
    $sel = $con->query("SELECT track.*,playlist.`created_at` as added_at FROM `tbl_track` as track,tbl_playlist_track as playlist WHERE track.`id` = playlist.`track_id` AND playlist.`playlist_id` = '".$_REQUEST['playlist_id']."' AND playlist.`user_id` = '".$_REQUEST['user_id']."' ORDER BY track.`id` DESC");
    if($sel->num_rows > 0)
    {
        $recent = mysqli_fetch_all($sel,MYSQLI_ASSOC);
    }
    else{
        $recent = array();
    }
    //get sorting
    $sel_sorting = $con->query("SELECT * FROM tbl_playlist_sorting WHERE user_id='".$_REQUEST['user_id']."' AND playlist_id='".$_REQUEST['playlist_id']."'");
    if($sel_sorting->num_rows > 0)
    {
        $sorting_data = $sel_sorting->fetch_assoc();
        $sorting = $sorting_data['sorting'];
    }
    else{
        $sorting = '1';
    }
    if(!empty($recent))
    {
        $msg["status"] = true;
        $msg["message"] = "Success.";
        $msg['track']=$recent;
        $msg['sorting']=$sorting;
    }
    else{
        $msg["status"] = false;
        $msg["message"] = "no track found.";
        $msg['track']=new arrayobject();
    }
}
else{
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
?>