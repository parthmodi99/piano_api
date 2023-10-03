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
        
        if ($chk_user_goal->num_rows > 0) {
            $goal_record = $chk_user_goal->fetch_assoc();
        }else{
            $add_goal = $con->query("INSERT INTO tbl_user_goal(`user_id`,`daily_goal`) VALUES('".$_REQUEST['user_id']."','0:30')");

            $chk_user_goal_new = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");

            $goal_record = $chk_user_goal_new->fetch_assoc();
        }

        $chk_timer = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
        $timer_record = $chk_timer->fetch_assoc();

        
        $result['user_id'] = $goal_record['user_id'];
        $result['daily_goal'] = $goal_record['daily_goal'];

        if ($chk_timer->num_rows > 0) {
            $test = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' AND is_dayTracked = 1 AND  updated_at != 'null' ORDER BY `created_at` DESC");
            $test2 = $test->fetch_assoc();

            if ($test->num_rows > 0) {
                $timestamp_update = $test2['updated_at'];
                $splitTimeStamp_update = explode(" ",$timestamp_update); 

                if($splitTimeStamp_update[0] < $cur_date){
                    $day = $timer_record['day'] + 1;

                    $update = $con->query("UPDATE tbl_user_goal_timer SET  `day`='". $day ."', `is_dayTracked`='0', updated_at='".$cur_date_time."' WHERE id='".$timer_record['id']."'");

                    $update2 = $con->query("UPDATE tbl_user_goal_timer SET `is_dayTracked`='0' WHERE user_id='".$_REQUEST['user_id']."'");

                    $chk_timer = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
                    $timer_record = $chk_timer->fetch_assoc();
                }
            }
                
            $timestamp = $timer_record['created_at'];
            $splitTimeStamp = explode(" ",$timestamp); 

            if($splitTimeStamp[0] == $cur_date){
                $result['goal_timer'] = $timer_record['goal_timer'];
            }else{
                $result['goal_timer'] = '00:00';
                $result['day'] = '1';
            }
            
            $result['day'] = $timer_record['day'];

            if($timer_record['is_dayTracked'] == 1 && $splitTimeStamp[0] == $cur_date){
                $result['is_dayTracked'] = true;
            }else{
                $result['is_dayTracked'] = false;
            }
        }else{
            $result['goal_timer'] = '00:00';
            $result['day'] = '1';
            $result['is_dayTracked'] = false;
        }

        $msg['message'] = 'Success';
        $msg['data'] = $result;
        $msg['status'] = true;









        

        // if ($chk_user_goal->num_rows > 0) {
        //     $result['user_id'] = $value['user_id'];
        //     $result['daily_goal'] = $value['daily_goal'];

        //     // $is_true = false;
        //     if ($chk_timer->num_rows > 0) {

        //         $test = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' AND is_dayTracked = 1 AND  updated_at != 'null' ORDER BY `created_at` DESC");
        //         $test2 = $test->fetch_assoc();

        //         if ($test->num_rows > 0) {
        //             $timestamp_update = $test2['updated_at'];
        //             $splitTimeStamp_update = explode(" ",$timestamp_update); 

        //             if($splitTimeStamp_update[0] < $cur_date){
        //                 $day = $timer_record['day'] + 1;
    
        //                 $update = $con->query("UPDATE tbl_user_goal_timer SET  `day`='". $day ."', `is_dayTracked`='0', updated_at='".$cur_date_time."' WHERE id='".$timer_record['id']."'");
    
        //                 $update2 = $con->query("UPDATE tbl_user_goal_timer SET `is_dayTracked`='0' WHERE user_id='".$_REQUEST['user_id']."'");
    
        //                 $chk_timer = $con->query("SELECT * FROM tbl_user_goal_timer WHERE user_id='".$_REQUEST['user_id']."' ORDER BY `created_at` DESC");
        //                 $timer_record = $chk_timer->fetch_assoc();
        //             }
        //         }
                
        //         $timestamp = $timer_record['created_at'];
        //         $splitTimeStamp = explode(" ",$timestamp); 

        //         if($splitTimeStamp[0] == $cur_date){
        //             $result['goal_timer'] = $timer_record['goal_timer'];
        //         }else{
        //             $result['goal_timer'] = '00:00';
        //             $result['day'] = '1';
        //         }
                
        //         $result['day'] = $timer_record['day'];

        //         if($timer_record['is_dayTracked'] == 1 && $splitTimeStamp[0] == $cur_date){
        //             $result['is_dayTracked'] = true;
        //         }else{
        //             $result['is_dayTracked'] = false;
        //         }

        //     }else{
        //         $result['goal_timer'] = '00:00';
        //         $result['day'] = '1';
        //         $result['is_dayTracked'] = false;
        //     }
        //     $msg['message'] = 'Success';
        //     $msg['data'] = $result;
        //     $msg['status'] = true;
        // }else{
        //     $result['user_id'] = $_REQUEST['user_id'];
        //     $result['daily_goal'] = '00:30';
        //     $result['goal_timer'] = '00:00';
        //     $result['day'] = '1';
        //     $result['is_dayTracked'] = false;
        // }
        // $msg['message'] = 'Success';
        // $msg['data'] = $result;
        // $msg['status'] = true;
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
