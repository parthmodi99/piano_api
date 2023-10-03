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

        $chk_user = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
        $record = $chk_user->fetch_assoc();

        if ($chk_user->num_rows == 0) {
            //Insert
            $add_goal = $con->query("INSERT INTO tbl_user_goal_timer(`user_id`,`goal_timer`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['goal_timer']."')");

            if ($add_goal) {

                // $chk_user_goal = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");

                // if ($chk_user_goal->num_rows == 0) {
                //     $add_goal = $con->query("INSERT INTO tbl_user_goal(`user_id`,`daily_goal`) VALUES('".$_REQUEST['user_id']."','00:30')");
                // }

                $msg['message']='Timer added successfully';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        }else{
            //Update            
            $timestamp = $record['created_at'];         
            $splitTimeStamp = explode(" ",$timestamp); 

            if($splitTimeStamp[0] == $cur_date && $record['is_dayTracked'] == 0){
                if($record['goal_timer'] < $_REQUEST['goal_timer']){
                    $update = $con->query("UPDATE tbl_user_goal_timer SET goal_timer='".$_REQUEST['goal_timer']."' WHERE id='".$record['id']."'");

                    if ($update) {
                        $msg['message']='Timer updated successfully';
                        $msg['data']=new arrayobject();
                        $msg['status']=true;
                    }
                }else{
                    $msg['message']='New timer is bigger then pervious timer.';
                    $msg['data']=new arrayobject();
                    $msg['status']=false;
                }                
            }else{
                $rowSQL = $con->query("SELECT MAX(day) AS max_day, MAX(is_dayTracked) AS max_track FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."'");
                $recordsql = $rowSQL->fetch_assoc();
                // $largestNumber = $recordsql['max_day'];

                $add_new_goal = $con->query("INSERT INTO tbl_user_goal_timer(`user_id`,`goal_timer`,`day`,`is_dayTracked`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['goal_timer']."','".$recordsql['max_day']."','".$recordsql['max_track']."')");

                if ($add_new_goal) {
                    $msg['message']='New Timer added.';
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
