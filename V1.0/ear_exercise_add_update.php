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
        $sel_chapter = $con->query("SELECT * FROM tbl_lession WHERE id='".$_REQUEST['lession_id']."'");

        if ($sel_chapter->num_rows > 0) {
            $sel = $con->query("SELECT * FROM tbl_ear_exercise WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id='".$_REQUEST['lession_id']."'");

            if ($sel->num_rows == 0) {
                $add_exercise = $con->query("INSERT INTO tbl_ear_exercise(`user_id`,`lession_id`,`no_of_exercise`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['lession_id']."','".$_REQUEST['no_of_exercise']."')");

                if ($add_exercise) {
                    $msg['message']='Added Success.';
                    $msg['data']=new arrayobject();
                    $msg['status']=true;
                }
            } else {
                $record = $sel->fetch_assoc();
                $exercise_id = $record['id'];
                // echo $bpm_id;
                // die;

                $update = $con->query("UPDATE tbl_ear_exercise SET no_of_exercise ='".$_REQUEST['no_of_exercise']."' WHERE id='". $exercise_id ."'");
                if ($update) {
                    $msg['message']='Update Success.';
                    $msg['data']=new arrayobject();
                    $msg['status']=true;
                }
            }
        } else {
            $msg['message']='Lession not found.';
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
