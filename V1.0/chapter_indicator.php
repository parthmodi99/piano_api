<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $sel_chapter = $con->query("SELECT * FROM tbl_lession WHERE course_id='".$_REQUEST['course_id']."' AND id='".$_REQUEST['lession_id']."'");

    if ($sel_chapter->num_rows > 0) {
        $sel_indicator = $con->query("SELECT * FROM tbl_chapter_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id='".$_REQUEST['course_id']."' AND lession_id='".$_REQUEST['lession_id']."'");

        if ($sel_indicator->num_rows == 0) {
            $add_indicator = $con->query("INSERT INTO tbl_chapter_indicator(`user_id`,`course_id`,`lession_id`,`chapter_index`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['course_id']."','".$_REQUEST['lession_id']."','".$_REQUEST['chapter_index']."')");
            $last_id = $con->insert_id;

            if ($add_indicator) {
                $msg['message']='Added Success.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        } else {
            $record_sel_indicator = $sel_indicator->fetch_assoc();
            $indicator_id = $record_sel_indicator['id'];

            $update = $con->query("UPDATE tbl_chapter_indicator SET chapter_index = '".$_REQUEST['chapter_index']."'  WHERE id='". $indicator_id ."'");
            if ($update) {
                $msg['message']='Update record.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        }
    } else {
        $msg['message']='Course or lession not found.';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
