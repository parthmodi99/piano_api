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

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['track_id']) && $_REQUEST['track_id'] != '' && isset($_REQUEST['type']) && $_REQUEST['type'] != '')
{
    if($_REQUEST['type'] == 'favorite')
    {
        $sel = $con->query("SELECT * FROM tbl_favorite WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$_REQUEST['track_id']."'");
        if($sel->num_rows == 0)
        {
            $ins = $con->query("INSERT INTO tbl_favorite(`user_id`,`track_id`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['track_id']."','".$cur_date."')");
            if($ins)
            {
                $msg['message']='Success.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        }
        else{
            $msg['message']='faild to add favorite.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }
    }
    elseif($_REQUEST['type'] == 'unfavorite')
    {
        $sel = $con->query("SELECT * FROM tbl_favorite WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$_REQUEST['track_id']."'");
        if($sel->num_rows == 1)
        {
            $delete = $con->query("DELETE FROM tbl_favorite WHERE user_id='".$_REQUEST['user_id']."' AND track_id='".$_REQUEST['track_id']."'");
            if($delete)
            {
                $msg['message']='Success.';
                $msg['data']=new arrayobject();
                $msg['status']=true;
            }
        }
        else{
            $msg['message']='faild to remove from favorite.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
        }
    }
    else{
        $msg['message']='Invalid type.';
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