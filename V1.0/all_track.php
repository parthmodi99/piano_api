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
if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '')
{
    $sel = $con->query("SELECT * FROM tbl_track WHERE status=1");
    $all_track = array();
    if($sel->num_rows > 0)
    {
        while($track = $sel->fetch_assoc()){
            //check track is favorite or not
            $fav = $con->query("SELECT * FROM tbl_favorite WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$track['id']."'");
            if($fav->num_rows == 0)
            {
                $track['is_favorite'] = 0;
            }
            else{
                $track['is_favorite'] = 1;
            }

            //track section details
            $track_section = array();
            $sel_section = $con->query("SELECT * FROM tbl_track_section WHERE track_id='".$track['id']."'");
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
                    if($section['same_as'] != '')
                    {
                        $same = explode(':',$section['same_as']);
                        if(isset($same[0]) && $same[0] != '' && isset($same[1]) && $same[1] != '')
                        {
                            $section['same_as'] = trim($same[0]);
                            $section['block'] = trim($same[1]);
                        }
                        else{
                            $section['same_as'] = '';
                            $section['block'] = '';
                        }
                    }
                    else{
                        $section['same_as'] = '';
                        $section['block'] = '';
                    }
                    $fl = json_decode($section['first_line'], true);
                    $first_line = array();
                    for ($i = 1; $i <= 16; $i++)
                    { 
                        if($section['block'] != '')
                        {
                            $same = explode('-',$section['block']);
                            if(isset($same[0]) && $same[0] != '' && isset($same[1]) && $same[1] != '')
                            {
                                if($i >= $same[0] && $i <= $same[1])
                                {
                                    $val = 'SAME';
                                }
                                else{
                                    if($fl[$i] == '')
                                    {
                                        $val = 'N!A';
                                    }
                                    else{
                                        $val = $fl[$i];
                                    }
                                }
                            }
                            else{
                                if($fl[$i] == '')
                                {
                                    $val = 'N!A';
                                }
                                else{
                                    $val = $fl[$i];
                                }
                            }
                        }
                        else{
                            if($fl[$i] == '')
                            {
                                $val = 'N!A';
                            }
                            else{
                                $val = $fl[$i];
                            }
                        }   
                        
                        $first_line[] = $val;
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

            //playlist status
            $sel_playlist = $con->query("SELECT * FROM tbl_playlist_track WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$track['id']."'");
            if($sel_playlist->num_rows > 0)
            {
                $track['in_playlist_status'] = 1;   
            }
            else{
                $track['in_playlist_status'] = 0;
            }

            //mistake status
            $mistake_track = $con->query("SELECT * FROM tbl_mistake_track WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$track['id']."'");
            if($mistake_track->num_rows > 0)
            {
                $track['mistake_track_status'] = 1;   
            }
            else{
                $track['mistake_track_status'] = 0;
            }
            $all_track[] = $track;
        }

        $msg["status"] = true;
        $msg["message"] = "Success.";
        $msg['data']=$all_track;
    }
    else{
        $msg['message']='No tracks found.';
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