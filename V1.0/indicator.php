<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

function make_lession_active($con)
{
    $check_already_complete = $con->query("SELECT id FROM tbl_indicator WHERE status = 2 AND lession_id = '".$_REQUEST['lession_id']."' AND user_id='".$_REQUEST['user_id']."' AND course_id = '".$_REQUEST['course_id']."'");
    if($check_already_complete->num_rows == 0){
        $make_other_grey = $con->query("UPDATE tbl_indicator SET status = 0 WHERE status = 1 AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");

        $make_current_active = $con->query("UPDATE tbl_indicator SET status = 1 WHERE lession_id = '".$_REQUEST['lession_id']."' AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");
    }

    return true;
}

function make_lession_complete($con)
{

    $check_already_complete = $con->query("SELECT id FROM tbl_indicator WHERE status = 2 AND lession_id = '".$_REQUEST['lession_id']."' AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");
    if($check_already_complete->num_rows == 0){

        $make_other_grey = $con->query("UPDATE tbl_indicator SET status = 0 WHERE status = 1 AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");

        $make_current_complete = $con->query("UPDATE tbl_indicator SET status = 2 WHERE lession_id = '".$_REQUEST['lession_id']."' AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");

        $sel_user_input = $con->query("SELECT * FROM tbl_lession WHERE course_id = '".$_REQUEST['course_id']."' AND id = '". $_REQUEST['lession_id']."'");
        $user_input_lession = $sel_user_input->fetch_assoc();

        $course_check = $con->query("SELECT * FROM tbl_course WHERE course_id = '".$_REQUEST['course_id']."'");
        $user_input_course = $course_check->fetch_assoc();

        if($user_input_course['course_type'] == 'hearing'){
            $lession_type = 'training';
        }else{
            $lession_type = 'normal';
        }
        
        $select_next_ids = $con->query("SELECT * FROM tbl_lession WHERE course_id='".$_REQUEST['course_id']."' AND position > '".$user_input_lession['position']."' AND lession_type = '".$lession_type."' AND id NOT IN(SELECT lession_id FROM tbl_indicator WHERE course_id='".$_REQUEST['course_id']."' AND user_id = '".$_REQUEST['user_id']."' AND status=2) ORDER BY position ASC");
        if($select_next_ids->num_rows > 0){
            //make next to current one
            while($next_id = $select_next_ids->fetch_assoc()){
                $check_exist_or_not = $con->query("SELECT * FROM tbl_indicator WHERE course_id='".$_REQUEST['course_id']."' AND user_id = '".$_REQUEST['user_id']."' AND lession_id='".$next_id['id']."'");
                if($check_exist_or_not->num_rows > 0){
                    $make_active = $con->query("UPDATE tbl_indicator SET status = 1 WHERE lession_id = '".$next_id['id']."' AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");
                }else{
                    $add_indicator = $con->query("INSERT INTO tbl_indicator(`user_id`,`course_id`,`lession_id`,`status`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['course_id']."','".$next_id['id']."',1)");
                }
                break;
            }
        }else{
            //make previous one to active
            $select_previous_ids = $con->query("SELECT * FROM tbl_lession WHERE course_id='".$_REQUEST['course_id']."' AND position < '".$user_input_lession['position']."'  AND lession_type = '".$lession_type."' AND id NOT IN(SELECT lession_id FROM tbl_indicator WHERE course_id='".$_REQUEST['course_id']."' AND user_id = '".$_REQUEST['user_id']."' AND status=2) ORDER BY position ASC");
            if($select_previous_ids->num_rows > 0){
                while($previous = $select_previous_ids->fetch_assoc()){
                    $check_exist_or_not = $con->query("SELECT * FROM tbl_indicator WHERE course_id='".$_REQUEST['course_id']."' AND user_id = '".$_REQUEST['user_id']."' AND lession_id='".$previous['id']."'");
                    if($check_exist_or_not->num_rows > 0){
                        $make_active = $con->query("UPDATE tbl_indicator SET status = 1 WHERE lession_id = '".$previous['id']."' AND user_id='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."'");
                    }else{
                        $add_indicator = $con->query("INSERT INTO tbl_indicator(`user_id`,`course_id`,`lession_id`,`status`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['course_id']."','".$previous['id']."',1)");
                    }
                    break;
                }
            }
        }

    }
    return true;
}



$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['course_id']) && $_REQUEST['course_id'] != '' && isset($_REQUEST['lession_id']) && $_REQUEST['lession_id'] != '') {
    $sel_course = $con->query("SELECT * FROM tbl_course WHERE id='".$_REQUEST['course_id']."'");
    if ($sel_course->num_rows > 0) {
        $course = $sel_course->fetch_assoc();

        if($course['course_type'] == 'hearing'){
            if($_REQUEST['correct_answer'] >= 16){
                
                $status = $_REQUEST['status'];
            }else{
                $status = '1';
            }
        }else{
            $status = $_REQUEST['status'];
        }

        $sel_lession = $con->query("SELECT * FROM tbl_lession WHERE course_id='".$_REQUEST['course_id']."' AND id='".$_REQUEST['lession_id']."'");
    
        if ($sel_lession->num_rows > 0) {
            $sel_indicator = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."' AND lession_id='".$_REQUEST['lession_id']."'");
    
            if ($sel_indicator->num_rows == 0) {
                $add_indicator = $con->query("INSERT INTO tbl_indicator(`user_id`,`course_id`,`lession_id`,`status`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['course_id']."','".$_REQUEST['lession_id']."','".$status."')");
                $last_id = $con->insert_id;
    
                if($status == 1){
                    //currently active 
                    make_lession_active($con);
    
                }elseif($status == 2){
                    //completed
                    make_lession_complete($con);
    
                }
    
                $msg['message']='Update record.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
                
            } else {
                $record_sel_indicator = $sel_indicator->fetch_assoc();
                $indicator_id = $record_sel_indicator['id'];
                
                if($status == 1){
                    //currently active 
                    make_lession_active($con);
    
                }elseif($status == 2){
                    //completed
                    make_lession_complete($con);
    
                }
    
                $msg['message']='Update record.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        } else {
            $msg['message']='Course or lession not found.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }
    }else{
        $msg['message']='Course not found.';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
