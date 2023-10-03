<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $check_user = $con->query("SELECT * FROM tbl_user WHERE `id`='".$_REQUEST['user_id']."'");

    if ($check_user->num_rows > 0) {
        $sel_chapter = $con->query("SELECT * FROM tbl_chapter WHERE id='".$_REQUEST['chapter_id']."'");

        if ($sel_chapter->num_rows > 0) {
            // if($_REQUEST['correct_answer'] >= 20){
                // $best_time = $_REQUEST['best_time'];
            // }else{
                // $best_time = '0';
            // }
            
            $add_score = $con->query("INSERT INTO tbl_ear_recognize(`user_id`,`chapter_id`,`correct_answer`,`avg_time`,`best_time`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['chapter_id']."','".$_REQUEST['correct_answer']."','".$_REQUEST['avg_time']."','". $_REQUEST['best_time']."')");

            if ($add_score) {
                $rec_cnt = $con->query("SELECT * FROM tbl_ear_recognize  WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0 AND correct_answer >= '16'");
                $total_rec_cnt = $rec_cnt->num_rows;

                if($total_rec_cnt > 0 && $_REQUEST['lession_id'] != '' && $_REQUEST['course_id'] != ''){

                    $check_exist_or_not = $con->query("SELECT * FROM tbl_indicator WHERE course_id='".$_REQUEST['course_id']."' AND user_id = '".$_REQUEST['user_id']."' AND lession_id='".$_REQUEST['lession_id']."'");

                    if($check_exist_or_not->num_rows > 0){
                        $update_indicator = $con->query("UPDATE tbl_indicator SET status = 2 WHERE lession_id = '".$_REQUEST['lession_id']."' AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");
                    }else{
                        $add_indicator = $con->query("INSERT INTO tbl_indicator(`user_id`,`course_id`,`lession_id`,`status`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['course_id']."','".$_REQUEST['lession_id']."',2)");
                    }
                }

                $count = $con->query("SELECT * FROM tbl_ear_recognize WHERE user_id='".$_REQUEST['user_id']."'");
                $total_exercise = $count->num_rows;
                $update = $con->query("UPDATE tbl_user SET total_exercise='".$total_exercise."' WHERE id='".$_REQUEST['user_id']."'");
                
                $msg['message']='Added Success.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        } else {
            $msg['message']='Chapter not found.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }
    } else {
        $msg['message'] = 'No User found';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
} else {
    $msg['message']='Invalid request..';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
