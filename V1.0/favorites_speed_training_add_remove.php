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
    if ($_REQUEST['is_favorite'] == '1') {
        $sel = $con->query("SELECT * FROM tbl_speed_training_favorites WHERE `user_id`='".$_REQUEST['user_id']."' AND patterns_id='".$_REQUEST['patterns_id']."'");

        if ($sel->num_rows == 0) {
            $add_speed_training_favorites = $con->query("INSERT INTO tbl_speed_training_favorites(`user_id`,`lession_id`,`patterns_id`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['lession_id']."','".$_REQUEST['patterns_id']."','".$cur_date."')");

            if ($add_speed_training_favorites) {
                $msg['message']='Added Success.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        } else {
            $msg['message']='Invalid request or Data Already Exist.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }
    } else {
        $sel = $con->query("SELECT * FROM tbl_speed_training_favorites WHERE `user_id`='".$_REQUEST['user_id']."' AND patterns_id='".$_REQUEST['patterns_id']."'");

        if ($sel->num_rows == 1) {
            $delete_speed_training_favorites = $con->query("DELETE FROM tbl_speed_training_favorites WHERE `user_id`='".$_REQUEST['user_id']."' AND patterns_id='".$_REQUEST['patterns_id']."'");

            if ($delete_speed_training_favorites) {
                $msg['message']='Success Record Deleted.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        } else {
            $msg['message']='Data does not exist.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
