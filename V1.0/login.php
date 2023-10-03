<?php
require_once("dbconfig.php");
include("dbfunction.php");

$msg['status'] = false;
$msg['message'] = "failed";
$msg['data'] = new arrayobject();

//$server_path = "http://".$_SERVER['SERVER_NAME']."/APIs/";
$timezone = isset($_POST['timezone']) && $_POST['timezone'] != '' ? $_POST['timezone'] : "";
if ($timezone != '') {
    date_default_timezone_set($_REQUEST['timezone']);
}
$d = date('Y-m-d H:i:s');

if (isset($_REQUEST['email']) && $_REQUEST['email'] != '' && isset($_REQUEST['password']) && $_REQUEST['password'] != '') {
    if (check_email_exist1($con, $_REQUEST['email'])) {
        $msg['message'] = "Email Not Exist";
    } else if (is_block($con, $_REQUEST['email'])) {
        $msg['message'] = "You are blocked";
    } else {
        $qryLogin = $con->query("SELECT * from tbl_user where email='" . $_REQUEST['email'] . "' AND password='" . md5($_REQUEST['password']) . "'");

        if ($qryLogin->num_rows == 1) {
            $userData = $qryLogin->fetch_assoc();

            $last_login = $userData['last_login'];

            $updateQry = "UPDATE tbl_user set updated_at=NOW(),last_login='" . $d . "'";

            if (isset($_REQUEST['device_type']) && $_REQUEST['device_type'] != '') {
                $updateQry .= ",device_type='" . $_REQUEST['device_type'] . "'";
            }

            if (isset($_REQUEST['device_token']) && $_REQUEST['device_token'] != '') {
                $updateQry .= ",device_token='" . $_REQUEST['device_token'] . "'";
            }

            if (isset($_REQUEST['timezone']) && $_REQUEST['timezone'] != '') {
                $updateQry .= ",timezone='" . $_REQUEST['timezone'] . "'";
            }

            if (isset($_REQUEST['is_react_user']) && $_REQUEST['is_react_user'] != '') {
                $updateQry .= ",is_react_user='" . $_REQUEST['is_react_user'] . "'";
            } else {
                $updateQry .= ",is_react_user='0'";
            }

            $updateQry .= " where id=" . $userData['id'];

            if ($con->query($updateQry)) {
                /*check user is pro or not---------------------*/
                $response = [];
                $pro_status = false;
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

                                        if ($userData['is_pro_user'] == 1) {
                                            $pro_status = true;
                                        }
                                    }
                                }
                            }
                        } else {
                            $pro_status = false;
                            if ($userData['is_pro_user'] == 1) {
                                $pro_status = true;
                            }
                        }
                    }
                }
                /*----------------------------------------------*/
                $user_d = get_user($con, $userData['id']);
                $user_d['pro_status'] = $pro_status;
                if ($last_login == '0000-00-00 00:00:00') {
                    $user_d['is_login_first_time'] = '1';
                } else {
                    $user_d['is_login_first_time'] = '0';
                }

                $msg['status'] = true;
                $msg['data'] = $user_d;
                //$msg['workouts'] = get_user_exercise_byid($con, $userData['id']);
                $msg['message'] = "Login Successfully.";
            }
        } else {
            $msg['message'] = "Wrong password, sorry.";
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