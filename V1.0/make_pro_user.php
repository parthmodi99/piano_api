<?php
include("dbconfig.php");
include("dbfunction.php");

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";
if(isset($_REQUEST['user_id']) && $_REQUEST['user_id']!='')
{
   if(isset($_REQUEST['value']) && $_REQUEST['value']!='')
   {
      $value = $_REQUEST['value'];
   }
   else
   {
      $value = 1;
   }
   if(isset($_REQUEST['is_new_subscription']) && $_REQUEST['is_new_subscription']!='')
   {
      $is_new_subscription = $_REQUEST['is_new_subscription'];
   }
   else
   {
      $is_new_subscription = 0;
   }
   
   /*check if user is pro by user*/
   $sel = $con->query("SELECT * FROM tbl_user WHERE id='".$_REQUEST['user_id']."'");
   if($sel->num_rows > 0)
   {
      $ud = $sel->fetch_assoc();
      if($ud['is_pro_user'] == 1 && $ud['is_pro_by_admin'] == 1)
      {
         $value = 1;
      }
      // if($ud['timezone']!='')
      // {
      //    date_default_timezone_set($ud['timezone']);
      // }
   }
   if($is_new_subscription == '1' && $value == '1')
   {
      $date = date('Y-m-d H:i:s');
      $upd = $con->query("UPDATE tbl_user SET is_pro_user='".$value."', pro_at='".$date."' WHERE id='".$_REQUEST['user_id']."'");
   }
   else
   {
      $date = '';
      $upd = $con->query("UPDATE tbl_user SET is_pro_user='".$value."' WHERE id='".$_REQUEST['user_id']."'");
   }
   
   if($upd)
   {
      $msg['message']='success';
      $msg['data']=new arrayobject();
      $msg['pro_status'] = $value == 1?true:false;
      $msg['status']=true;
   }
   else
   {
      $msg['message']='Failed.';
      $msg['data']=new arrayobject();
      $msg['pro_status'] = false;
      $msg['status']=false;
   }
}
echo json_encode($msg);
?>