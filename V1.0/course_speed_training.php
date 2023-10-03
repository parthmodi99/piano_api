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


$check_user = $con->query("SELECT * FROM tbl_user WHERE `id`='".$_REQUEST['user_id']."'");

if ($check_user->num_rows > 0) {
    //$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

    $course = $con->query("SELECT * FROM tbl_course ORDER BY -position DESC");
    // $course = $con->query("SELECT * FROM tbl_course");
    for ($course_result = array(); $row = $course->fetch_assoc(); $course_result[] = $row);

    $lession = $con->query("SELECT * FROM tbl_lession ORDER BY -position DESC");
    for ($lession_result = array(); $rows = $lession->fetch_assoc(); $lession_result[] = $rows);

    $chapter = $con->query("SELECT * FROM tbl_chapter ORDER BY -position DESC");
    for ($chapter_result = array(); $row = $chapter->fetch_assoc(); $chapter_result[] = $row);

    $patterns = $con->query("SELECT * FROM tbl_patterns");
    for ($patterns_result = array(); $row = $patterns->fetch_assoc(); $patterns_result[] = $row);

    $bpm = $con->query("SELECT * FROM tbl_bpm");
    for ($bpm_result = array(); $row = $bpm->fetch_assoc(); $bpm_result[] = $row);



    //echo "<pre>";

    foreach ($chapter_result as $key => $value) {
        $chapter_detail = $con->query("SELECT * FROM tbl_chapter_detail WHERE chapter_id=" . $value['id']);
        // $chapter_detail = $con->query("SELECT * FROM tbl_chapter_detail WHERE chapter_id=" . $value['id'] . " LIMIT 10");

        for ($chapter_detail_result = array(); $row_chapter = $chapter_detail->fetch_assoc(); $chapter_detail_result[] = $row_chapter);

//    print_r($chapter_detail_result);

        $chapter_result[$key]['chapter_detail'] = $chapter_detail_result;

        foreach ($chapter_result[$key]['chapter_detail'] as $key2 => $value2) {
            if ($value2['type'] == 'metronome') {
                $chapter_bpm = $con->query("SELECT * FROM tbl_chapter_bpm WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$value['id']."' AND chapter_detail_id='".$value2['id']."'");
                if ($chapter_bpm->num_rows > 0) {
                    $row_bpm = $chapter_bpm->fetch_assoc();
                    $chapter_result[$key]['chapter_detail'][$key2]['chapter_bpm'] = $row_bpm['bpm'];
                } else {
                    $chapter_result[$key]['chapter_detail'][$key2]['chapter_bpm'] = '80';
                }
            }
        }
    }

    foreach ($lession_result as $key => $value) {
        $lession_detail = $con->query("SELECT * FROM tbl_lession_details WHERE lession_id=" . $value['id']);
        // $lession_detail = $con->query("SELECT * FROM tbl_lession_details WHERE lession_id=" . $value['id'] . " LIMIT 10");

        for ($lession_detail_result = array(); $row_lession = $lession_detail->fetch_assoc(); $lession_detail_result[] = $row_lession);

        // $lession_result[$key]['lession_detail'] = $lession_detail_result;
        $lession_result[$key]['chapter_detail'] = $lession_detail_result;
    }

    foreach ($patterns_result as $key => $value) {
        $bpm_detail = $con->query("SELECT * FROM tbl_bpm WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id='".$value['lession_id']."' AND patterns_id='".$value['id']."'");

        if ($bpm_detail->num_rows > 0) {
            $row_bpm = $bpm_detail->fetch_assoc();
            $bpm_detail_result = $row_bpm['bpm'];

            $patterns_result[$key]['bpm_detail'] = $bpm_detail_result;
        } else {
            $patterns_result[$key]['bpm_detail'] = '80';
        }
        $check_favorit = $con->query("SELECT * FROM tbl_speed_training_favorites WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id='".$value['lession_id']."' AND patterns_id='".$value['id']."'");

        if ($check_favorit->num_rows > 0) {
            $patterns_result[$key]['is_favorites'] = '1';
        } else {
            $patterns_result[$key]['is_favorites'] = '0';
        }
    }


    $result = [];

    $sel = $con->query("SELECT * FROM tbl_course_title");
    if ($sel->num_rows > 0) {
        $course_title = $sel->fetch_assoc();
    } else {
        $course_title = new arrayobject();
    }

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
        }

        return $list ? $list : [];
    }

    foreach ($course_result as $key => $value) {
        $result[$key] = $value;
        
        /*Total Complete Lession */
            $count = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."' AND status = '2'");
            $result[$key]['total_complete_lession'] = $count->num_rows;
        /* end */

        //TRY-1
        $test_count = 0;

        if($value['course_type'] == 'hearing'){
            $check_lession_type = "training";
        }else{
            $check_lession_type = "normal";
        }

        //total_valid_lession
            $count_training_lession = $con->query("SELECT * FROM tbl_lession WHERE `course_id`='".$value['id']."' AND lession_type ='". $check_lession_type ."'  ORDER BY -position DESC");
            // $result[$key]['total_valid_lession'] = $count_training_lession->num_rows;

            for ($valid_lession_result = array(); $valid_lession = $count_training_lession->fetch_assoc(); $valid_lession_result[] = $valid_lession);
            
            $test_array = [];
            foreach ($valid_lession_result as $key5 => $value5) {
                $abc_array = $value5;

                if ($value['coming_soon'] == '0' && $value['name'] == 'The basics') {
                    $abc_array['is_read'] = "1";
                } elseif ($value['coming_soon'] == '0') {
                    $first_chapter_read = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."'");
    
                    if ($first_chapter_read->num_rows > 0) {
                        $chapter_read = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."' AND lession_id ='" . $value5['id']."'");
                        if ($chapter_read->num_rows > 0) {
                            $record = $chapter_read->fetch_assoc();
                            $abc_array['is_read'] = $record['status'];
                        } else {
                            $abc_array['is_read'] = "0";
                        }
                    } else {
                        if ($key5 === array_key_first($valid_lession_result)) {
                            $abc_array['is_read'] = "1";
                        } else {
                            $abc_array['is_read'] = "0";
                        }
                    }
                } else {
                    $abc_array['is_read'] = "0";
                }

                $test_array[] = $abc_array;
            }

            $result[$key]['total_valid_lession'] = $test_array;
        //end 
        
        //total_valid_lession_complete
            $lession_type = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."'");

            if ($lession_type->num_rows > 0) {
                for ($lession_type_result = array(); $lession_typee = $lession_type->fetch_assoc(); $lession_type_result[] = $lession_typee);
            
                foreach ($lession_type_result as $key4 => $value4) {
                    $type_check = $con->query("SELECT l.*, i.status FROM tbl_lession as l join tbl_indicator as i on l.id = i.lession_id WHERE l.id='".$value4['lession_id']."' AND i.course_id ='" . $value4['course_id']."' AND i.user_id='".$_REQUEST['user_id']."'");

                    if ($type_check->num_rows > 0) {
                        $record_lession_type = $type_check->fetch_assoc();

                        if($record_lession_type['lession_type'] == $check_lession_type && $record_lession_type['status'] == 2){
                            $test_count++;
                        }
                    }
                }        
            }        

            $result[$key]['total_valid_lession_complete'] = $test_count;
        //end

        //Continue
        /*Total Lession */
        $chapter_list = $con->query("SELECT * FROM tbl_lession WHERE course_id ='" . $value['id']."' ORDER BY -position DESC ");
        for ($chapter_list_result = array(); $read_chapter = $chapter_list->fetch_assoc(); $chapter_list_result[] = $read_chapter);
        $result[$key]['total_lession'] = $chapter_list_result;

        $previousValue = null;

        foreach ($result[$key]['total_lession'] as $key2 => $value2) {
            if ($value['coming_soon'] == '0' && $value['name'] == 'The basics') {
                $result[$key]['total_lession'][$key2]['is_read'] = "1";
            } elseif ($value['coming_soon'] == '0') {
                $first_chapter_read = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."'");

                if ($first_chapter_read->num_rows > 0) {
                    $chapter_read = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."' AND lession_id ='" . $value2['id']."'");
                    if ($chapter_read->num_rows > 0) {
                        $record = $chapter_read->fetch_assoc();
                        $result[$key]['total_lession'][$key2]['is_read'] = $record['status'];
                    } else {
                        $result[$key]['total_lession'][$key2]['is_read'] = "0";
                    }
                } else {
                    if ($key2 === array_key_first($result[$key]['total_lession'])) {
                        $result[$key]['total_lession'][$key2]['is_read'] = "1";
                    } else {
                        $result[$key]['total_lession'][$key2]['is_read'] = "0";
                    }
                }
            } else {
                $result[$key]['total_lession'][$key2]['is_read'] = "0";
            }
        }
        /* End */

        $result[$key]['lession'] = getLession($lession_result, $value['id']);

        if ($result[$key]['lession']) {
            foreach ($result[$key]['lession'] as $key1 => $value1) {
                $result[$key]['lession'][$key1]['chapter'] = getChapter($chapter_result, $value1['id']);

                /* if ($result[$key]['lession'][$key1]['chapter']) {
                    foreach ($result[$key]['lession'][$key1]['chapter'] as $key3 => $value3) {
                        // $result[$key]['lession'][$key1]['chapter'][$key3]['chapter_is_read'] = $value3['id'];

                        $chap_red = $con->query("SELECT * FROM tbl_chapter_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND lession_id ='" . $value1['id']."' AND chapter_id ='" . $value3['id']."'");

                        if ($chap_red->num_rows > 0) {
                            $rcd = $chap_red->fetch_assoc();
                            $result[$key]['lession'][$key1]['chapter'][$key3]['chapter_is_read'] = '1';
                            $result[$key]['lession'][$key1]['chapter'][$key3]['chapter_index'] = $rcd['chapter_index'];
                        } else {
                            $result[$key]['lession'][$key1]['chapter'][$key3]['chapter_is_read'] = '0';
                            $result[$key]['lession'][$key1]['chapter'][$key3]['chapter_index'] = '0';
                        }
                    }
                } */

                $result[$key]['lession'][$key1]['patterns'] = getPatterns($patterns_result, $value1['id']);

                // $result[$key]['lession'][$key1]['lession_id'] = $value1['id'];
                if ($value['coming_soon'] == '0' && $value['name'] == 'The basics') {
                    $result[$key]['lession'][$key1]['is_read'] = "1";

                    $chap_red = $con->query("SELECT * FROM tbl_chapter_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."' AND lession_id ='" . $value1['id']."'");

                    if ($chap_red->num_rows > 0) {
                        $rcd = $chap_red->fetch_assoc();
                        $result[$key]['lession'][$key1]['chapter_index'] = $rcd['chapter_index'];
                    } else {
                        $result[$key]['lession'][$key1]['chapter_index'] = '0';
                    }
                } elseif ($value['coming_soon'] == '0') {
                    $first_chapter_read = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."'");

                    if ($first_chapter_read->num_rows > 0) {
                        $chapter_read = $con->query("SELECT * FROM tbl_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."' AND lession_id ='" .$value1['id']."'");
                        if ($chapter_read->num_rows > 0) {
                            $record = $chapter_read->fetch_assoc();
                            $result[$key]['lession'][$key1]['is_read'] = $record['status'];
                        } else {
                            $result[$key]['lession'][$key1]['is_read'] = "0";
                        }
                    } else {
                        if ($key1 === array_key_first($result[$key]['lession'])) {
                            $result[$key]['lession'][$key1]['is_read'] = "1";
                        } else {
                            $result[$key]['lession'][$key1]['is_read'] = "0";
                        }
                    }

                    $chap_red = $con->query("SELECT * FROM tbl_chapter_indicator WHERE `user_id`='".$_REQUEST['user_id']."' AND course_id ='" . $value['id']."' AND lession_id ='" . $value1['id']."'");

                    if ($chap_red->num_rows > 0) {
                        $rcd = $chap_red->fetch_assoc();
                        $result[$key]['lession'][$key1]['chapter_index'] = $rcd['chapter_index'];
                    } else {
                        $result[$key]['lession'][$key1]['chapter_index'] = '0';
                    }
                } else {
                    $result[$key]['lession'][$key1]['is_read'] = "0";
                }
            }
        }
    }

    //get total free user count

    $sel_user = $con->query("SELECT id FROM tbl_user WHERE is_pro_user = 0");


    $msg['message'] = 'Success';
    $msg['course_details'] = $course_title;
    $msg['data'] = $result;
    $msg['status'] = true;
    $msg['total_free_user'] = $sel_user->num_rows;
} else {
    $msg['message'] = 'No User found';
    $msg['course_details'] = new arrayobject();
    $msg['data'] = new arrayobject();
    $msg['status'] = false;
}

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
