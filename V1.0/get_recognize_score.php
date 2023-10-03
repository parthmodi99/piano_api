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
            $sel = $con->query("SELECT *, max(best_time) as best_timeing FROM tbl_ear_recognize WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0 GROUP BY user_id");

            if ($sel->num_rows > 0) {
                $result = $sel->fetch_assoc();

                //getting correct answer
                $ans_select = $con->query("SELECT * FROM tbl_ear_recognize WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time = '".$result['best_timeing']."'");

                $ans_data = $ans_select->fetch_assoc();


                $d['chapter_id'] = $_REQUEST['chapter_id'];
                $d['correct_answer'] = $ans_data['correct_answer'];
                $d['best_time'] = $result['best_timeing'];

                $msg['message'] = 'Success';
                $msg['data'] = $d;
                $msg['status'] = true;
            } else {
                $msg['message']='No data found.';
                $msg['data']=new arrayobject();
                $msg['status']=false;
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
