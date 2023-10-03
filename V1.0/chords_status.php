<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '' && isset($_REQUEST['status']) && $_REQUEST['status'] != '')
{
    
    $sel = $con->query("SELECT * FROM tbl_chords_status WHERE user_id='".$_REQUEST['user_id']."'");
    if($sel->num_rows == 0)
    {
        $ins = $con->query("INSERT INTO tbl_chords_status(`user_id`,`status`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['status']."','".$cur_date."')");
        if($ins)
        {
            $msg['message']='Success.';
            $msg['data']=new arrayobject();
            $msg['status']=true;
        }
    }
    else{
        $upd = $con->query("UPDATE tbl_chords_status SET status='".$_REQUEST['status']."',updated_at='".$cur_date."' WHERE user_id='".$_REQUEST['user_id']."'");
        if($upd)
        {
            $msg['message']='Success.';
            $msg['data']=new arrayobject();
            $msg['status']=true;
        }
    }
    
}
else{
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
?>