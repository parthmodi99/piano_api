<?php 
include("dbconfig.php");
include("dbfunction.php");
$data_array =  array(
      "password"        => $_REQUEST['password'],
      "receipt-data"        => $_REQUEST['receipt-data']   
);
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

/*-----------------------------------------------------*/

if($_REQUEST['isSandbox'] == 'Yes')
{
	$url = 'https://sandbox.itunes.apple.com/verifyReceipt';
}	
else
{
	$url = 'https://buy.itunes.apple.com/verifyReceipt';
}

$make_call = callAPI('POST', $url, json_encode($data_array));
$response = json_decode($make_call, true);

$sel = $con->query("SELECT * FROM tbl_receipt_data WHERE user_id='".$_REQUEST['user_id']."' AND receipt_data='".$_REQUEST['receipt-data']."'");
if($sel->num_rows == 0)
{
	$insert = $con->query("INSERT INTO tbl_receipt_data(`user_id`,`receipt_data`,`password`,`is_sandbox`,`created_at`) VALUES('".$_REQUEST['user_id']."','".$_REQUEST['receipt-data']."','".$_REQUEST['password']."','".$_REQUEST['isSandbox']."','".date('Y-m-d H:i:s')."')");
	$receipt_id = $con->insert_id;
}
else{
	$receipt_d = $sel->fetch_assoc();
	$receipt_id = $receipt_d['id'];
}

//print_r($response); die;

if(!empty($response))
{
	$expirationDateMs = max(array_column($response['latest_receipt_info'], 'expires_date_ms'));
	$requestDateMs = $response['receipt']['request_date_ms'];

	$status = $response['status'];
	if($status == 0 && $expirationDateMs > $requestDateMs)
	{
		$msg['status'] = true;
		$msg['msg'] = 'success';
		$msg['pro_status'] = true;
		$msg['data'] = new arrayobject();

		$upd = $con->query("UPDATE tbl_receipt_data SET pro_status = 'true' WHERE id='".$receipt_id."'");
	}
	else
	{
        $msg['status'] = true;
        $msg['msg'] = 'success';
        $msg['pro_status'] = false;
        $msg['data'] = new arrayobject();
	}
}
else
{
    $msg['status'] = true;
    $msg['msg'] = 'success';
    $msg['pro_status'] = false;
    $msg['data'] = new arrayobject();
}
echo json_encode($msg);	

/*[[jsonResponse objectForKey:@"status"] integerValue] == 0 && (expirationDateMs > requestDateMs);*/



function callAPI($method, $url, $data){
   $curl = curl_init();

   switch ($method){
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
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
   ));
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}
/*$errors   = $response['response']['errors'];
$data     = $response['response']['data'][0];*/

?>