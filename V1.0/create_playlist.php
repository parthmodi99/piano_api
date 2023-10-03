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
   if(isset($_REQUEST['name']) && $_REQUEST['name']!=''){

      $check = $con->query("SELECT * FROM tbl_playlist WHERE user_id='".$_REQUEST['user_id']."' AND LOWER(name)='".mysqli_real_escape_string($con,strtolower(trim($_REQUEST['name'])))."'");
      if($check->num_rows == 0)
      {
         $ins = $con->query("INSERT INTO tbl_playlist(`user_id`,`name`,`created_at`) VALUES('".$_REQUEST['user_id']."','".mysqli_real_escape_string($con,trim($_REQUEST['name']))."','".$cur_date."')");
         if($ins)
         {
            $playlist_id = $con->insert_id;

            $ins_sorting = $con->query("INSERT INTO tbl_playlist_sorting(`user_id`,`playlist_id`,`sorting`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$playlist_id."','1','".$cur_date."')");

            if(isset($_REQUEST['track_id']) && $_REQUEST['track_id']!=''){
               //insert track into playlist
               $insert_playlist = $con->query("INSERT INTO tbl_playlist_track(`user_id`,`playlist_id`,`track_id`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$playlist_id."','".$_REQUEST['track_id']."','".$cur_date."') ");
               if($insert_playlist)
               {
                  $msg['message']='Saved successfully.';
                  $msg['data']=new arrayobject();
                  $msg['status']=true;
               }  
               else{
                  $msg['message']='Failed.';
                  $msg['data']=new arrayobject();
                  $msg['status']=false;
               } 
            }
            else{
                  $msg['message']='Saved successfully.';
                  $msg['data']=new arrayobject();
                  $msg['status']=true;
            }
         }
         else{
            $msg['message']='Failed.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
         }
      }
      else{
         $msg['message']='Playlist already exist.';
         $msg['data']=new arrayobject();
         $msg['status']=false;
      }
   }elseif (isset($_REQUEST['playlist_id']) && $_REQUEST['playlist_id']!='') {
      $check = $con->query("SELECT * FROM tbl_playlist WHERE user_id='".$_REQUEST['user_id']."' AND id='".$_REQUEST['playlist_id']."'");
      if($check->num_rows == 1)
      {
         $check_exist = $con->query("SELECT * FROM tbl_playlist_track WHERE user_id='".$_REQUEST['user_id']."' AND playlist_id='".$_REQUEST['playlist_id']."' AND track_id='".$_REQUEST['track_id']."'");
         if($check_exist->num_rows == 0)
         {
            //insert track into playlist
            $insert_playlist = $con->query("INSERT INTO tbl_playlist_track(`user_id`,`playlist_id`,`track_id`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['playlist_id']."','".$_REQUEST['track_id']."','".$cur_date."') ");
            if($insert_playlist)
            {
               $msg['message']='Saved successfully.';
               $msg['data']=new arrayobject();
               $msg['status']=true;
            }  
            else{
               $msg['message']='Failed.';
               $msg['data']=new arrayobject();
               $msg['status']=false;
            } 
         }
         else{
            $msg['message']='Track already exist in playlist.';
            $msg['data']=new arrayobject();
            $msg['status']=false;
         }
      }
      else{
         $msg['message']='Playlist not exist.';
         $msg['data']=new arrayobject();
         $msg['status']=false;
      }
   }
   else{
      $msg['message']='Failed.';
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