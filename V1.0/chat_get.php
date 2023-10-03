<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;


$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $con->set_charset("utf8");
    $cht_get = $con->query("SELECT * FROM tbl_chat WHERE `from_id`='".$_REQUEST['user_id']."' OR to_id ='" . $_REQUEST['user_id']."'");

    //get unread msg status
    $cht_msg_unread = $con->query("SELECT * FROM tbl_chat WHERE to_id ='" . $_REQUEST['user_id']."' AND is_read = 0");

    if ($cht_get->num_rows > 0) {
        //$make_active = $con->query("UPDATE tbl_chat SET is_read = 1 WHERE `from_id`='".$_REQUEST['user_id']."' OR to_id ='" . $_REQUEST['user_id']."'");

        // $sel = $con->query("SELECT * FROM tbl_chat WHERE `from_id`='".$_REQUEST['user_id']."' OR to_id ='" . $_REQUEST['user_id']."'");

        for ($sel_result = array(); $row_chapter = $cht_get->fetch_assoc(); $sel_result[] = $row_chapter);

        $result[] = $sel_result;

        $msg['message']='Success.';
        $msg['unread_msg'] = $cht_msg_unread->num_rows > 0 ? true : false;
        $msg['data']=  $result;
        $msg['status']=true;
        //print_r($msg); die;
    } else{
        $msg['message']='Success.';
        $msg['unread_msg'] = false;
        $msg['data']=  array();
        $msg['status']=true;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
