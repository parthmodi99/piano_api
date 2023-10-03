<?php 
include("dbconfig.php");
include("dbfunction.php");
$msg['status']=false;

$msg['message']="failed";

$msg['data']=new arrayobject();

mysqli_set_charset($con,'utf8');
if(isset($_POST['type']) && $_POST['type']!='')
{
	$data = array();
	$page_slug=$_POST['type'];
	$qry_get_page=$con->query("select * from tbl_cms where page_slug='".$page_slug."'");
	if($qry_get_page->num_rows>0)
	{
		$row_page=$qry_get_page->fetch_assoc();
		$data['page_title']=$row_page['page_title'];
		$data['page_slug']=$row_page['page_slug'];
		$data['content']=$row_page['content'];
		$data['created_at']=$row_page['created_at'];

		$msg['status']=true;
		$msg['message']='success';
		$msg['data']=$data;	
	}
	else
	{
		$msg['message']='Not found';

	}

	
}

echo json_encode($msg);
?>