<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

$sel_lession = $con->query("SELECT * FROM tbl_ear_exercise  WHERE user_id='".$_REQUEST['user_id']."' AND lession_id ='" . $_REQUEST['lession_id']."'");

if ($sel_lession->num_rows > 0) {
    for ($reco_chapter_detail_result = array(); $row_chapter = $sel_lession->fetch_assoc(); $reco_chapter_detail_result[] = $row_chapter);


    $msg['message'] = 'Success';
    $msg['data'] = $reco_chapter_detail_result;
    $msg['status'] = true;
} else {
    $msg['message']='Data not available.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
