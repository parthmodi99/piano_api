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

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '')
{
    $user = $con->query("SELECT * FROM tbl_user WHERE id='".$_REQUEST['user_id']."'");
    $user_data = $user->fetch_assoc();
    /* favorite */
    $sel = $con->query("SELECT tf.`created_at` as favorite_at,t.* FROM tbl_track as t,tbl_favorite as tf WHERE t.`status`=1 AND tf.`user_id` = '".$_REQUEST['user_id']."' AND t.`id` = tf.`track_id` ORDER BY t.`artist` DESC");
    if($sel->num_rows > 0)
    {
        $favorite = mysqli_fetch_all($sel,MYSQLI_ASSOC);
    }
    else{
        $favorite = array();
    }

    /* recent */
    //$sel = $con->query("SELECT * FROM tbl_track WHERE status=1 AND id NOT IN(SELECT track_id FROM tbl_track_view WHERE user_id='".$_REQUEST['user_id']."') ORDER BY artist DESC");
    $sel = $con->query("SELECT * FROM tbl_track WHERE status=1 ORDER BY id DESC");
    if($sel->num_rows > 0)
    {
        $recent = mysqli_fetch_all($sel,MYSQLI_ASSOC);
    }
    else{
        $recent = array();
    }

    /* polular */

    $sel = $con->query("SELECT tv.`track_id`,t.*, COUNT(tv.`track_id`) as total_view FROM tbl_track_view as tv,tbl_track as t WHERE tv.`track_id`=t.`id` AND tv.`track_id` IN(SELECT track_id FROM tbl_track_view WHERE user_id='".$_REQUEST['user_id']."') GROUP BY tv.`track_id` HAVING COUNT(tv.`track_id`)>0");
    if($sel->num_rows > 0)
    {
        $popular = mysqli_fetch_all($sel,MYSQLI_ASSOC);
    }
    else{
        $popular = array();
    }

    /*recent count*/
    $sel_c = $con->query("SELECT id FROM tbl_track WHERE status = 1 AND created_at >= '".$user_data['recently_visit_at']."'");

    $msg["status"] = true;
    $msg["message"] = "Success.";
    $msg['favorite']=$favorite;
    $msg['recent']=$recent;
    $msg['recent_count']=$sel_c->num_rows;
    $msg['popular']=$popular;
    
}
else{
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
?>