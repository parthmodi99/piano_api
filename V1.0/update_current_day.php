<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;


$cur_date_time = date('Y-m-d H:i:s');
$cur_date = date('Y-m-d');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $user = $con->query("SELECT * FROM tbl_user WHERE id='".$_REQUEST['user_id']."'");
    if ($user->num_rows > 0) {
        $chk_user_goal = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
        $goal_record = $chk_user_goal->fetch_assoc();

        if($chk_user_goal->num_rows == 0){
            $goal = "00:30";

            $add_zero = explode(":",$goal); 
            $main_goal = sprintf("%02d", $add_zero[0]) . ':'. sprintf("%02d", $add_zero[1]);

            $add_goal = $con->query("INSERT INTO tbl_user_goal(`user_id`,`daily_goal`) VALUES('".$_REQUEST['user_id']."','".$goal."')");

            $chk_user_goal = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
            $goal_record = $chk_user_goal->fetch_assoc();
        }else{
            $goal = $goal_record['daily_goal'];
            $add_zero = explode(":",$goal); 

            $main_goal = sprintf("%02d", $add_zero[0]) . ':'. sprintf("%02d", $add_zero[1]);
        }

        $chk_user_timer = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
        $timer_record = $chk_user_timer->fetch_assoc();
        
        if($chk_user_timer->num_rows == 0){
            $goal_timer = $_REQUEST['goal_timer'];

            $add_goal_timer = $con->query("INSERT INTO tbl_user_goal_timer(`user_id`,`goal_timer`) VALUES('".$_REQUEST['user_id']."','".$goal_timer."')");

            $chk_user_timer = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
            $timer_record = $chk_user_timer->fetch_assoc();
        }else{
            $goal_timer = $timer_record['goal_timer'];
        }
        

        if($goal_timer >= $main_goal){
            if($timer_record['updated_at'] != null){
                $timestamp = $timer_record['updated_at'];         
                $splitTimeStamp = explode(" ",$timestamp); 
            }

            if($timer_record['updated_at'] == null || $splitTimeStamp[0] < $cur_date){
                $update = $con->query("UPDATE tbl_user_goal_timer SET `is_dayTracked`='".$_REQUEST['is_dayTracked']."', updated_at='".$cur_date_time."' WHERE id='".$timer_record['id']."'");

                $update2 = $con->query("UPDATE tbl_user_goal_timer SET `is_dayTracked`='".$_REQUEST['is_dayTracked']."' WHERE user_id='".$_REQUEST['user_id']."'");

                if ($update) {
                    $msg['message']='Day tracked updated successfully';
                    $msg['data']=new arrayobject();
                    $msg['status']=true;
                }
            }else{
                $msg['message']='You can not update day track more then once in a day';
                $msg['data']=new arrayobject();
                $msg['status']=false;
            }

        }else{
            $msg['message']='Fail updated - You did not reach your daily goal';
            $msg['data']=new arrayobject();
            $msg['status']=false;
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
