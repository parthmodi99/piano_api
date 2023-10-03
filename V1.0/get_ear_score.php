<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '') {
    $check_user = $con->query("SELECT * FROM tbl_user WHERE `id`='".$_REQUEST['user_id']."'");

    if ($check_user->num_rows > 0) {
        $sel_chapter = $con->query("SELECT * FROM tbl_chapter WHERE id='".$_REQUEST['chapter_id']."'");

        if ($sel_chapter->num_rows > 0) {            
            // $sel = $con->query("SELECT *, max(best_time) as best_timeing FROM tbl_ear_score WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0 AND created_at IN (select max(created_at) from tbl_ear_score GROUP BY level, type) GROUP BY level, type");

            $sel = $con->query("SELECT *, MAX(correct_answer) AS max_correct_answer FROM tbl_ear_score  WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0 GROUP BY level, type");

            // $course = $sel->fetch_assoc();

            /* for ($score_result = array(); $row = $sel->fetch_assoc(); $score_result[] = $row);

            foreach ($score_result as $value) {
                $sel2 = $con->query("SELECT * FROM tbl_ear_score  WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0  AND `level` = '".$value['level']."' and correct_answer = '".$value['max_correct_answer']."'");

                $sel_score2 = $sel2->fetch_assoc();

                if ($sel2->num_rows == 1) {
                    // echo $value['level'] . "=>". $sel_score2['best_time']."<br>";
                   $find_best_time = $sel_score2['best_time']."<br>";
                }else{
                    $sel3 = $con->query("SELECT *, min(best_time) as best_timeing FROM tbl_ear_score  WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0  AND `level` = '".$value['level']."' and correct_answer = '".$value['max_correct_answer']."'");

                    $sel_score3 = $sel3->fetch_assoc();

                    // echo $value['level'] . "=>". $sel_score3['best_timeing']."<br>";
                   $find_best_time = $sel_score3['best_timeing']."<br>";
                }
            }
            die; */
            $result = [];

            if ($sel->num_rows > 0) {
                for ($score_result = array(); $row = $sel->fetch_assoc(); $score_result[] = $row);

                foreach ($score_result as $value) {

                    $sel2 = $con->query("SELECT * FROM tbl_ear_score  WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0  AND `level` = '".$value['level']."' AND type='".$value['type']."' and correct_answer = '".$value['max_correct_answer']."'");

                    $sel_score2 = $sel2->fetch_assoc();

                    if ($sel2->num_rows == 1) {
                        // echo $value['level'] . "=>". $sel_score2['best_time']."<br>";
                        $find_best_time = $sel_score2['best_time'];
                    }else{
                        $sel3 = $con->query("SELECT *, min(best_time) as best_timeing FROM tbl_ear_score  WHERE `user_id`='".$_REQUEST['user_id']."' AND chapter_id='".$_REQUEST['chapter_id']."' AND best_time != 0  AND `level` = '".$value['level']."' AND type='".$value['type']."' and correct_answer = '".$value['max_correct_answer']."'");

                        $sel_score3 = $sel3->fetch_assoc();

                        // echo $value['level'] . "=>". $sel_score3['best_timeing']."<br>";
                        $find_best_time = $sel_score3['best_timeing'];
                    }

                    if ($value['type'] == 'major') {
                        $row['chapter_id'] = $value['chapter_id'];
                        $row['level'] = $value['level'];
                        $row['best_time'] = $find_best_time;
                        $row['correct_answer'] = $value['max_correct_answer'];

                        $major[] = $row;
                        $addi2['major'] = $major;
                    } else {
                        $row['chapter_id'] = $value['chapter_id'];
                        $row['level'] = $value['level'];
                        $row['best_time'] = $find_best_time;
                        $row['correct_answer'] = $value['max_correct_answer'];

                        $minor[] = $row;
                        $addi2['minor'] = $minor;
                    }
                }


                $levels = array('Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5');
                $major_res= [];
                $minor_res= [];
                $cnt = $sel->num_rows;
                // echo $cnt;
                // die;
                for ($i=0; $i< $cnt; $i++) {
                    if ($score_result[$i]['type'] == 'major') {
                        $lavel_major[] = $score_result[$i]['level'];
                        $major_res= $lavel_major;
                    } else {
                        $level_minor[] = $score_result[$i]['level'];
                        $minor_res= $level_minor;
                    }
                }
                

                $diffs1=array_diff($levels, $major_res);
                $diffs2=array_diff($levels, $minor_res);

                if ($diffs1 != '') {
                    foreach ($diffs1 as $diff1) {
                        $row['chapter_id'] = $_REQUEST['chapter_id'];
                        $row['level'] = $diff1;
                        $row['best_time'] = "0";
                        $row['correct_answer'] = "0";

                        $major[] = $row;
                        $addi2['major'] = $major;

                        // $major = $addi2['major'];
                        $level = [];
                        foreach ($addi2['major'] as $key => $row) {
                            $level[$key] = $row['level'];
                        }
                        $a  = array_multisort($level, SORT_ASC, $addi2['major']);
                    }
                }
                // die;
                if ($diffs2 != '') {
                    foreach ($diffs2 as $diff2) {
                        $row['chapter_id'] = $_REQUEST['chapter_id'];
                        $row['level'] = $diff2;
                        $row['best_time'] = "0";
                        $row['correct_answer'] = "0";

                        $minor[] = $row;
                        $addi2['minor'] = $minor;

                        // $minor = $addi2['minor'];
                        $level = [];
                        foreach ($addi2['minor'] as $key => $row) {
                            $level[$key] = $row['level'];
                        }
                        $b  = array_multisort($level, SORT_ASC, $addi2['minor']);
                    }
                }
            } else {
                $levels = array('Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5');

                foreach ($levels as $level) {
                    $row['chapter_id'] = $_REQUEST['chapter_id'];
                    $row['level'] = $level;
                    $row['best_time'] = "0";
                    $row['correct_answer'] = "0";

                    $major[] = $row;
                    $addi2['major'] = $major;
                    $addi2['minor'] = $major;
                }
            }

            // $major = $addi2['major'];
            // $level = [];
            // foreach ($major as $key => $row) {
            //     $level[$key] = $row['level'];
            // }
            // $addi2['a']  = array_multisort($level, SORT_ASC, $major);

            $result[] = $addi2;

            // usort($result, function ($a, $b) {
            //     return $b['level'] <=> $a['level'];
            // });

            // $major = $addi2['major'];
            // $level = [];
            // foreach ($addi2 as $key => $row) {
            //     $level[$key] = $row['level'];
            // }
            // $result[]  = array_multisort($level, SORT_ASC, $addi2);

            $msg['message'] = 'Success';
            $msg['data'] = $result;
            $msg['status'] = true;
        } else {
            $msg['message']='Chapter not found.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }
    } else {
        $msg['message'] = 'No User found';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
} else {
    $msg['message']='Invalid request..';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
