<?php
include("dbconfig.php");
include("dbfunction.php");
require "../PHPMailer/class.phpmailer.php";

$msg['message'] = 'Failed.';
$msg['data'] = new arrayobject();
$msg['status'] = false;

//$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";
$mobile = isset($_POST['mobile']) && $_POST['mobile'] != '' ? $_POST['mobile'] : "";
$gender = isset($_POST['gender']) && $_POST['gender'] != '' ? $_POST['gender'] : "";
$timezone = isset($_POST['timezone']) && $_POST['timezone'] != '' ? $_POST['timezone'] : "";
$pro_status = false;
if ($timezone != '') {
    date_default_timezone_set($_REQUEST['timezone']);
}
$cur_date = date('Y-m-d H:i:s');

if (isset($_REQUEST['email']) && $_REQUEST['email'] != '' && isset($_REQUEST['social_key']) && $_REQUEST['social_key'] != '') {
    if (check_email($_REQUEST['email'], $con)) {
        $qryLogin = $con->query("select * from tbl_user where email='" . $_REQUEST['email'] . "'");

        if ($qryLogin->num_rows == 1) {
            $userData = $qryLogin->fetch_assoc();

            $last_login = $userData['last_login'];

            if ($userData['login_type'] == 0) {
                if ($userData['is_block'] == 0) {
                    $updateUser = $con->query("update tbl_user set social_key='" . $_REQUEST['social_key'] . "',device_type='" . $_REQUEST['device_type'] . "',device_token='" . $_REQUEST['device_token'] . "',last_login=NOW() where id=" . $userData['id']);

                    $updateQry = "update tbl_user set updated_at=NOW(),last_login='" . $cur_date . "'";

                    if ($userData['profile_pic'] == '') {
                        if (isset($_REQUEST['profile_pic']) && $_REQUEST['profile_pic'] != '') {
                            $updateQry .= ",profile_pic='" . $_REQUEST['profile_pic'] . "'";
                        }
                    }

                    if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
                        $updateQry .= ",name='" . $_REQUEST['name'] . "'";
                    }

                    if (isset($_REQUEST['is_react_user']) && $_REQUEST['is_react_user'] != '') {
                        $updateQry .= ",is_react_user='" . $_REQUEST['is_react_user'] . "'";
                    } else {
                        $updateQry .= ",is_react_user='0'";
                    }

                    if (isset($_REQUEST['timezone']) && $_REQUEST['timezone'] != '') {
                        $updateQry .= ",timezone='" . $_REQUEST['timezone'] . "'";
                    }


                    $updateQry .= ' where id=' . $userData['id'];

                    $con->query($updateQry);
                    /*check user is pro or not---------------------*/
                    $response = [];

                    if (isset($_REQUEST['receipt-data']) && $_REQUEST['receipt-data'] != '') {
                        $data_array = [
                            "password" => $_REQUEST['password'],
                            "receipt-data" => $_REQUEST['receipt-data']
                        ];

                        if ($_REQUEST['isSandbox'] == 'Yes') {
                            $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
                        } else {
                            $url = 'https://buy.itunes.apple.com/verifyReceipt';
                        }

                        $make_call = callAPI('POST', $url, json_encode($data_array));
                        $response = json_decode($make_call, true);
                    }

                    if (!empty($response)) {

                        $expirationDateMs = max(array_column($response['latest_receipt_info'], 'expires_date_ms'));
                        $requestDateMs = $response['receipt']['request_date_ms'];

                        $status = $response['status'];
                        if ($status == 0 && $expirationDateMs > $requestDateMs) {
                            $pro_status = true;
                        } else {
                            /*$select_user = $con->query("SELECT * FROM tbl_friend WHERE (user_id = '".$_REQUEST['user_id']."' OR other_user_id = '".$_REQUEST['user_id']."') ORDER BY id ASC LIMIT 5");*/
                            $select_user = $con->query("SELECT * FROM tbl_request WHERE (from_user_id = '" . $userData['id'] . "' OR to_user_id = '" . $userData['id'] . "') AND status IN(1,3) ORDER BY id ASC LIMIT 5");
                            if ($select_user->num_rows >= 5) {
                                $i = 0;
                                while ($d = $select_user->fetch_assoc()) {
                                    $i++;
                                    if ($i == 5) {
                                        $curr_date = date('Y-m-d H:i:s');

                                        $date = $d['updated_at'];
                                        $expire_date = date('Y-m-d H:i:s', strtotime("+6 months", strtotime($date)));
                                        if (strtotime($expire_date) > strtotime($curr_date)) {
                                            $pro_status = true;
                                        } else {
                                            $upd = $con->query("UPDATE tbl_user SET pro_by_invite=1 WHERE id='" . $userData['id'] . "'");
                                            $pro_status = false;
                                        }

                                    }
                                }
                            } else {
                                $pro_status = false;
                            }
                        }
                    }

                    /*----------------------------------------------*/
                    $user_d = get_user($con, $userData['id']);
                    if ($last_login == '0000-00-00 00:00:00') {
                        $user_d['is_login_first_time'] = '1';
                    } else {
                        $user_d['is_login_first_time'] = '0';
                    }
                    $user_d['pro_status'] = $pro_status;
                    $msg['status'] = true;
                    $msg['message'] = 'login Successfully';
                    $msg['data'] = $user_d;
                    //$msg['workouts'] = get_user_exercise_byid($con, $userData['id']);
                    echo json_encode($msg);
                    exit;
                } else {
                    $msg['message'] = 'This user has had their account suspended. If you are unsure why this has happened, please contact Gymtimer.';
                    echo json_encode($msg);
                    exit;
                }
            } else {

                if ($userData['is_block'] == 0) {

                    $updateUser = $con->query("update tbl_user set social_key='" . $_REQUEST['social_key'] . "',device_type='" . $_REQUEST['device_type'] . "',device_token='" . $_REQUEST['device_token'] . "',last_login='" . $cur_date . "' where id=" . $userData['id']);

                    $updateQry = "update tbl_user set updated_at=NOW()";

                    if ($userData['profile_pic'] == '') {
                        if (isset($_REQUEST['profile_pic']) && $_REQUEST['profile_pic'] != '') {
                            $updateQry .= ",profile_pic='" . $_REQUEST['profile_pic'] . "'";
                        }
                    }

                    if (isset($_REQUEST['name']) && $_REQUEST['name'] != '') {
                        $updateQry .= ",name='" . $_REQUEST['name'] . "'";
                    }

                    if (isset($_REQUEST['is_react_user']) && $_REQUEST['is_react_user'] != '') {
                        $updateQry .= ",is_react_user='" . $_REQUEST['is_react_user'] . "'";
                    } else {
                        $updateQry .= ",is_react_user='0'";
                    }


                    $updateQry .= ' where id=' . $userData['id'];

                    $con->query($updateQry);

                    /*check user is pro or not---------------------*/
                    $response = [];

                    if (isset($_REQUEST['receipt-data']) && $_REQUEST['receipt-data'] != '') {
                        $data_array = [
                            "password" => $_REQUEST['password'],
                            "receipt-data" => $_REQUEST['receipt-data']
                        ];

                        if ($_REQUEST['isSandbox'] == 'Yes') {
                            $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
                        } else {
                            $url = 'https://buy.itunes.apple.com/verifyReceipt';
                        }

                        $make_call = callAPI('POST', $url, json_encode($data_array));
                        $response = json_decode($make_call, true);
                    }

                    if (!empty($response)) {

                        $expirationDateMs = max(array_column($response['latest_receipt_info'], 'expires_date_ms'));
                        $requestDateMs = $response['receipt']['request_date_ms'];

                        $status = $response['status'];
                        if ($status == 0 && $expirationDateMs > $requestDateMs) {
                            $pro_status = true;
                        } else {
                            /*$select_user = $con->query("SELECT * FROM tbl_friend WHERE (user_id = '".$_REQUEST['user_id']."' OR other_user_id = '".$_REQUEST['user_id']."') ORDER BY id ASC LIMIT 5");*/
                            $select_user = $con->query("SELECT * FROM tbl_request WHERE (from_user_id = '" . $userData['id'] . "' OR to_user_id = '" . $userData['id'] . "') AND status IN(1,3) ORDER BY id ASC LIMIT 5");
                            if ($select_user->num_rows >= 5) {
                                $i = 0;
                                while ($d = $select_user->fetch_assoc()) {
                                    $i++;
                                    if ($i == 5) {
                                        $curr_date = date('Y-m-d H:i:s');

                                        $date = $d['updated_at'];
                                        $expire_date = date('Y-m-d H:i:s', strtotime("+6 months", strtotime($date)));
                                        if (strtotime($expire_date) > strtotime($curr_date)) {
                                            $pro_status = true;
                                        } else {
                                            $upd = $con->query("UPDATE tbl_user SET pro_by_invite=1 WHERE id='" . $userData['id'] . "'");
                                            $pro_status = false;
                                        }

                                    }
                                }
                            } else {
                                $pro_status = false;
                            }
                        }
                    }

                    /*----------------------------------------------*/

                    $user_d = get_user($con, $userData['id']);
                    if ($last_login == '0000-00-00 00:00:00') {
                        $user_d['is_login_first_time'] = '1';
                    } else {
                        $user_d['is_login_first_time'] = '0';
                    }
                    $user_d['pro_status'] = $pro_status;
                    $msg['status'] = true;
                    $msg['message'] = 'login Successfully';
                    $msg['data'] = $user_d;
                    //$msg['workouts'] = get_user_exercise_byid($con, $userData['id']);
                } else {
                    $msg['message'] = 'This user has had their account suspended. If you are unsure why this has happened, please contact Gymtimer.';
                }
            }

        }
    } else {
        $name = $_REQUEST['name'] != '' ? $_REQUEST['name'] : '';
        $profile_pic = isset($_POST['profile_pic'])&&$_POST['profile_pic']!=''?$_POST['profile_pic']:"";
        $device_type = $_REQUEST['device_type'] != '' ? $_REQUEST['device_type'] : '';
        $device_token = $_REQUEST['device_token'] != '' ? $_REQUEST['device_token'] : '';
        $timezone = isset($_POST['timezone'])&&$_POST['timezone']!=''?$_POST['timezone']:"";
        $is_react_user = isset($_POST['is_react_user'])&&$_POST['is_react_user']!=''?$_POST['is_react_user']:"0";

        /*-------------------------*/
        $chars = "0123456789";
        $res = "";
        for ($i = 0; $i < 6; $i++) {
            $res .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        if ($res != '') {
            for ($j = 0; $j < 100; $j++) {
                $sel = $con->query("SELECT id FROM tbl_user WHERE referral_code='" . $res . "'");
                if ($sel->num_rows == 0) {
                    break;
                } else {
                    $res = "";
                    for ($i = 0; $i < 6; $i++) {
                        $res .= $chars[mt_rand(0, strlen($chars) - 1)];
                    }
                }
            }
        }
        /*-------------------------*/

        $insert = $con->query("INSERT into tbl_user(name,email,social_key,profile_pic,login_type,device_type,device_token,timezone,referral_code,is_react_user,created_at,updated_at,last_login) values('" . $name . "','" . $_REQUEST['email'] . "','" . $_REQUEST['social_key'] . "','" . $profile_pic . "','1','" . $device_type . "','" . $device_token . "','" . $timezone . "','" . $res . "','" . $is_react_user . "','" . $cur_date . "','0000-00-00 00:00:00','" . $cur_date . "')");

        if ($insert) {
            $user_id = $con->insert_id;

            if (isset($_REQUEST['referral_code']) && $_REQUEST['referral_code'] != '') {
                $get = $con->query("SELECT * FROM tbl_user WHERE referral_code='" . $_REQUEST['referral_code'] . "'");
                $get_r = $get->fetch_assoc();
                if ($get_r['id'] != $user_id) {
                    $ssel = $con->query("SELECT id FROM tbl_referral WHERE user_id='" . $user_id . "' AND other_user_id='" . $get_r['id'] . "'");
                    if ($ssel->num_rows == 0) {
                        $insert = $con->query("INSERT INTO tbl_referral(`user_id`,`other_user_id`,`created_at`) VALUES('" . $user_id . "','" . $get_r['id'] . "',NOW())");
                        if ($insert) {
                            /*------------------send push notification------------*/
                            $from_user_data = get_user_byid($con, $user_id);
                            $to_user_data = get_user_byid($con, $get_r['id']);
                            $title = $from_user_data['name'] . ' ' . 'create new account with your referral code !';
                            $device_token = $to_user_data['device_token'];
                            $device_type = $to_user_data['device_type'];
                            $type = 5;
                            if ($device_token != '') {
                                $send_noti = sendnotification($con, $title, $device_type, $device_token, $user_id, $get_r['id'], $type, 'referral_code', $to_user_data['is_react_user']);
                            }
                            /*----------------------------------------------------*/
                        }
                    }
                }
            }

            /*-------Send Welcome Mail-------------*/
            $user = get_data_from_email($_POST['email'], $con);
            if ($user) {

                $email = $_POST['email'];
                $username = $user['name'];

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
            <h2 style="color: #1f1f1f; font-size:28px; font-weight: bold;line-height:32px;margin-top: 0;">Hello ' . $username . ', </h2><br>


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

            /*-------------------------------------*/

            $user_d = get_user($con, $user_id);
            $user_d['is_login_first_time'] = '1';
            $user_d['pro_status'] = false;

            $msg['status'] = true;

            $msg['message'] = "Login Successfully";

            $msg['data'] = $user_d; //get_user($con,$user_id);
            //$msg['workouts'] = get_user_exercise_byid($con, $user_id);
        }


    }
}

function callAPI($method, $url, $data)
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    // EXECUTE:
    $result = curl_exec($curl);
    if (!$result) {
        die("Connection Failure");
    }
    curl_close($curl);
    return $result;
}

echo json_encode($msg);
?>