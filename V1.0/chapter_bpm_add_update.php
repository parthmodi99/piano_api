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
    $sel_chapt = $con->query("SELECT * FROM tbl_chapter WHERE id='".$_REQUEST['chapter_id']."' ");
    if ($sel_chapt->num_rows > 0) {
        $sel_metronome = $con->query("SELECT * FROM tbl_chapter_detail WHERE chapter_id='".$_REQUEST['chapter_id']."' AND type='metronome'");

        if ($sel_metronome->num_rows > 0) {
            $rec= $sel_metronome->fetch_assoc();
            $chapter_detail_id = $rec['id'];
            
            $sel = $con->query("SELECT * FROM tbl_chapter_bpm WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND chapter_detail_id='".$chapter_detail_id."'");

            if ($sel->num_rows == 0) {
                $bpm = $_REQUEST['bpm'] ? $_REQUEST['bpm'] : '80';
                $add_bpm = $con->query("INSERT INTO tbl_chapter_bpm(`user_id`,`chapter_id`,`chapter_detail_id`,`bpm`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['chapter_id']."','".$chapter_detail_id."','".$bpm."','".$cur_date."')");

                if ($add_bpm) {
                    $msg['message']='Added Success.';
                    $msg['data']=new arrayobject();
                    $msg['status']=true;
                }
            } else {
                $record = $sel->fetch_assoc();
                $chpter_bpm_id = $record['id'];
                // echo $bpm_id;
                // die;

                $update = $con->query("UPDATE tbl_chapter_bpm SET bpm ='".$_REQUEST['bpm']."' WHERE id='". $chpter_bpm_id ."'");
                if ($update) {
                    $msg['message']='Update Success.';
                    $msg['data']=new arrayobject();
                    $msg['status']=true;
                }
            }
        } else {
            $msg['message']='Metronome not Avaliable of this chapter';
            $msg['data']=new arrayobject();
            $msg['status']=true;
        }
    } else {
        $msg['message']='Chapter not found.';
        $msg['data']=new arrayobject();
        $msg['status']=true;
    }
} else {
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
