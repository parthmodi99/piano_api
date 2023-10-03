<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;


// $cur_date = date('Y-m-d H:i:s');
$cur_date = date('Y-m-d');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $user = $con->query("SELECT * FROM tbl_user WHERE id='".$_REQUEST['user_id']."'");
    if ($user->num_rows > 0) {

        $chk_user = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
        $record = $chk_user->fetch_assoc();

        if ($chk_user->num_rows == 0) {
            //Insert
            $add_goal = $con->query("INSERT INTO tbl_user_goal(`user_id`,`daily_goal`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['daily_goal']."')");

            if ($add_goal) {
                $msg['message']='Goal added successfully';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        }else{
            //Update

            $chk_user_timer = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
            $timer_record = $chk_user_timer->fetch_assoc();

            $timestamp = $record['created_at'];
            $splitTimeStamp = explode(" ",$timestamp); 

            if($splitTimeStamp[0] == $cur_date && $timer_record['is_dayTracked'] == '0'){
                $update = $con->query("UPDATE tbl_user_goal SET daily_goal='".$_REQUEST['daily_goal']."' WHERE id='".$record['id']."'");

                if ($update) {
                    $msg['message']='Goal updated successfully';
                    $msg['data']=new arrayobject();
                    $msg['status']=true;
                }
            }else{
                $add_new_goal = $con->query("INSERT INTO tbl_user_goal(`user_id`,`daily_goal`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['daily_goal']."')");

                if ($add_new_goal) {
                    $msg['message']='New Goal added successfully';
                    $msg['data']=new arrayobject();
                    $msg['status']=true;
                }
            }

            
        }
    }else{
        $msg['message']='User not found.';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
