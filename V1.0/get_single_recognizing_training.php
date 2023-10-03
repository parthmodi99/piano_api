<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

$sel_lession = $con->query("SELECT * FROM tbl_chapter_recognizing WHERE chapter_id='" . $_REQUEST['chapter_id'] ."'");

if ($sel_lession->num_rows > 0) {
    for ($i=1;$i<=12; $i++) {
        $reco_chapter_detail = $con->query("SELECT * FROM tbl_chapter_recognizing WHERE chapter_id='" . $_REQUEST['chapter_id'] ."' and key_name='audio_" .$i."'");
        for ($reco_chapter_detail_result = array(); $row_chapter = $reco_chapter_detail->fetch_assoc(); $reco_chapter_detail_result[] = $row_chapter);

        $result[] = $reco_chapter_detail_result;
    }

    $msg['message'] = 'Success';
    $msg['data'] = $result;
    $msg['status'] = true;
} else {
    $msg['message']='Data not available.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
