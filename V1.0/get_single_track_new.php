<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

if($timezone!='')
{
  date_default_timezone_set($_REQUEST['timezone']);
}
$cur_date = date('Y-m-d H:i:s');

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['track_id']) && $_REQUEST['track_id'] != '')
{
    $sel = $con->query("SELECT * FROM tbl_track WHERE id='".$_REQUEST['track_id']."'");
    if($sel->num_rows > 0)
    {
        $track = $sel->fetch_assoc();

        $view = $con->query("SELECT * FROM tbl_track_view WHERE track_id='".$_REQUEST['track_id']."' AND user_id='".$_REQUEST['user_id']."'");
        if($view->num_rows == 0)
        {
            $ins_view = $con->query("INSERT INTO tbl_track_view(`track_id`,`user_id`,`created_at`) VALUES('".$_REQUEST['track_id']."','".$_REQUEST['user_id']."','".$cur_date."')");
        }

        //check track is favorite or not
        $fav = $con->query("SELECT * FROM tbl_favorite WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$_REQUEST['track_id']."'");
        if($fav->num_rows == 0)
        {
            $track['is_favorite'] = 0;
        }
        else{
            $track['is_favorite'] = 1;
        }

        //track section details
        $track_section = array();
        $sel_section = $con->query("SELECT * FROM tbl_track_section WHERE track_id='".$_REQUEST['track_id']."'");
        if($sel_section->num_rows > 0)
        {
            while($section = $sel_section->fetch_assoc())
            {
                if(trim($section['section']) == '')
                {
                    $section['section'] = array();
                }
                else{
                    $section['section'] = array_map('trim', explode('-',trim($section['section'])));
                }
                $fl = json_decode($section['first_line'], true);
                $first_line = array();
                for ($i = 1; $i <= 16; $i++)
                { 
                    if($fl[$i] == '')
                    {
                        $val = 'N!A';
                    }
                    else{
                        $val = $fl[$i];
                    }
                    $t = array();
                    $l = new arrayobject();
                    $t[] = ['2','N!A','2','1'];
                    $l[$val] = $t;
                    $first_line[] = $l;
                }
                $section['first_line'] = $first_line;
                //$section['second_line'] = json_decode($section['second_line']);

                $sl = json_decode($section['second_line'], true);
                $second_line = array();
                for ($i = 1; $i <= 64; $i++)
                { 
                    if($sl[$i] == '')
                    {
                        $val = 'N!A';
                    }
                    else{
                        $val = $sl[$i];
                    }
                    $second_line[] = $val;
                }
                $section['second_line'] = $second_line;
                $track_section[] = $section;

            }
        }
        $track['section'] = $track_section;

        //chords status
        $track['chords'] = 'off';
        $sel_chords = $con->query("SELECT * FROM tbl_chords_status WHERE user_id='".$_REQUEST['user_id']."'");
        if($sel_chords->num_rows > 0)
        {
            $chords_data = $sel_chords->fetch_assoc();
            $track['chords'] = $chords_data['status'];
        }


        $msg["status"] = true;
        $msg["message"] = "Success.";
        $msg['data']=$track;
    }
    else{
        $msg['message']='No track found.';
        $msg['data']=new arrayobject();
        $msg['status']=false;
    }
}
else{
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
?>