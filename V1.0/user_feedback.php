<?php
include("dbconfig.php");
include("dbfunction.php");
require "../PHPMailer/class.phpmailer.php";

$msg['message']='Failed.';
$msg['data']=new arrayobject();
$msg['status']=false;

$date = date("Y-m-d H:i:s");

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id']!='' && isset($_REQUEST['feedback']) && $_REQUEST['feedback']!='')
{
	$user = $con->query("SELECT * FROM tbl_user WHERE id='".$_REQUEST['user_id']."'");
	if($user->num_rows > 0)
	{
		$user_data = $user->fetch_assoc();
		$ins = $con->query("INSERT INTO tbl_feedback(`user_id`,`feedback`,`created_at`) VALUES('".$_REQUEST['user_id']."','".mysqli_real_escape_string($con,$_REQUEST['feedback'])."','".$date."')");
		if($ins)
		{
			$email='thelevelupapp@gmail.com';
			//$email = 'rajvadi68@gmail.com';
	        $mail = new PHPMailer;
	        $mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = 'tls';
			$mail->Port = 587;
			$mail->Host = 'smtp.gmail.com';
			$mail->Username = 'demo.ebizzinfotech@gmail.com';
			$mail->Password = 'ubhlnertzoilvsps';
			$mail->setFrom('demo.ebizzinfotech@gmail.com', 'Piano Team');
	        $mail->addAddress($email);
	        $mail->isHTML(true);
	        $mail->Subject = "Feedback from ".$user_data['name']."";
	        $mail->Body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
<meta content="telephone=no" name="format-detection" />
<meta content="width=mobile-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=no;" name="viewport" />
<meta content="IE=9; IE=8; IE=7; IE=EDGE;" http-equiv="X-UA-Compatible" />
<title></title>

<style type="text/css">

#outlook a {padding:0;}
body, #body-table {height:100% !important; width:100% !important; margin:0 auto; padding:0; line-height:100%; !important; font-family:Arial, Helvetica, sans-serif; font-size:13px;}
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
p {padding:0; margin: 0; line-height: 24px; font-family: Arial, Helvetica, sans-serif;}
a, a:link {color: #1c344d;text-decoration: none !important;}
.footer-link a, .nav-link a {color: #fff6e5;}

/* Yahoo Mail */
.thread-item.expanded .thread-body{background-color: #edf6ea !important;}
.thread-item.expanded .thread-body .body, .msg-body{display:block !important;}
#body-table .undoreset table {display: table !important;table-layout: fixed !important;}
</style>
<!--[if mso]>
<style>
.font_fix{font-family:Arial, Helvetica, sans-serif !important;}
</style>
<![endif]-->
</head>
<body>

<!-- Start of banner -->


                            

                            

                          
                                            <table width="832" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#fff" style="width: 100%;max-width:832px;height: 177px;    padding-top: 40px;">
                                <tbody>
                                    <tr>
                                        <td align="center" >    
                                        <img src="http://18.235.99.234/APIs/uploads/email_template/levelUpLogo.png" style="width:80px;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                            <table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#ffffff" style="padding:25px 6%; ">
                                            <tbody>
                                            <tr>
                                            <td align="left">   
                                                
                                               
                                                
                                                      <h3 style="font-family: Arial;color:#515151;">Feedback : </h3>
                                                      <h4><b>'.$_REQUEST['feedback'].'</b></h4>

                                                      <h3 style="font-family: Arial;color:#515151;">Email : <b>'.$user_data['email'].'</b></h3>
                                                  
                                                    
                                                 

                                            </td>
                                            </tr>
                                              <tr>
                                           
                                            </tr>
                                            </tbody>
                                            </table>
</body>
</html>';
	       if(!$mail->send()) 
	        {
	           $msg['message']='please try again after sometime';
				$msg['data']=new arrayobject();
				$msg['status']=true;
	        } 
	        else
	        {
	            $msg['message']='Thanks, we will respond to you asap !';
				$msg['data']=new arrayobject();
				$msg['status']=true;
	        }
			
		}
	}
	
}
echo json_encode($msg);
?>