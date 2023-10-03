<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

$lession = $con->query("SELECT * FROM tbl_lession");
    for ($lession_result = array(); $rows = $lession->fetch_assoc(); $lession_result[] = $rows);

    foreach ($lession_result as $key => $value) {

        if($value['speed_training'] == 1 || $value['unlimited_key_training'] == 1 || $value['ear_training'] == 1 || $value['recognizing'] == 1){
            $update = $con->query("UPDATE tbl_lession SET `lession_type`='training' WHERE id='".$value['id']."'");
        }else{
            $course = $con->query("SELECT * FROM tbl_course WHERE id ='" . $value['course_id']."'");
            $record = $course->fetch_assoc();

            if($record['course_type'] == 'hearing'){
                $update = $con->query("UPDATE tbl_lession SET `lession_type`='normal' WHERE id='".$value['id']."'");
            }else{
                $update = $con->query("UPDATE tbl_lession SET `lession_type`='normal' WHERE id='".$value['id']."'");
            }
        }
        $msg['message']='Record updated successfully';
        $msg['data']=new arrayobject();
        $msg['status']=true;

    }

echo json_encode($msg);
