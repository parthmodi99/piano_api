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
            if($_REQUEST['major_minor'] != ''){
                $sel = $con->query("SELECT *, trim(avg_time/1000)+0 as new_avg_time, trim(best_time/1000)+0 as new_best_time FROM tbl_ear_score WHERE `user_id`='".$_REQUEST['user_id']."' AND  `chapter_id`='".$_REQUEST['chapter_id']."'  AND  `level`='".$_REQUEST['level']."' AND type= '".$_REQUEST['major_minor']."'");
                $result = [];
        
                if ($sel->num_rows > 0) {
                    for ($score_result = array(); $row = $sel->fetch_assoc(); $score_result[] = $row);
        
                    foreach ($score_result as $value) {
                        $test =  floor($value['new_avg_time']*100)/100;
                        $row['best_time'] = floor($value['new_best_time']*100)/100; ;
                        $row['avg_time'] = $value['correct_answer'] . '/'. floor($value['new_avg_time']*100)/100;
                        $result[] = $row;
                    }
        
                    $msg['message'] = 'Success';
                    $msg['data'] = $result;
                    $msg['status'] = true;
                } else {
                    $msg['message'] = 'No Record found';
                    $msg['data']=new arrayobject();
                    $msg['status']=false;
                }
            }else{
                $sel = $con->query("SELECT *, trim(avg_time/1000)+0 as new_avg_time, trim(best_time/1000)+0 as new_best_time FROM tbl_ear_recognize WHERE `user_id`='".$_REQUEST['user_id']."' AND  `chapter_id`='".$_REQUEST['chapter_id']."'");
                $result = [];
    
                if ($sel->num_rows > 0) {
                    for ($score_result = array(); $row = $sel->fetch_assoc(); $score_result[] = $row);
        
                    foreach ($score_result as $value) {
                            $row['best_time'] = $value['new_best_time'];
                            $row['avg_time'] = $value['correct_answer'] . '/'. $value['new_avg_time'];
                            $result[] = $row;
                    }
        
                    $msg['message'] = 'Success';
                    $msg['data'] = $result;
                    $msg['status'] = true;
                } else {
                    $msg['message'] = 'No Record found';
                    $msg['data']=new arrayobject();
                    $msg['status']=false;
                }
            }
        }else {
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
