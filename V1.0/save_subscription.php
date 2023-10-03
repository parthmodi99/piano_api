<?php

include("dbconfig.php");
//include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;


$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['device_type']) && $_REQUEST['device_type'] != '' && isset($_REQUEST['purchase_token']) && $_REQUEST['purchase_token'] != '') {
    

    $save_subscription = $con->query("INSERT INTO tbl_subscription(`user_id`,`device_type`,`purchase_token`,`subscription_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['device_type']."','".mysqli_real_escape_string($con,$_REQUEST['purchase_token'])."','".$cur_date."')");

    if ($save_subscription) {

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
