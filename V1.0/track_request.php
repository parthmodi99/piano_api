<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;


// if ($timezone!='') {
//     date_default_timezone_set($_REQUEST['timezone']);
// }

$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $trck_request = $con->query("INSERT INTO tbl_track_request(`user_id`,`track_title`,`created_at`) VALUES('".$_REQUEST['user_id']."','".mysqli_real_escape_string($con, trim($_REQUEST['track_title']))."','".$cur_date."')");

    // if (isset($_REQUEST['device_token']) && $_REQUEST['device_token'] != '') {
        $update_user = $con->query("UPDATE tbl_user SET device_token='".mysqli_real_escape_string($con, trim($_REQUEST['device_token']))."',device_type='" . $_REQUEST['device_type'] . "' WHERE id='".$_REQUEST['user_id']."'");
        //,device_type='" . $_REQUEST['device_type'] . "'
    // }
     
    if ($trck_request) {
        $msg['message']='Success.';
        $msg['data']=new arrayobject();
        $msg['status']=true;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
