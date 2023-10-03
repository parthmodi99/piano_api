<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;


// if ($timezone!='') {
//     date_default_timezone_set($_REQUEST['timezone']);
// }

$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $favorites = $con->query("SELECT * FROM tbl_speed_training_favorites WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id='".$_REQUEST['lession_id']."'");

    $result = [];

    if ($favorites->num_rows > 0) {
        for ($favorites_result = array(); $row = $favorites->fetch_assoc(); $favorites_result[] = $row);

        foreach ($favorites_result as $key => $value) {
            //$result[$key] = $value;
            $sel = $con->query("SELECT * FROM tbl_patterns WHERE id='".$value['patterns_id']."'");
            $row = $sel->fetch_assoc();

            $bpm = $con->query("SELECT * FROM tbl_bpm WHERE `user_id`='".$value['user_id']."' AND lession_id='".$value['lession_id']."'AND patterns_id='".$value['patterns_id']."'");
            if ($bpm->num_rows > 0) {
                $record = $bpm->fetch_assoc();
                $row['bpm_detail'] = $record['bpm'];
            } else {
                $row['bpm_detail'] = '80';
            }

            $result[] = $row;
        }

        $msg['message'] = 'Success';
        $msg['data'] = $result;
        $msg['status'] = true;
    } else {
        $msg['message'] = 'No data found.';
        $msg['data'] = new arrayobject();
        $msg['status'] = false;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
