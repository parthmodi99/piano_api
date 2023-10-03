<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

$sel_lession = $con->query("SELECT * FROM tbl_lession WHERE id='".$_REQUEST['lession_id']."'");

if ($sel_lession->num_rows > 0) {
    $record_sel_lession = $sel_lession->fetch_assoc();
    $is_ear_training = $record_sel_lession['ear_training'];

    if($is_ear_training == 1){
        $lession_id = $_REQUEST['lession_id'];

        $sel_chapter = $con->query("SELECT * FROM tbl_chapter WHERE lession_id='".$lession_id."'");
        for ($chapter_result = array(); $row = $sel_chapter->fetch_assoc(); $chapter_result[] = $row);

        foreach ($chapter_result as $key => $value) {
            $result[$key] = $value;

            /*Chapter Details*/
            $chapter_detail = $con->query("SELECT * FROM tbl_chapter_detail WHERE chapter_id=" . $value['id']);
            for ($chapter_detail_result = array(); $row_chapter = $chapter_detail->fetch_assoc(); $chapter_detail_result[] = $row_chapter);

            $result[$key]['chapter_detail'] = $chapter_detail_result;

            /*Ear training Details*/
            $ear_chapter_detail = $con->query("SELECT * FROM tbl_chapter_ear_tarining WHERE chapter_id=" . $value['id']);
            for ($ear_chapter_detail_result = array(); $row_chapter = $ear_chapter_detail->fetch_assoc(); $ear_chapter_detail_result[] = $row_chapter);

            $result[$key]['ear_chapter_detail'] = $ear_chapter_detail_result;

            if($ear_chapter_detail_result[0]["image_type"] == "major"){
                $result[$key]['is_major'] = true;
            }else{
                $result[$key]['is_major'] = false;
            }
            
        }
        
        $msg['message'] = 'Success';
        $msg['data'] = $result;
        $msg['status'] = true;
    }else{
        $msg['message']='Ear training not found.';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
} else {
    $msg['message']='Lession not found.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
