<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;


$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
   
    $make_active = $con->query("UPDATE tbl_chat SET is_read = 1 WHERE `from_id`='".$_REQUEST['user_id']."' OR to_id ='" . $_REQUEST['user_id']."'");

    $msg['message']='Success.';
    $msg['data']=  new arrayobject();
    $msg['status']=true;   
    
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
