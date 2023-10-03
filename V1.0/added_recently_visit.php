<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";

$cur_date = date('Y-m-d H:i:s');

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != '')
{
    $update = $con->query("UPDATE tbl_user SET recently_visit_at='".$cur_date."' WHERE id='".$_REQUEST['user_id']."'");
    if($update)
    {
        $msg['message']='Success.';
        $msg['data']=new arrayobject();
        $msg['status']=true;
    }
}
else{
    $msg['message']=' Invalid request.';
    $msg['data']=new arrayobject();
    $msg['status']=false;
}

echo json_encode($msg);
?>