<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

$cur_date = date('Y-m-d H:i:s');

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";
if(isset($_REQUEST['user_id']) && $_REQUEST['user_id']!='')
{
   $sel = $con->query("SELECT * FROM tbl_playlist WHERE user_id='".$_REQUEST['user_id']."'");
   if($sel->num_rows > 0)
   {
      $list = array();
      while($play = $sel->fetch_assoc())
      {
         //count playlist song
         $count = $con->query("SELECT id FROM tbl_playlist_track WHERE playlist_id='".$play['id']."'");
         $play['total_song'] = $count->num_rows;
         $list[] = $play;
      }
      $msg['message']='Success';
      $msg['data']=$list;
      $msg['status']=true;
   }
   else{
      $msg['message']='No playlist found.';
      $msg['data']=new arrayobject();
      $msg['status']=false;
   }
}
else{
   $msg['message']='Failed.';
   $msg['data']=new arrayobject();
   $msg['status']=false;
}
echo json_encode($msg);
?>