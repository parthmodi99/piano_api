<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

$sel_lession = $con->query("SELECT * FROM tbl_chapter WHERE lession_id='".$_REQUEST['lession_id']."' AND title ='" . $_REQUEST['letter_name']."'");

if ($sel_lession->num_rows > 0) {
    $record_sel_lession = $sel_lession->fetch_assoc();
    $chapter_id = $record_sel_lession['id'];

    $ear_chapter_detail = $con->query("SELECT * FROM tbl_chapter_ear_tarining WHERE chapter_id=" . $chapter_id);
    $cnt = $ear_chapter_detail->num_rows;
    
    for ($ear_chapter_detail_result = array(); $row_chapter = $ear_chapter_detail->fetch_assoc(); $ear_chapter_detail_result[] = $row_chapter);

    // $result = $ear_chapter_detail_result;
    $result['speed'] = array_slice($ear_chapter_detail_result,0,8);
    $result['notes'] = array_slice($ear_chapter_detail_result,8,30);

    $msg['message'] = 'Success';
    $msg['data'] = $result;
    $msg['status'] = true;
} else {
    $msg['message']='Letter not found.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
