<?php

include("dbconfig.php");
include("dbfunction.php");

$msg['message'] = 'Failed.';
$msg['data'] = new arrayobject();
$msg['status'] = false;
$cur_date = date('Y-m-d H:i:s');
if (isset($_POST['user_id']) && $_POST['user_id'] != '') {
    $flag = 0;
    $updateQry = "update tbl_user set updated_at=NOW()";

    if (isset($_REQUEST['name'])) {
        $updateQry .= ",name='" . ucfirst($_REQUEST['name']) . "'";
    }

    if (isset($_REQUEST['email'])) {
        if (check_email_exist_with_userid($_REQUEST['email'], $con, $_POST['user_id'])) {
            $flag = 1;
        }
        $updateQry .= ",name='" . ucfirst($_REQUEST['name']) . "'";
    }

    if (isset($_REQUEST['password']) && $_REQUEST['password'] != '') {
        $updateQry .= ",password='" . md5($_REQUEST['password']) . "'";
    }

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['name'] != '') {
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = "../uploads/profile_pic/" . md5(date('Y-m-d H:i:s')) . '.' . $ext;
        $fname = "uploads/profile_pic/" . md5(date('Y-m-d H:i:s')) . '.' . $ext;
        $tmp_name = $_FILES['profile_pic']['tmp_name'];
        if (move_uploaded_file($tmp_name, $filename)) {
            $updateQry .= ",profile_pic='" . $fname . "'";
        } else {
            $msg['message'] = 'failed to upload image.';
            $msg['data'] = new arrayobject();
            $msg['status'] = false;
            echo json_encode($msg);
            exit();
        }
    }

    $updateQry .= " where id=" . $_REQUEST['user_id'];

    //echo $updateQry;die;
    if ($flag == 1) {
        $msg['message'] = 'Email Already Exist.';
        $msg['data'] = new arrayobject();
        $msg['status'] = false;
    } else {
        if ($con->query($updateQry)) {
            $msg['status'] = true;
            $msg['message'] = "Your changes were updated successfully";
            $msg['data'] = get_user($con, $_REQUEST['user_id']);
        }
    }
}

echo json_encode($msg);
?>