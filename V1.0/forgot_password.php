<?php
include("dbconfig.php");
include("dbfunction.php");
require "../PHPMailer/class.phpmailer.php";

$msg['status'] = false;

$msg['message'] = "failed";

$msg['data'] = new arrayobject();

$pass = RandomString();

if (isset($_POST['email']) && $_POST['email'] != '') {
    if (check_email($_POST['email'], $con)) {
        if (is_block($con, $_REQUEST['email'])) {
            $msg['message'] = "You are blocked";
        } else {
            $user = get_data_from_email($_POST['email'], $con);
            if ($user) {
                $username = $user['name'];
                $user_id = $user['id'];
                $qry_update = $con->query("UPDATE `tbl_user` set password='" . md5($pass) . "' where id=" . $user_id);
                if ($qry_update) {

                    $email = $_POST['email'];
                    $mail = new PHPMailer;
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Username = 'demo.ebizzinfotech@gmail.com';
                    $mail->Password = 'ubhlnertzoilvsps';
                    $mail->setFrom('demo.ebizzinfotech@gmail.com', 'Piano Team');
                    $mail->addAddress($email, $username);
                    $mail->isHTML(true);
                    $mail->Subject = 'Your new password.';
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
                            <table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#ffffff" style="    margin-top: 30px;padding:25px 6%; ">
                                <tbody>
                                    <tr>
                                        <td align="left">   
                                            <h4 style="color:#515151;">Here is your temporary password for Piano</h4>
                                            <div style="background: #2d2d2d;border: 1px solid #676767;border-radius: 4px;clear: both;color: #fff!important;display: inline-block;font-size: 16px;font-weight: normal;line-height: 19px;padding: 8px 12px;text-align: center; text-decoration:none !important;">' . $pass . '</div>
                                                
                                            </br></br><div><h4 style="color: #525252;">You will be able to change this password in the app settings.</h4></div>
                                            </h4>
                                            
                                            <h4 style="color: #5c5c5c;font-size: 14px;font-weight: 500;line-height: 18px;margin: 0px;">
                                            If you have any questions or feedback, please feel free to contact us any time at pianoapp@gmail.com. 
                                            </h4><br>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">   
                                            
                                            <h4 style="color: #5a5a5a;font-size: 14px;font-weight: 500;line-height: 18px;margin: 0px;">See you soon,</h4>
                                            
                                            <h4 style="color: #5a5a5a;font-size: 14px;font-weight: 500;line-height: 18px;margin: 0px;">The Piano Team.</h4>
                                            

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </body>
                    </html>';

                    /*
                    <table width="832" align="center" cellspacing="0" cellpadding="0" border="0" class="mobile-width" bgcolor="#fff" style="width: 100%;max-width:832px;height: 150px;    padding-top: 40px;">
                        <tbody>
                            <tr>
                                <td align="center" >
                                <img src="http://ec2-52-207-213-59.compute-1.amazonaws.com/APIs/uploads/new.png" style="width:80px;">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                        <h2 style="color: #1f1f1f; font-size:28px; font-weight: bold;line-height:32px;margin-top: 0;">Hello '.$username.', </h2>
                    */
                    if (!$mail->send()) {
                        $msg['status'] = false;
                        $msg['message'] = "Email not sent.";
                    } else {
                        $msg['status'] = true;
                        $msg['message'] = "Your new password is in your mailbox.";
                    }
                }
            }
        }
    } else {
        $msg['status'] = false;
        $msg['message'] = "You are not registered to this email.";
    }

}
echo json_encode($msg);
?>