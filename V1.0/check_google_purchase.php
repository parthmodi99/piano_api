<?php 
include("dbconfig.php");
include("dbfunction.php");
require_once '../vendor/autoload.php';

if(isset($_REQUEST['is_react_user']) && $_REQUEST['is_react_user']!='')
{
    $upd = $con->query("UPDATE tbl_user SET is_react_user='".$_REQUEST['is_react_user']."' WHERE id='".$_REQUEST['user_id']."'");
}

/*check if user is pro by user*/
$sel = $con->query("SELECT * FROM tbl_user WHERE id='".$_REQUEST['user_id']."'");
if($sel->num_rows > 0)
{
	$ud = $sel->fetch_assoc();
	if($ud['timezone']!='')
    {
      date_default_timezone_set($ud['timezone']);
    }
    $cur_date = date('Y-m-d H:i:s');
	if($ud['is_pro_user'] == 1 && $ud['is_pro_by_admin'] == 1)
	{
		$msg['status'] = true;
		$msg['msg'] = 'success';
		$msg['pro_status'] = true;
		$msg['data'] = new arrayobject();
		echo json_encode($msg);	
		exit();
	}
}

$packageName = $_REQUEST['package_name'];
$productId = $_REQUEST['product_id'];
$token = $_REQUEST['purchase_token'];

$client = new \Google_Client();
$client->setAuthConfig('../piano.json');
$client->addScope('https://www.googleapis.com/auth/androidpublisher');
$service = new \Google_Service_AndroidPublisher($client);
try{
    $purchase = $service->purchases_subscriptions->get($packageName, $productId, $token);

    if(isset($purchase->startTimeMillis) && $purchase->startTimeMillis!='')
    {
        $start_date = $purchase->startTimeMillis / 1000;
        $end_date = $purchase->expiryTimeMillis / 1000;

        $purchase_date = date("Y-m-d H:i:s", $start_date);
        $expiry_date = date("Y-m-d H:i:s", $end_date);
        $cur_date = date('Y-m-d H:i:s');

        /*echo $purchase_date.PHP_EOL;
        echo $cur_date.PHP_EOL;
        echo $expiry_date; die;*/
        $is_cancel = $purchase->cancelReason;
        if(strtotime(date('Y-m-d H:i:s')) <= strtotime($expiry_date) && trim($is_cancel) == '')
        {
            $msg['status'] = true;
            $msg['msg'] = 'success';
            $msg['pro_status'] = true;
            $msg['data'] = new arrayobject();
            echo json_encode($msg);
            exit();
        }
        else{
            $msg['status'] = true;
            $msg['msg'] = 'success';
            $msg['pro_status'] = false;
            $msg['data'] = new arrayobject();
            echo json_encode($msg);
            exit();
        }
    }
    else{
        $msg['status'] = true;
        $msg['msg'] = 'success';
        $msg['pro_status'] = false;
        $msg['data'] = new arrayobject();
        echo json_encode($msg);
        exit();
    }
}
catch (Exception $e)
{
    $msg['status'] = true;
    $msg['msg'] = 'success';
    $msg['pro_status'] = false;
    $msg['data'] = new arrayobject();
    echo json_encode($msg);
    exit();
}
echo json_encode($msg);	