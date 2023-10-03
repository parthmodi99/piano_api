<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("dbconfig.php");
include("dbfunction.php");

$msg['message'] = 'Failed.';
$msg['data'] = new arrayobject();
$msg['status'] = false;

$cur_date = date('Y-m-d H:i:s');

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$course = $con->query("SELECT * FROM tbl_course");
for ($course_result = array(); $row = $course->fetch_assoc(); $course_result[] = $row);

$lession = $con->query("SELECT * FROM tbl_lession");
for ($lession_result = array(); $rows = $lession->fetch_assoc(); $lession_result[] = $rows);

$chapter = $con->query("SELECT * FROM tbl_chapter");
for ($chapter_result = array(); $row = $chapter->fetch_assoc(); $chapter_result[] = $row);

$patterns = $con->query("SELECT * FROM tbl_patterns");
for ($patterns_result = array(); $row = $patterns->fetch_assoc(); $patterns_result[] = $row);

$bpm = $con->query("SELECT * FROM tbl_bpm");
for ($bpm_result = array(); $row = $bpm->fetch_assoc(); $bpm_result[] = $row);



//echo "<pre>";

foreach ($chapter_result as $key => $value) {
    $chapter_detail = $con->query("SELECT * FROM tbl_chapter_detail WHERE chapter_id=" . $value['id'] . " LIMIT 10");

    for ($chapter_detail_result = array(); $row_chapter = $chapter_detail->fetch_assoc(); $chapter_detail_result[] = $row_chapter);
    
//    print_r($chapter_detail_result);
    $chapter_result[$key]['chapter_detail'] = $chapter_detail_result;
}

foreach ($lession_result as $key => $value) {
    $lession_detail = $con->query("SELECT * FROM tbl_lession_details WHERE lession_id=" . $value['id'] . " LIMIT 10");

    for ($lession_detail_result = array(); $row_lession = $lession_detail->fetch_assoc(); $lession_detail_result[] = $row_lession);
    
//    print_r($lession_detail_result);
    $lession_result[$key]['lession_detail'] = $lession_detail_result;
}

foreach ($patterns_result as $key => $value) {
    $bpm_detail = $con->query("SELECT * FROM tbl_bpm WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id='".$value['lession_id']."' AND patterns_id='".$value['id']."'");

    if ($bpm_detail->num_rows > 0) {
        $row_bpm = $bpm_detail->fetch_assoc();
        $bpm_detail_result = $row_bpm['bpm'];
    
        $patterns_result[$key]['bpm_detail'] = $bpm_detail_result;
    }else{
        $patterns_result[$key]['bpm_detail'] = '800';
    }
}


$result = [];

function getLession($array, $course_id)
{
    $list = array();
    foreach ($array as $key => $value) {
        if ($course_id == $value['course_id']) {
            $list[] = $value;
        }
    }

    return $list ? $list : null;
}

function getChapter($array, $lession_id)
{
    $list = array();
    foreach ($array as $key => $value) {
        if ($lession_id == $value['lession_id']) {
            $list[] = $value;
        }
    }
    
    return $list ? $list : null;
}

function getPatterns($array, $lession_id)
{
    $list = array();
    foreach ($array as $key => $value) {
        if ($lession_id == $value['lession_id']) {
            $list[] = $value;
        }

        /* foreach ($bpm_result as $key => $value2) {
            if ($value['id'] == $value2['patterns_id']) {
                $sel = $con->query("SELECT * FROM tbl_bpm WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id='".$value['lession_id']."' AND patterns_id='".$value['id']."'");
                // print_r($list);
                if ($sel->num_rows > 0) {
                    $list[]['bpm'] = "yes";
                } else {
                    $list[]['bpm'] = "no";
                }
            }
        } */
    }

    

    // $list[] = getBpm($bpm_result, $lession_id);
    
    return $list ? $list : [];
}

/* function getBpm($array, $lession_id)
{
    // $list = array();
    // foreach ($array as $key => $value) {
    //     if ($lession_id == $value['lession_id']) {
    //         $list[]["BPM"] = $value['bpm'];
    //     }
    // }

    $list = "yes";
    return $list ? $list : [];
} */



foreach ($course_result as $key => $value) {
    $result[$key] = $value;
    $result[$key]['lession'] = getLession($lession_result, $value['id']);

    if ($result[$key]['lession']) {
        foreach ($result[$key]['lession'] as $key1 => $value1) {
            $result[$key]['lession'][$key1]['chapter'] = getChapter($chapter_result, $value1['id']);

            $result[$key]['lession'][$key1]['patterns'] = getPatterns($patterns_result, $value1['id']);

            // $result[$key]['lession'][$key1]['bpm'] = getBpm($bpm_result, $value1['id']);
        }
    }
}


$msg['message'] = 'Success';
$msg['data'] = $result;
$msg['status'] = true;


$show_json = json_encode($msg);
if (json_last_error_msg() == "Malformed UTF-8 characters, possibly incorrectly encoded") {
    $show_json = json_encode($msg, JSON_INVALID_UTF8_SUBSTITUTE);
}

if ($show_json !== false) {
    echo($show_json);
} else {
    die("json_encode fail: " . json_last_error_msg());
}

die;
