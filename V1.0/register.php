<?php
include("dbconfig.php");
include("dbfunction.php");
require "../PHPMailer/class.phpmailer.php";

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";
$mobile=isset($_POST['mobile'])&&$_POST['mobile']!=''?$_POST['mobile']:"";
$gender=isset($_POST['gender'])&&$_POST['gender']!=''?$_POST['gender']:"";
$timezone = isset($_POST['timezone'])&&$_POST['timezone']!=''?$_POST['timezone']:"";
if($timezone!='')
{
  date_default_timezone_set($_REQUEST['timezone']);
}
$cur_date = date('Y-m-d H:i:s');

if(isset($_REQUEST['email']) && $_REQUEST['email']!='')
{
	if(check_email($_REQUEST['email'],$con))
	{
		$msg['message']="This email already exists Try to Log In.";
	}
	else
	{
		if(isset($_REQUEST['name']) && $_REQUEST['name']!='' && isset($_REQUEST['password']) && $_REQUEST['password']!='')
		{
          $is_react_user = isset($_POST['is_react_user'])&&$_POST['is_react_user']!=''?$_POST['is_react_user']:"0";
         /*-------------------------------*/
         $chars = "0123456789";
         $res = "";
         for ($i = 0;$i < 6;$i++)
         {
             $res .= $chars[mt_rand(0, strlen($chars) - 1) ];
         }

         if ($res != '')
         {
             for ($j = 0;$j < 100;$j++)
             {
                 $sel = $con->query("SELECT id FROM tbl_user WHERE referral_code='".$res."'");
                 if ($sel->num_rows == 0)
                 {
                     break;
                 }
                 else
                 {
                     $res = "";
                     for ($i = 0;$i < 6;$i++)
                     {
                         $res .= $chars[mt_rand(0, strlen($chars) - 1) ];
                     }
                 }
             }
         }
         /*-------------------------------*/
			$insert=$con->query("INSERT into tbl_user(name,email,password,device_type, device_token,mobile,gender,timezone,referral_code,is_react_user,created_at,updated_at,last_login) values ('".$_REQUEST['name']."','".$_REQUEST['email']."','".md5($_REQUEST['password'])."','".$_REQUEST['device_type']."','".$_REQUEST['device_token']."','".$mobile."','".$gender."','".$timezone."','".$res."','".$is_react_user."','".$cur_date."','0000-00-00 00:00:00','".$cur_date."')");
			if($insert)
			{
			  $user_id=$con->insert_id;
           /*--------------------*/
           if(isset($_REQUEST['referral_code']) && $_REQUEST['referral_code']!='')
           {
               $get = $con->query("SELECT * FROM tbl_user WHERE referral_code='".$_REQUEST['referral_code']."'");
               $get_r = $get->fetch_assoc();
               if($get_r['id']!=$user_id)
               {
                  $ssel = $con->query("SELECT id FROM tbl_referral WHERE user_id='".$user_id."' AND other_user_id='".$get_r['id']."'");
                  if($ssel->num_rows == 0)
                  {
                     $insert = $con->query("INSERT INTO tbl_referral(`user_id`,`other_user_id`,`created_at`) VALUES('".$user_id."','".$get_r['id']."',NOW())");
                     if($insert)
                     {
                        /*------------------send push notification------------*/
                        $from_user_data = get_user_byid($con,$user_id);
                        $to_user_data = get_user_byid($con,$get_r['id']);
                        $title = $from_user_data['name'].' '.'create new account with your referral code !';
                        $device_token = $to_user_data['device_token'];
                        $device_type = $to_user_data['device_type'];
                        $type = 5;
                        if($device_token!='')
                        {
                           $send_noti = sendnotification($con,$title,$device_type,$device_token,$user_id,$get_r['id'],$type,'referral_code',$to_user_data['is_react_user']);
                        }
                        /*----------------------------------------------------*/
                     }
                  }
               }
           }
           /*--------------------*/

		   $user=get_data_from_email($_POST['email'],$con);
           if($user)
           {

            $email=$_POST['email'];
            $username=$user['name'];
            
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'open';
            $mail->Port = 587;
            $mail->Host = 'sm32.siteground.biz';
            $mail->Username = 'noreply@ebizzdevelopment.com';
            $mail->Password = '8l)^n@cW67,T';
            $mail->setFrom('noreply@ebizzdevelopment.com', 'GymTimer Team');
            $mail->addAddress($email, $username);
            $mail->isHTML(true);
            $mail->Subject = 'Welcome to GymTimer !';
            $mail->Body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns:v="urn:schemas-microsoft-com:vml">
            <head>
            <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
            <meta content="telephone=no" name="format-detection">
            <meta content="width=mobile-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;" name="viewport">
            <meta content="IE=9; IE=8; IE=7; IE=EDGE;" http-equiv="X-UA-Compatible">
            <title></title>

            <link href="https://fonts.googleapis.com/css?family=Nunito:800,800i&amp;subset=latin-ext,vietnamese" rel="stylesheet">

            <style type="text/css">

            </style>

            <style type="text/css">

             #outlook a {padding:0;}
            body, #body-table {height:100% !important; width:100% !important; margin:0 auto; padding:0; line-height:100%; !important; font-family: Nunito, sans-serif; font-size:13px;}
            
            /* Client Specific Resets */
            .ReadMsgBody {width:100%;} .ExternalClass{width:100%;}
            .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100% !important;}
            .ExternalClass * {line-height: 100% !important;}
            table, td {mso-table-lspace:0pt; mso-table-rspace:0pt;}
            img {outline: none; border: none; text-decoration: none; -ms-interpolation-mode: bicubic;}
            body, table, td, p, a, li, blockquote {-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%;}
            body.outlook img {width: auto !important;max-width: none !important;}

            /* Start Template Styles */
            /* Main */
            body{ -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0; -webkit-font-smoothing: antialiased;}
            body, #body-table { margin:0 auto !important;; margin:0 auto !important; text-align:center !important;}
            p {padding:0; margin: 0; line-height: 24px; font-family: Nunito, sans-serif;}
            
            a, a:link {color: #1c344d;text-decoration: none !important;}
            .footer-link a, .nav-link a {color: #fff6e5;}

            /* Yahoo Mail */
            .thread-item.expanded .thread-body{background-color: #edf6ea !important;}
            .thread-item.expanded .thread-body .body, .msg-body{display:block !important;}
             #body-table .undoreset table {display: table !important;table-layout: fixed !important;}
            </style>
            <!--[if mso]>
            
            
            <![endif]-->
            </head>
            <body>

            <!-- Start of banner -->

            
            <!-- Start blocchi ======================= -->
            
            

            

            <table width="832" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#ffffff" style="width: 100%;max-width:832px;margin-top:10px;border:0px solid #dddddd;">
            <tbody>
            <tr>
            
            <td align="center"> 
            <table width="832" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#fff" style="width: 100%;max-width:832px;height: 90px;">
            <tbody>
            <!--  <tr></tr><img src="http://ec2-52-207-213-59.compute-1.amazonaws.com/APIs/uploads/headerimg_2.jpg" style="width: 100%;"> -->
            <tr>
            <td align="center" style="margin-bottom: 47px;">    
            <img src="http://ec2-52-207-213-59.compute-1.amazonaws.com/APIs/uploads/new.png" style="width:80px;">
            </td>
            </tr>
            </tbody>
            </table>
            <table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#ffffff" style="margin-top: 30px;padding:25px 6%; ">
            <tbody>
            <tr>
            <td align="left"> 
            <br>  
            <h2 style="color: #1f1f1f; font-size:28px; font-weight: bold;line-height:32px;margin-top: 0;">Hello '.$username.', </h2><br>
            
            
            <h4 style="font-family: Arial;color: #888888;font-size: 14px;font-weight: 500;line-height: 18px;margin: 0px;">Welcome to GymTimer, you are now the smartest person in your gym !</h4><br>
            
            
            
            
            <h4 style="font-family: Arial;color: #888888;font-size: 14px;font-weight: 500;line-height: 18px;margin: 0px;">
            If you have any questions or feedback, please feel free to contact us any time at gymtimerapp@gmail.com. 
            </h4><br>

            </td>
            </tr>
            <tr>
            <td align="left">   
            
            <h4 style="font-family: Arial;color: #888888;font-size: 14px;font-weight: 500;line-height: 18px;margin: 0px;">Train hard and see you soon !</h4>
            
            <h4 style="font-family: Arial;color: #888888;font-size: 14px;font-weight: 500;line-height: 18px;margin: 0px;">The Gymtimer Team.</h4>
            

            </td>
            </tr>
            </tbody>
            </table>
            <!--   <table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#ffffff" style="padding:25px 6%;border-top: 1px solid #dddddd; ">
            <tbody>

            </tbody>
            </table>
            -->
            </td>
            </tr>
            </tbody>
            </table>  
            </body>
            </html>';
            /*if(!$mail->send()) 
            {
             
            } 
            else
            {
             
            }*/

          }

       
          $user_d = get_user($con,$user_id);       
          $user_d['is_login_first_time'] = '1';
        
       $msg['data']=$user_d;
       $msg['message']='Registered successfully.';
       $msg['status']=true;
     }
   }
 }
}
echo json_encode($msg);
?>