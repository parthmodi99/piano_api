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
    $sel_petrn = $con->query("SELECT * FROM tbl_patterns WHERE lession_id='".$_REQUEST['lession_id']."' AND id='".$_REQUEST['patterns_id']."'");

    if ($sel_petrn->num_rows > 0) {
        $sel = $con->query("SELECT * FROM tbl_bpm WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id='".$_REQUEST['lession_id']."' AND patterns_id='".$_REQUEST['patterns_id']."'");

        if ($sel->num_rows == 0) {
            $bpm = $_REQUEST['bpm'] ? $_REQUEST['bpm'] : '80';
            $add_bpm = $con->query("INSERT INTO tbl_bpm(`user_id`,`lession_id`,`patterns_id`,`bpm`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['lession_id']."','".$_REQUEST['patterns_id']."','".$bpm."','".$cur_date."')");

            if ($add_bpm) {
                $msg['message']='Added Success.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        } else {
            $record = $sel->fetch_assoc();
            $bpm_id = $record['id'];
            // echo $bpm_id;
            // die;
    
            $update = $con->query("UPDATE tbl_bpm SET bpm ='".$_REQUEST['bpm']."' WHERE id='". $bpm_id ."'");
            if ($update) {
                $msg['message']='Update Success.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        }
    } else {
        $msg['message']='Patterns not found.';
        $msg['data']=new arrayobject();
        $msg['status']=true;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
