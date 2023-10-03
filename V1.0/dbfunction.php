<?php

include("dbconfig.php");
function check_email($email,$con)
{
	$qry=$con->query("SELECT email from tbl_user where email='".$email."'");
	if($qry->num_rows>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}
function get_user($con,$user_id)
{
	$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";
	$data=array();
	$qry=$con->query("SELECT * from tbl_user  where id='".$user_id."'");
	if($qry->num_rows>0)
	{
		$data=$qry->fetch_assoc();
		$user_data['id'] = $data['id'];
		$user_data['name'] = $data['name']!=''?$data['name']:'';
		$user_data['email'] = $data['email']!=''?$data['email']:'';
		$user_data['mobile'] = $data['mobile']!=''?$data['mobile']:'';
		$user_data['gender'] = $data['gender']!=''?$data['gender']:'';
		$user_data['device_type'] = $data['device_type']!=''?$data['device_type']:'';
		$user_data['device_token'] = $data['device_token']!=''?$data['device_token']:'';
		$user_data['social_key'] = $data['social_key']!=''?$data['social_key']:'';
		$user_data['referral_code'] = $data['referral_code']!=''?$data['referral_code']:'';
		//$user_data['profile_pic'] = $data['profile_pic']!=''?$server_path.''.$data['profile_pic']:'';
		if($data['profile_pic']!='')
	    {
	      if (strpos($data['profile_pic'], 'http')!==false)
	      {
	        $user_data['profile_pic']=$data['profile_pic'];
	      }
	      else
	      {
	        $user_data['profile_pic']=$server_path.$data['profile_pic'];
	      }
	    }
	    else
	    {
	     $user_data['profile_pic']='';
	   	}
		$user_data['timezone'] = $data['timezone']!=''?$data['timezone']:'';
		$user_data['created_at'] = $data['created_at']!=''?$data['created_at']:'';
		return $user_data;
	}
	else{
		$data=new arrayobject();
		return $data;
	}
}
function check_email_exist1($con,$email)
{
	$qry=$con->query("SELECT email from tbl_user where email='".$email."'");
	if($qry->num_rows>0)
	{
		return false;
	}
	else
	{
		return true;
	}
}
function get_data_from_email($email,$con)
{
	$qry=$con->query("SELECT * from tbl_user where email='".$email."'");
	if($qry->num_rows>0)
	{
		$data=$qry->fetch_assoc();

	}
	else{
		$data=new arrayobject();
	}
	return $data;
}
function RandomString()
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring = '';
	for ($i = 0; $i < 6; $i++) {
		$randstring.= $characters[rand(0, strlen($characters))];
	}
	return $randstring;
}
function is_block($con,$email)
{
	$qry=$con->query("SELECT email from tbl_user where email='".$email."' AND is_block=1");
	if($qry->num_rows>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function check_email_exist_with_userid($email,$con,$user_id)
{

	$qry=$con->query("SELECT * from tbl_user where email='".$email."'");
	if($qry->num_rows>0)
	{
		$data = $qry->fetch_assoc();
		if($data['id'] == $user_id)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		return false;
	}
}
function gettotalexercise($con,$user_id)
{
	$sel = $con->query("SELECT count(id) as total_workout FROM tbl_exercise WHERE user_id='".$user_id."'");
	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		return $d['total_workout'];
	}
	else
	{
		return 0;
	}
}
function getlast30dayexercise($con,$user_id)
{
	$start_date = date('Y-m-d H:i:s', strtotime('today - 30 days'));
	$end_date = date('Y-m-d 23:59:00');



	$sel = $con->query("SELECT count(id) as total_workout FROM tbl_exercise WHERE user_id = '$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");

	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		return $d['total_workout'];
	}
	else
	{
		return 0;
	}
}
function getlast7dayexercise($con,$user_id)
{
	$stop_date = date('Y-m-d 00:00:01', strtotime(date('Y-m-d H:i:s') . ' +1 day'));
	$start_date = date('Y-m-d 00:00:01', strtotime($stop_date.' - 7 days'));
	$end_date = date('Y-m-d 23:59:00');


	$sel = $con->query("SELECT count(id) as total_workout, DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND  workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly ORDER BY id DESC");

	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		//return $d['total_workout'];
		return $sel->num_rows;
	}
	else
	{
		return 0;
	}
}


function gettotalworkout($con,$user_id)
{
	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' GROUP BY DateOnly");

	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		//return $d['total_workout'];
		return $sel->num_rows;
	}
	else
	{
		return 0;
	}
}

function gettotaltime($con,$user_id)
{
	$times = array();
	$sel1 = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id'");
	if($sel1->num_rows > 0)
	{
		while($d1 = $sel1->fetch_assoc())
		{
			$times[] = $d1['exercise_total_time'];
		}
		$total_time = sum_the_time($times);

		return $total_time;
	}
	else
	{
		return '00:00:00';
	}

}

function getaveragemuscle($con,$user_id)
{

	$sel = $con->query("SELECT count(id) as total_workout FROM tbl_exercise WHERE user_id='$user_id'");
	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		$total = $d['total_workout'];

		$get_m = $con->query("SELECT count(id) as total_musle FROM tbl_exercise WHERE user_id='$user_id' AND exercise_type='muscle'");
		if($get_m->num_rows > 0)
		{
			$get_musle = $get_m->fetch_assoc();
			$musle = $get_musle['total_musle'];
			$per = $musle*100/$total;
			return $per.'%';
		}
		else
		{
			return '0%';
		}

	}
	else
	{
		return '0%';
	}
}

function getaveragestrength($con,$user_id)
{

	$sel = $con->query("SELECT count(id) as total_workout FROM tbl_exercise WHERE user_id='$user_id'");
	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		$total = $d['total_workout'];

		$get_m = $con->query("SELECT count(id) as total_str FROM tbl_exercise WHERE user_id='$user_id' AND exercise_type='strength'");
		if($get_m->num_rows > 0)
		{
			$get_musle = $get_m->fetch_assoc();
			$musle = $get_musle['total_str'];
			$per = $musle*100/$total;
			return $per.'%';
		}
		else
		{
			return '0%';
		}

	}
	else
	{
		return '0%';
	}
}

function getaverageendurance($con,$user_id)
{

	$sel = $con->query("SELECT count(id) as total_workout FROM tbl_exercise WHERE user_id='$user_id'");
	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		$total = $d['total_workout'];

		$get_m = $con->query("SELECT count(id) as total_end FROM tbl_exercise WHERE user_id='$user_id' AND exercise_type='endurance'");
		if($get_m->num_rows > 0)
		{
			$get_musle = $get_m->fetch_assoc();
			$musle = $get_musle['total_end'];
			$per = $musle*100/$total;
			return $per.'%';
		}
		else
		{
			return '0%';
		}

	}
	else
	{
		return '0%';
	}
}

function getaveragetime($con,$user_id)
{
	$sel = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id'");
	if($sel->num_rows > 0)
	{
		$sec = 0;
		while($tt = $sel->fetch_assoc())
		{
			$str_time = $tt['exercise_total_time'];

			$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

			$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

			//$new_seconds = $seconds + $time_seconds;

			$sec += $time_seconds;

		}
		$average_sec = $sec/$sel->num_rows;

		$average_time = secondss($average_sec);

		return $average_time;


	}
	else
	{
		return "00:00:00";
	}

}
function getaverageexercise($con,$user_id)
{

	$sel = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record FROM tbl_exercise WHERE user_id='$user_id'");
	if($sel->num_rows > 0)
	{
		$d = $sel->fetch_assoc();
		$average = round($d['total_exercise']/$d['total_record']);
		return $average;
	}
	else
	{
		return 0;
	}
}

function week_between_two_dates($date1, $date2)
{
    $first = DateTime::createFromFormat('m/d/Y', $date1);
    $second = DateTime::createFromFormat('m/d/Y', $date2);
    if($date1 > $date2) return week_between_two_dates($date2, $date1);
    return substr($first->diff($second)->days/7, 0, 3);
}

function gettotalskilldata($con,$user_id)
{
	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$dates = $sel->fetch_assoc();
		$dt1 = date('m/d/Y',strtotime($dates['workout_start_time']));
		$dt2 = date('m/d/Y');
		$diff = week_between_two_dates($dt1, $dt2);

		$times = array();
		$i = 0;
		$level = 0;
		$j = 0;
		while($d = $sel->fetch_assoc())
		{
			$i++;
			$j++;
			if($i==4)
			{
				$level = 1;
				$j = 0;
			}
			else
			{
				if($j == 4)
				{
					$level++;
					$j = 0;
				}

			}
			//$times[] = $d['exercise_total_time'];
		}



		//for time calculation
		$sel1 = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id'");
		if($sel1->num_rows > 0)
		{
			while($d1 = $sel1->fetch_assoc())
			{
				$times[] = $d1['exercise_total_time'];
			}
		}



		if($diff == 0)
		{
			$average = $sel->num_rows;
		}
		else
		{
			$average = substr($sel->num_rows / $diff, 0, 3);
		}

		$level = ($sel->num_rows/5 + 1);

		$wo['total_workout'] = (string)$sel->num_rows;
		$wo['total_time'] = sum_the_time($times);
		$wo['level'] = (string)floor($level);
		$wo['average'] = (string)sprintf("%.1f", $average);

		return $wo;
	}
	else
	{
		$wo['total_workout'] = '0';
		$wo['total_time'] = '00:00:00';
		$wo['level'] = '0';
		$wo['average'] = '0.0';
		return $wo;
	}
}

function getyearlyskilldata($con,$user_id,$levell = 0)
{
	$start_date = date('Y-m-d H:i:s', strtotime('today - 365 days'));
	$end_date = date('Y-m-d 23:59:00');

	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$dates = $sel->fetch_assoc();
		$dt1 = date('m/d/Y',strtotime($start_date));
		$dt2 = date('m/d/Y',strtotime($end_date));
		$diff = week_between_two_dates($dt1, $dt2);


		$times = array();
		$i = 0;
		$level = 0;
		$j = 0;
		while($d = $sel->fetch_assoc())
		{
			$i++;
			$j++;
			if($i==4)
			{
				$level = 1;
				$j = 0;
			}
			else
			{
				if($j == 4)
				{
					$level++;
					$j = 0;
				}

			}
			//$times[] = $d['exercise_total_time'];
		}


		//for time calculation
		$sel1 = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel1->num_rows > 0)
		{
			while($d1 = $sel1->fetch_assoc())
			{
				$times[] = $d1['exercise_total_time'];
			}
		}


		if($diff == 0)
		{
			$average = $sel->num_rows;
		}
		else
		{
			$average = substr($sel->num_rows / $diff, 0, 3);
		}
		$wo['total_workout'] = (string)$sel->num_rows;
		$wo['total_time'] = sum_the_time($times);
		$wo['level'] = (string)$levell;
		$wo['average'] = (string)sprintf("%.1f", $average);

		return $wo;
	}
	else
	{
		$wo['total_workout'] = '0';
		$wo['total_time'] = '00:00:00';
		$wo['level'] = (string)$levell;
		$wo['average'] = '0.0';

		return $wo;
	}
}

function getmonthlyskilldata($con,$user_id,$levell = 0)
{
	$start_date = date('Y-m-d H:i:s', strtotime('today - 30 days'));
	$end_date = date('Y-m-d 23:59:00');

	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$dates = $sel->fetch_assoc();
		$dt1 = date('m/d/Y',strtotime($start_date));
		$dt2 = date('m/d/Y',strtotime($end_date));
		$diff = '4.3';



		$times = array();
		$i = 0;
		$level = 0;
		$j = 0;
		while($d = $sel->fetch_assoc())
		{
			$i++;
			$j++;
			if($i==4)
			{
				$level = 1;
				$j = 0;
			}
			else
			{
				if($j == 4)
				{
					$level++;
					$j = 0;
				}

			}
			//$times[] = $d['exercise_total_time'];
		}


		//for time calculation
		$sel1 = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel1->num_rows > 0)
		{
			while($d1 = $sel1->fetch_assoc())
			{
				$times[] = $d1['exercise_total_time'];
			}
		}


		if($diff == 0)
		{
			$average = $sel->num_rows;
		}
		else
		{
			$average = substr($sel->num_rows / $diff, 0, 3);
		}
		$wo['total_workout'] = (string)$sel->num_rows;
		$wo['total_time'] = sum_the_time($times);
		$wo['level'] = (string)$levell;
		$wo['average'] = (string)sprintf("%.1f", $average);

		return $wo;
	}
	else
	{
		$wo['total_workout'] = '0';
		$wo['total_time'] = '00:00:00';
		$wo['level'] = (string)$levell;
		$wo['average'] = '0.0';

		return $wo;
	}
}

function getweeklyskilldata($con,$user_id,$levell = 0)
{
	$stop_date = date('Y-m-d 00:00:01', strtotime(date('Y-m-d H:i:s') . ' +1 day'));
	$start_date = date('Y-m-d 00:00:01', strtotime($stop_date.' - 7 days'));
	$end_date = date('Y-m-d 23:59:00');

	/*echo "SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND exercise_date_time BETWEEN '".$start_date."' AND '".$end_date."'"; die;*/


	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$times = array();
		$i = 0;
		$level = 0;
		$j = 0;
		while($d = $sel->fetch_assoc())
		{
			$i++;
			$j++;
			if($i==4)
			{
				$level = 1;
				$j = 0;
			}
			else
			{
				if($j == 4)
				{
					$level++;
					$j = 0;
				}

			}
			//$times[] = $d['exercise_total_time'];
		}
		//for time calculation
		$sel1 = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel1->num_rows > 0)
		{
			while($d1 = $sel1->fetch_assoc())
			{
				$times[] = $d1['exercise_total_time'];
			}
		}
		/*if($level != 0)
		{
			$average = substr($level / $sel->num_rows, 0, 3);
		}
		else
		{
			$average = '0.0';
		}*/
		$average = $sel->num_rows.'.0';
		$wo['total_workout'] = (string)$sel->num_rows;
		$wo['total_time'] = sum_the_time($times);
		$wo['level'] = (string)$levell;
		$wo['average'] = (string)$average;

		return $wo;
	}
	else
	{
		$wo['total_workout'] = '0';
		$wo['total_time'] = '00:00:00';
		$wo['level'] = (string)$levell;
		$wo['average'] = '0.0';

		return $wo;
	}
}


function sum_the_time($times) {
  //$times = array($time1, $time2);
  $seconds = 0;
  foreach ($times as $time)
  {
    list($hour,$minute,$second) = explode(':', $time);
    $seconds += $hour*3600;
    $seconds += $minute*60;
    $seconds += $second;
  }
  $hours = floor($seconds/3600);
  $seconds -= $hours*3600;
  $minutes  = floor($seconds/60);
  $seconds -= $minutes*60;
  // return "{$hours}:{$minutes}:{$seconds}";
  return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); // Thanks to Patrick
}

/*-------------------------------average----------------------------*/
function gettotalaveragedata($con,$user_id)
{
	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$sec = 0;
		$average_time = '00:00:00';
		$total_quantity = 0;
		while($tt = $sel->fetch_assoc())
		{
			$str_time = $tt['exercise_total_time'];

			$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

			$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

			$total_quantity += $tt['workout_quality'];

			//$new_seconds = $seconds + $time_seconds;

			$sec += $time_seconds;

		}
		if($total_quantity!=0)
		{
			$total_quantity = round($total_quantity / $sel->num_rows);
		}
		$average_sec = $sec/$sel->num_rows;

		$average_time = secondss($average_sec);

		$average_exe = 0;
		/*---------------------------average workout-----------------------------*/
		/*$sel = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' GROUP BY DateOnly");
		if($sel->num_rows > 0)
		{
			$d = $sel->fetch_assoc();
			$average_exe = round($d['total_exercise']/$d['total_record']);
		}*/
		/*----------------------------average time-------------------------*/
		$sel_time = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id'");
		if($sel_time->num_rows > 0)
		{
			$sec = 0;
			$average_time = '00:00:00';
			//$total_quantity = 0;
			while($tt = $sel_time->fetch_assoc())
			{
				$str_time = $tt['exercise_total_time'];

				$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

				sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

				$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

				//$total_quantity += $tt['workout_quality'];

				//$new_seconds = $seconds + $time_seconds;

				$sec += $time_seconds;

			}
			$average_sec = $sec/$sel->num_rows;

			$average_time = secondss($average_sec);

			$average_exe = 0;
		}
		/*-------------------------------------------------------------------------*/
		/*--------------------------------------average quality-------------------*/

		$sel_qua = $con->query("SELECT sum(workout_quality) as total_quality,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' group by DateOnly");
		if($sel_qua->num_rows > 0)
		{

			$total_quantity = 0;
			while($ttt = $sel_qua->fetch_assoc())
			{

				$quantity = $ttt['total_quality'];
				if($quantity > 100)
				{
					$quantity = 100;
				}
				$total_quantity += $quantity;

			}
			if($total_quantity!=0)
			{
				$total_quantity = round($total_quantity / $sel->num_rows);
			}
		}
		/*-------------------------------------------------------------------*/
		/*-------------------------average exercise----------------------------*/
		$sel1 = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id'");
		if($sel1->num_rows > 0)
		{
			$d = $sel1->fetch_assoc();
			$average_exe = round($d['total_exercise']/$sel->num_rows);
		}

		/*------------------------------------------*/
		$wo['time'] = $average_time;
		$wo['exercise'] = (string)$average_exe;
		$wo['quantity'] = (string)$total_quantity;
		return $wo;
	}
	else
	{
		$wo['time'] = '00:00:00';
		$wo['exercise'] = '0';
		$wo['quantity'] = '0';
		return $wo;
	}

}

function getyearlyaveragedata($con,$user_id)
{
	$start_date = date('Y-m-d H:i:s', strtotime('today - 365 days'));
	$end_date = date('Y-m-d 23:59:00');

	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$sec = 0;
		$average_time = '00:00:00';
		$total_quantity = 0;
		while($tt = $sel->fetch_assoc())
		{
			$str_time = $tt['exercise_total_time'];

			$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

			$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

			$total_quantity += $tt['workout_quality'];

			//$new_seconds = $seconds + $time_seconds;

			$sec += $time_seconds;

		}
		if($total_quantity!=0)
		{
			$total_quantity = round($total_quantity / $sel->num_rows);
		}
		$average_sec = $sec/$sel->num_rows;

		$average_time = secondss($average_sec);

		$average_exe = 0;
		/*---------------------------average workout-----------------------------*/
		/*$sel = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
		if($sel->num_rows > 0)
		{
			$d = $sel->fetch_assoc();
			$average_exe = round($d['total_exercise']/$d['total_record']);
		}*/
		/*----------------------------average time-------------------------*/
		$sel_time = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel_time->num_rows > 0)
		{
			$sec = 0;
			$average_time = '00:00:00';
			//$total_quantity = 0;
			while($tt = $sel_time->fetch_assoc())
			{
				$str_time = $tt['exercise_total_time'];

				$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

				sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

				$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

				//$total_quantity += $tt['workout_quality'];

				//$new_seconds = $seconds + $time_seconds;

				$sec += $time_seconds;

			}
			$average_sec = $sec/$sel->num_rows;

			$average_time = secondss($average_sec);

			$average_exe = 0;
		}
		/*-------------------------------------------------------------------------*/
		/*--------------------------------------average quality-------------------*/

		$sel_qua = $con->query("SELECT sum(workout_quality) as total_quality,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' group by DateOnly");
		if($sel_qua->num_rows > 0)
		{

			$total_quantity = 0;
			while($ttt = $sel_qua->fetch_assoc())
			{

				$quantity = $ttt['total_quality'];
				if($quantity > 100)
				{
					$quantity = 100;
				}
				$total_quantity += $quantity;

			}
			if($total_quantity!=0)
			{
				$total_quantity = round($total_quantity / $sel->num_rows);
			}
		}
		/*-------------------------------------------------------------------*/
		/*-------------------------average exercise----------------------------*/
		$sel1 = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel1->num_rows > 0)
		{
			$d = $sel1->fetch_assoc();
			$average_exe = round($d['total_exercise']/$sel->num_rows);
		}

		/*------------------------------------------*/
		$wo['time'] = $average_time;
		$wo['exercise'] = (string)$average_exe;
		$wo['quantity'] = (string)$total_quantity;
		return $wo;
	}
	else
	{
		$wo['time'] = '00:00:00';
		$wo['exercise'] = '0';
		$wo['quantity'] = '0';
		return $wo;
	}

}

function getmonthlyaveragedata($con,$user_id)
{
	$start_date = date('Y-m-d H:i:s', strtotime('today - 30 days'));
	$end_date = date('Y-m-d 23:59:00');

	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$sec = 0;
		$average_time = '00:00:00';
		$total_quantity = 0;
		while($tt = $sel->fetch_assoc())
		{
			$str_time = $tt['exercise_total_time'];

			$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

			$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

			$total_quantity += $tt['workout_quality'];

			//$new_seconds = $seconds + $time_seconds;

			$sec += $time_seconds;

		}
		if($total_quantity!=0)
		{
			$total_quantity = round($total_quantity / $sel->num_rows);
		}
		$average_sec = $sec/$sel->num_rows;

		$average_time = secondss($average_sec);

		$average_exe = 0;


		/*----------------------------average time-------------------------*/
		$sel_time = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel_time->num_rows > 0)
		{
			$sec = 0;
			$average_time = '00:00:00';
			//$total_quantity = 0;
			while($tt = $sel_time->fetch_assoc())
			{
				$str_time = $tt['exercise_total_time'];

				$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

				sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

				$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

				//$total_quantity += $tt['workout_quality'];

				//$new_seconds = $seconds + $time_seconds;

				$sec += $time_seconds;

			}
			$average_sec = $sec/$sel->num_rows;

			$average_time = secondss($average_sec);

			$average_exe = 0;
		}
		/*-------------------------------------------------------------------------*/
		/*--------------------------------------average quality-------------------*/

		$sel_qua = $con->query("SELECT sum(workout_quality) as total_quality,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' group by DateOnly");
		if($sel_qua->num_rows > 0)
		{

			$total_quantity = 0;
			while($ttt = $sel_qua->fetch_assoc())
			{

				$quantity = $ttt['total_quality'];
				if($quantity > 100)
				{
					$quantity = 100;
				}
				$total_quantity += $quantity;

			}
			if($total_quantity!=0)
			{
				$total_quantity = round($total_quantity / $sel->num_rows);
			}
		}
		/*-------------------------------------------------------------------*/
		/*-------------------------average exercise----------------------------*/
		$sel1 = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel1->num_rows > 0)
		{
			$d = $sel1->fetch_assoc();
			$average_exe = round($d['total_exercise']/$sel->num_rows);
		}

		/*------------------------------------------*/
		$wo['time'] = $average_time;
		$wo['exercise'] = (string)$average_exe;
		$wo['quantity'] = (string)$total_quantity;
		return $wo;
	}
	else
	{
		$wo['time'] = '00:00:00';
		$wo['exercise'] = '0';
		$wo['quantity'] = '0';
		return $wo;
	}

}

function getweeklyaveragedata($con,$user_id)
{
	$stop_date = date('Y-m-d 00:00:01', strtotime(date('Y-m-d H:i:s') . ' +1 day'));
	$start_date = date('Y-m-d 00:00:01', strtotime($stop_date.' - 7 days'));
	$end_date = date('Y-m-d 23:59:00');

	/*echo "SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND exercise_date_time BETWEEN '".$start_date."' AND '".$end_date."'"; die;*/
	/*echo "SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly"; die;*/
	$sel = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
	if($sel->num_rows > 0)
	{
		$sec = 0;
		$average_time = '00:00:00';
		$total_quantity = 0;
		while($tt = $sel->fetch_assoc())
		{
			$str_time = $tt['exercise_total_time'];

			$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

			sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

			$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

			$total_quantity += $tt['workout_quality'];

			//$new_seconds = $seconds + $time_seconds;

			$sec += $time_seconds;

		}
		if($total_quantity!=0)
		{
			$total_quantity = round($total_quantity / $sel->num_rows);
		}
		$average_sec = $sec/$sel->num_rows;

		$average_time = secondss($average_sec);

		$average_exe = 0;
		/*---------------------------average workout-----------------------------*/
		/*$sel = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY DateOnly");
		if($sel->num_rows > 0)
		{
			$d = $sel->fetch_assoc();
			$average_exe = round($d['total_exercise']/$d['total_record']);
		}*/

		/*------------------------------------------*/

		/*----------------------------average time-------------------------*/
		$sel_time = $con->query("SELECT *,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel_time->num_rows > 0)
		{
			$sec = 0;
			$average_time = '00:00:00';
			//$total_quantity = 0;
			while($tt = $sel_time->fetch_assoc())
			{
				$str_time = $tt['exercise_total_time'];

				$str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);

				sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

				$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;

				//$total_quantity += $tt['workout_quality'];

				//$new_seconds = $seconds + $time_seconds;

				$sec += $time_seconds;

			}
			$average_sec = $sec/$sel->num_rows;

			$average_time = secondss($average_sec);

			$average_exe = 0;
		}
		/*-------------------------------------------------------------------------*/
		/*--------------------------------------average quality-------------------*/
		$sel_qua = $con->query("SELECT sum(workout_quality) as total_quality,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."' group by DateOnly");
		if($sel_qua->num_rows > 0)
		{

			$total_quantity = 0;
			while($ttt = $sel_qua->fetch_assoc())
			{

				$quantity = $ttt['total_quality'];
				if($quantity > 100)
				{
					$quantity = 100;
				}
				$total_quantity += $quantity;

			}
			if($total_quantity!=0)
			{
				$total_quantity = round($total_quantity / $sel->num_rows);
			}
		}
		/*-------------------------------------------------------------------*/
		/*-------------------------average exercise----------------------------*/
		$sel1 = $con->query("SELECT sum(total_exercise) as total_exercise,count(id) as total_record,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$start_date."' AND '".$end_date."'");
		if($sel1->num_rows > 0)
		{
			$d = $sel1->fetch_assoc();
			$average_exe = round($d['total_exercise']/$sel->num_rows);
		}

		/*------------------------------------------*/
		$wo['time'] = $average_time;
		$wo['exercise'] = (string)$average_exe;
		$wo['quantity'] = (string)$total_quantity;
		return $wo;
	}
	else
	{
		$wo['time'] = '00:00:00';
		$wo['exercise'] = '0';
		$wo['quantity'] = '0';
		return $wo;
	}

}
/*-----------------------------------------------------------------------*/

/*----------------------get time data----------------------------*/
function gettimedata($con,$user_id)
{
	$ff = array();
	/*--------get 00 - 06h data----------------*/
	$t1 = date('00:00:00');
	$t2 = date('06:00:00');

	$all_max = array();

	/*echo "SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND exercise_date_time BETWEEN '".$t1."' AND '".$t2."'"; die;*/
	/*echo "SELECT *,CAST(exercise_date_time AS time) as time FROM tbl_exercise WHERE user_id='91' and CAST(exercise_date_time AS time) BETWEEN CAST('00:00:00' AS time) AND CAST('06:00:00' AS time)"; die;*/

	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '00 - 06h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '00 - 06h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 06 - 08h data----------------*/
	$t1 = date('06:00:00');
	$t2 = date('08:00:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '06 - 08h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '06 - 08h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 08 - 10h data----------------*/
	$t1 = date('08:00:00');
	$t2 = date('10:00:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '08 - 10h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '08 - 10h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 10 - 12h data----------------*/
	$t1 = date('10:00:00');
	$t2 = date('12:00:00');

	//$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '10 - 12h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '10 - 12h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 12 - 14h data----------------*/
	$t1 = date('12:00:00');
	$t2 = date('14:00:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '12 - 14h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '12 - 14h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 14 - 16h data----------------*/
	$t1 = date('14:00:00');
	$t2 = date('16:00:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '14 - 16h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '14 - 16h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 16 - 18h data----------------*/
	$t1 = date('16:00:00');
	$t2 = date('18:00:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '16 - 18h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '16 - 18h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 18 - 20h data----------------*/
	$t1 = date('18:00:00');
	$t2 = date('20:00:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '18 - 20h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '18 - 20h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 20 - 22h data----------------*/
	$t1 = date('20:00:00');
	$t2 = date('22:00:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '20 - 22h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '20 - 22h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/
	/*--------get 22 - 00h data----------------*/
	$t1 = date('22:00:00');
	$t2 = date('23:59:00');

	/*$sel1 = $con->query("SELECT * FROM tbl_exercise WHERE user_id='$user_id' AND workout_start_time BETWEEN '".$t1."' AND '".$t2."'");*/
	$sel1 = $con->query("SELECT *,CAST(workout_start_time AS time) as time,DATE(workout_start_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' and CAST(workout_start_time AS time) BETWEEN CAST('".$t1."' AS time) AND CAST('".$t2."' AS time) GROUP BY DateOnly");
	if($sel1->num_rows > 0)
	{
		$t['time'] = '22 - 00h';
		$t['workout'] = (string)$sel1->num_rows;
		$all_max[] = (string)$sel1->num_rows;
		$ff[] = $t;
	}
	else
	{
		$t['time'] = '22 - 00h';
		$t['workout'] = '';
		$all_max[] = '';
		$ff[] = $t;
	}
	/*----------------------------------------*/

	$b= max($all_max);

	$fff['data'] = $ff;
	$fff['max'] = $b;

	return $fff;
}
/*---------------------------------------------------------------*/

function getweekdata($con,$user_id)
{
   $all_max = array();
   for($i=7; $i>=1; $i--)
   {
      $q = 0;
      $stop_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +1 day'));


      $sdate = date('Y-m-d 00:00:01',(strtotime ( "-$i day" , strtotime ( $stop_date) ) ));
      $edate = date('Y-m-d 23:59:00',(strtotime ( "-$i day" , strtotime ( $stop_date) ) ));

      $day_name = date('l', strtotime($sdate));

      /*print_r($day_name); die;*/
      $day = '';
      $day_short = '';
      if($i == 7)
      {
      	$day = 'Monday';
      	$day_short = 'M';
      }
      elseif ($i == 6)
      {
      	$day = 'Tuesday';
      	$day_short = 'T';
      }
       elseif ($i == 5)
      {
      	$day = 'Wednesday';
      	$day_short = 'W';
      }
       elseif ($i == 4)
      {
      	$day = 'Thursday';
      	$day_short = 'T';
      }
       elseif ($i == 3)
      {
      	$day = 'Friday';
      	$day_short = 'F';
      }
       elseif ($i == 2)
      {
      	$day = 'Saturday';
      	$day_short = 'S';
      }
       elseif ($i == 1)
      {
      	$day = 'Sunday';
      	$day_short = 'S';
      }
      else
      {
      	$day = '';
      	$day_short = '';
      }


      /*$check_exercise = $con->query("SELECT *,DATE(exercise_date_time) DateOnly FROM tbl_exercise WHERE user_id='$user_id' AND exercise_date_time BETWEEN '".$sdate."' AND '".$edate."' GROUP BY DateOnly");*/
       $check_exercise = $con->query("select *,DAYNAME(workout_start_time) as tt,DATE(workout_start_time) DateOnly FROM `tbl_exercise` WHERE `user_id` = '$user_id' AND DAYNAME(workout_start_time) = '$day' GROUP BY DateOnly");
      if($check_exercise->num_rows > 0)
      {
     /* 	print_r($check_exercise->num_rows); die;*/
         $tt++;
         while($w = $check_exercise->fetch_assoc())
         {
            $total_workout = $total_workout + $w['total_exercise'];
            $q = $q + $w['workout_quality'];
         }


         $d['day'] = $day_short;
         $d['value'] = 1;
         $d['quality'] = $q;
         $d['workout'] = (string)$check_exercise->num_rows;
         $all_max[] = (string)$check_exercise->num_rows;

      }
      else
      {
         $d['day'] = $day_short;
         $d['value'] = 0;
         $d['quality'] = $q;
         $d['workout'] = '0';
         $all_max[] = '0';
      }
      $workout[] = $d;
   }

   $b= max($all_max);

	$fff['data'] = $workout;
	$fff['max'] = $b;

	return $fff;
  // return $workout;
}

function secondss($seconds) {
  $t = round($seconds);
  return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
}

function get_user_exercise_byid($con,$user_id)
{
	$sel = $con->query("SELECT * FROM tbl_exercise WHERE user_id='".$user_id."'");
	if($sel->num_rows > 0)
	{
		$work_out = array();
		while($data = $sel->fetch_assoc())
		{
			$workout['user_id'] = $data['user_id'];
			$workout['exercise_type'] = $data['exercise_type'];
			$workout['date'] = $data['exercise_date_time'];
			$workout['workout_start_time'] = $data['workout_start_time'];
			$workout['exercise_time'] = $data['exercise_total_time'];
			$workout['total_exercise'] = $data['total_exercise'];
			$workout['workout_quality'] = $data['workout_quality'];
			$work_out[] = $workout;

		}
		return $work_out;
	}
	else
	{
		return array();
	}
}

function get_user_byid($con,$user_id)
{
	$server_path="http://".$_SERVER['HTTP_HOST']."/APIs/";
	$data=array();
	$qry=$con->query("SELECT * from tbl_user  where id='".$user_id."'");
	if($qry->num_rows>0)
	{
		$data=$qry->fetch_assoc();
		$user_data['user_id'] = $data['id'];
		$user_data['name'] = $data['name']!=''?$data['name']:'';
		$user_data['email'] = $data['email']!=''?$data['email']:'';
		$user_data['mobile'] = $data['mobile']!=''?$data['mobile']:'';
		$user_data['gender'] = $data['gender']!=''?$data['gender']:'';
		$user_data['device_type'] = $data['device_type']!=''?$data['device_type']:'';
		$user_data['device_token'] = $data['device_token']!=''?$data['device_token']:'';
		$user_data['referral_code'] = $data['referral_code']!=''?$data['referral_code']:'';
		//$user_data['profile_pic'] = $data['profile_pic']!=''?$server_path.''.$data['profile_pic']:'';
		if($data['profile_pic']!='')
	    {
	      if (strpos($data['profile_pic'], 'http')!==false)
	      {
	        $user_data['profile_pic']=$data['profile_pic'];
	      }
	      else
	      {
	        $user_data['profile_pic']=$server_path.$data['profile_pic'];
	      }
	    }
	    else
	    {
	     $user_data['profile_pic']='';
	   	}
		$user_data['hidden_mode'] = $data['hidden_mode']!=''?$data['hidden_mode']:0;
		$user_data['is_react_user'] = $data['is_react_user']!=''?$data['is_react_user']:0;
		$user_data['created_at'] = $data['created_at']!=''?$data['created_at']:'';
		return $user_data;
	}
	else{
		$data=new arrayobject();
		return $data;
	}
}

function getnewrequestcount($con,$user_id)
{

	$qry=$con->query("SELECT COUNT(`id`) as new_request from tbl_request where to_user_id='".$user_id."' AND view_status=0");
	if($qry->num_rows>0)
	{
		$req_data = $qry->fetch_assoc();
		return $req_data['new_request'];
	}
	else
	{
		return 0;
	}
}

function sendnotification($con,$title,$device_type,$device_token,$user_id,$to_user_id,$type,$noti_t,$is_react_user = 0,$body = '')
{
	if($is_react_user == 0)
	{
		$apnsServer = 'ssl://gateway.push.apple.com:2195';
		//'ssl://gateway.sandbox.push.apple.com:2195';
		//ssl://gateway.push.apple.com:2195
		$privateKeyPassword = '1';
		$message=$title;
		$deviceToken = $device_token;
		$pushCertAndKeyPemFile = 'pushcert.pem';  //'GymTimer_Push_Distribution.pem';
		$stream = stream_context_create();
		stream_context_set_option($stream,'ssl','passphrase',$privateKeyPassword);
		stream_context_set_option($stream,'ssl','local_cert',$pushCertAndKeyPemFile);
		$connectionTimeout = 20;
		$connectionType = STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT;
		$connection = stream_socket_client($apnsServer,$errorNumber,$errorString,$connectionTimeout,$connectionType,$stream);
		if (!$connection){
			//echo "Failed to connect to the APNS server. Error no = $errorNumber<br/>";
			//exit;
		} else {
			//echo "Successfully connected to the APNS. Processing...</br>";
		}

		$messageBody['aps'] = array(
			 'message'=>'success',
	        'sound'=> 'default',
	        'title' =>$title,
	        'alert' => $title,
	        'type' =>$type,
	        'badge' => +1,
	        'user_id' => $user_id
		);

		$payload = json_encode($messageBody);
		$notification = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		$wroteSuccessfully = fwrite($connection, $notification, strlen($notification));
		if (!$wroteSuccessfully){
			//echo "Could not send the message<br/>";
			$result = 'False';
		}
		else {
			$result = 'True';
	        //echo "Successfully sent the message<br/>";
		}
		fclose($connection);
		$data = json_encode($messageBody['aps']);

	    $current_datetime = date("Y-m-d h:i:s");
	    $qry_insert_notification=$con->query("INSERT INTO `tbl_notification`(`from_user_id`,`to_user_id`, `message`,`type`, `created_at`) VALUES (".$user_id.",'".$to_user_id."','".$data."','".$noti_t."','".$current_datetime."')");
		return $result;
	}
	else{

		if($body == '')
		{
			$body = $title;
		}
		 
		 $sound = 'default';

		 $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';

		 if($device_type == 'A')/*android*/
		 {
			 $fields = array(
			 'to' => $device_token,
			 'priority' => 'high',
			 'data' => array('title' => $title, 'body' => $body,'sound' => $sound,'userid'=>$user_id , 'type' =>$type )	 
			 );
		 }
		 else
		 {
			 $fields = array(
			 'to' => $device_token,
			 'priority' => 'high',
			 'notification' => array('title' => $title, 'body' => $body,'sound' => $sound,'userid'=>$user_id , 'type' =>$type )	 
			 );
		 }
		 
		 $headers = array(
		 'Authorization:key=AAAAbXFqwDM:APA91bFOsMYXsEQ77htPl_9lST54Um4j2Kksst4IW5u-x2V4vbeX_EO5zG41PARgy5ctCxwGLXLCpqwle28Y0cVLkltHGudzWH-5YgMJFCulK801VTZ_wkgR6UD9LrCgFzvs1TwKQy2e',
		 'Content-Type:application/json'
		 );
		 $ch = curl_init(); 
		 curl_setopt($ch, CURLOPT_URL, $path_to_firebase_cm);
		 curl_setopt($ch, CURLOPT_POST, true);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		 curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		 $result = curl_exec($ch);
		 curl_close($ch); 

		 $data = json_encode($fields);

	    $current_datetime = date("Y-m-d h:i:s");
	    $qry_insert_notification=$con->query("INSERT INTO `tbl_notification`(`from_user_id`,`to_user_id`, `message`,`type`, `created_at`) VALUES (".$user_id.",'".$to_user_id."','".$data."','".$noti_t."','".$current_datetime."')");
		return $result;
	}
}

function check_friends_or_not($con,$user_id,$other_user_id)
{
	$sel = $con->query("SELECT * FROM tbl_friend WHERE (user_id = '".$user_id."' AND other_user_id = '".$other_user_id."' OR user_id = '".$other_user_id."' AND other_user_id = '".$user_id."')");
	if($sel->num_rows > 0)
	{
		return 1;
	}
	else
	{
		$sell = $con->query("SELECT * FROM tbl_request WHERE (from_user_id = '".$user_id."' AND to_user_id = '".$other_user_id."' OR from_user_id = '".$other_user_id."' AND to_user_id = '".$user_id."') AND status IN(0,1)");
		if($sell->num_rows > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}

/*---------------------------------------------------------LevelUp-------------------------------------*/
function getweeklevelupdata($con,$user_id,$goal_id,$type,$reg_date)
{
	$stop_date = date('Y-m-d 00:00:01', strtotime(date('Y-m-d H:i:s') . ' +1 day'));
	$start_date = date('Y-m-d 00:00:01', strtotime($stop_date.' - 7 days'));
	$end_date = date('Y-m-d 23:59:00');

	$sel = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$start_date."' AND '".$end_date."'");
	$result = $sel->fetch_assoc();

	$wo['total'] = $result['total']!=''?$result['total']:'0';

	//get goal creation differnce in days
	$user_goal = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$user_id."' AND goal_id='".$goal_id."'");
	if($user_goal->num_rows > 0)
	{
		$goal_detail = $user_goal->fetch_assoc();
		$now = time(); // or your date as well
		$your_date = strtotime($goal_detail['created_at']);
		$datediff = $now - $your_date;

		$dayy = round($datediff / (60 * 60 * 24));
		$wo['create_day'] = (string)$dayy;
	}
	else{
		$wo['create_day'] = '0';
	}

	if($type == 'detail')
	{
		//check percentage by previous data
		$previous_start_date = date('Y-m-d H:i:s', strtotime($start_date.'- 7 days'));
		$previous_end_date =  date('Y-m-d 23:59:00',strtotime($start_date.'- 1 days'));

		$pre = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$previous_start_date."' AND '".$previous_end_date."'");
		$pre_result = $pre->fetch_assoc();

		$this_m = $result['total'];
		$previous_m = $pre_result['total'];

		if($this_m >= $previous_m)
		{
			$diff = $this_m - $previous_m;
			if($diff == 0)
			{
				$diff = 1;
			}
			if($this_m == 0)
			{
				$this_m = 1;
			}
			$total_workout_percentage = $diff * 100 /  $this_m;
			$total_workout_prefix = 'up';
			$total_workout_percentage = explode('.', $total_workout_percentage);
			if($total_workout_percentage[0] > 100)
			{
				$total_workout_percentage[0] = '100';
			}
		}
		else
		{
			$diff = $previous_m - $this_m;
			if($diff == 0)
			{
				$diff = 1;
			}
			if($this_m == 0)
			{
				$this_m = 1;
			}
			$total_workout_percentage = $diff * 100 /  $this_m;
			$total_workout_prefix = 'down';
			$total_workout_percentage = explode('.', $total_workout_percentage);
			if($total_workout_percentage[0] > 100)
			{
				$total_workout_percentage[0] = '100';
			}
		}

		$twoweeks = strtotime($reg_date);
		$fdate = strtotime("+ 2 weeks", $twoweeks);
		$future_date = date('Y-m-d H:i:s', $fdate);
		$cur_date = date("Y-m-d H:i:s");

		if(strtotime($cur_date) >= strtotime($future_date))
		{
			$wo['is_show_per'] = true;
		}
		else
		{
			$wo['is_show_per'] = false;
		}

		//get trophies value
		$user = $con->query("SELECT * FROM tbl_user WHERE id='".$user_id."'");
		$user_data = $user->fetch_assoc();

		$registered_date = $user_data['created_at'];
		$cur_date = date('Y-m-d H:i:s');
		$old_count = array();
		for($i = 7;$i <= 5000;$i= $i+7)
		{
			if($i == 7)
			{
				$start_check_date = $registered_date;
			}
			else{
				$start_check_date = $end_check_date;
			}
			$end_check_date = date('Y-m-d H:i:s', strtotime($registered_date . ' +'.$i.' day'));
			$sell = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$start_check_date."' AND '".$end_check_date."'");
			$resultt = $sell->fetch_assoc();

			$old_count[] = $resultt['total'];
			if(strtotime($end_check_date) >= strtotime($cur_date))
			{
				break;
			}
			//echo $i.PHP_EOL;
		}
		$wo['trophy'] = max($old_count);
		$wo['total_percentage'] = (string)$total_workout_percentage[0];
		$wo['total_prefix'] = (string)$total_workout_prefix;
	}
	return $wo;

}

function getmonthlevelupdata($con,$user_id,$goal_id,$type,$reg_date)
{
	$start_date = date('Y-m-d H:i:s', strtotime('today - 30 days'));
	$end_date = date('Y-m-d 23:59:00');

	$sel = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$start_date."' AND '".$end_date."'");
	$result = $sel->fetch_assoc();

	$wo['total'] = $result['total']!=''?$result['total']:'0';

	//get goal creation differnce in days
	$user_goal = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$user_id."' AND goal_id='".$goal_id."'");
	if($user_goal->num_rows > 0)
	{
		$goal_detail = $user_goal->fetch_assoc();
		$now = time(); // or your date as well
		$your_date = strtotime($goal_detail['created_at']);
		$datediff = $now - $your_date;

		$dayy = round($datediff / (60 * 60 * 24));
		$wo['create_day'] = (string)$dayy;
	}
	else{
		$wo['create_day'] = '0';
	}

	if($type == 'detail')
	{
		//check percentage by previous data
		$previous_start_date = date('Y-m-d H:i:s', strtotime($start_date.'- 30 days'));
		$previous_end_date =  date('Y-m-d 23:59:00',strtotime($start_date.'- 1 days'));

		$pre = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$previous_start_date."' AND '".$previous_end_date."'");
		$pre_result = $pre->fetch_assoc();

		$this_m = $result['total'];
		$previous_m = $pre_result['total'];

		if($this_m >= $previous_m)
		{
			$diff = $this_m - $previous_m;
			if($diff == 0)
			{
				$diff = 1;
			}
			if($this_m == 0)
			{
				$this_m = 1;
			}
			$total_workout_percentage = $diff * 100 /  $this_m;
			$total_workout_prefix = 'up';
			$total_workout_percentage = explode('.', $total_workout_percentage);
			if($total_workout_percentage[0] > 100)
			{
				$total_workout_percentage[0] = '100';
			}
		}
		else
		{
			$diff = $previous_m - $this_m;
			if($diff == 0)
			{
				$diff = 1;
			}
			if($this_m == 0)
			{
				$this_m = 1;
			}
			$total_workout_percentage = $diff * 100 /  $this_m;
			$total_workout_prefix = 'down';
			$total_workout_percentage = explode('.', $total_workout_percentage);
			if($total_workout_percentage[0] > 100)
			{
				$total_workout_percentage[0] = '100';
			}
		}

		$twoweeks = strtotime($reg_date);
		$fdate = strtotime("+2 months", $twoweeks);
		$future_date = date('Y-m-d H:i:s', $fdate);
		$cur_date = date("Y-m-d H:i:s");
		if(strtotime($cur_date) >= strtotime($future_date))
		{
			$wo['is_show_per'] = true;
		}
		else
		{
			$wo['is_show_per'] = false;
		}

		$old_count = array();
		for($i = 30;$i <= 5000;$i= $i+7)
		{
			if($i == 30)
			{
				$start_check_date = $registered_date;
			}
			else{
				$start_check_date = $end_check_date;
			}
			$end_check_date = date('Y-m-d H:i:s', strtotime($registered_date . ' +'.$i.' day'));
			$sell = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$start_check_date."' AND '".$end_check_date."'");
			$resultt = $sell->fetch_assoc();

			$old_count[] = $resultt['total'];
			if(strtotime($end_check_date) >= strtotime($cur_date))
			{
				break;
			}
			//echo $i.PHP_EOL;
		}
		$wo['trophy'] = max($old_count);
		$wo['total_percentage'] = (string)$total_workout_percentage[0];
		$wo['total_prefix'] = (string)$total_workout_prefix;
	}
	return $wo;

}

function getyearlevelupdata($con,$user_id,$goal_id,$type,$reg_date)
{
	$start_date = date('Y-m-d H:i:s', strtotime('today - 365 days'));
	$end_date = date('Y-m-d 23:59:00');

	$sel = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$start_date."' AND '".$end_date."'");
	$result = $sel->fetch_assoc();

	$wo['total'] = $result['total']!=''?$result['total']:'0';

	//get goal creation differnce in days
	$user_goal = $con->query("SELECT * FROM tbl_user_goal WHERE user_id='".$user_id."' AND goal_id='".$goal_id."'");
	if($user_goal->num_rows > 0)
	{
		$goal_detail = $user_goal->fetch_assoc();
		$now = time(); // or your date as well
		$your_date = strtotime($goal_detail['created_at']);
		$datediff = $now - $your_date;

		$dayy = round($datediff / (60 * 60 * 24));
		$wo['create_day'] = (string)$dayy;
	}
	else{
		$wo['create_day'] = '0';
	}

	if($type == 'detail')
	{
		//check percentage by previous data
		$previous_start_date = date('Y-m-d H:i:s', strtotime($start_date.'- 365 days'));
		$previous_end_date =  date('Y-m-d 23:59:00',strtotime($start_date.'- 1 days'));

		$pre = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$previous_start_date."' AND '".$previous_end_date."'");
		$pre_result = $pre->fetch_assoc();

		$this_m = $result['total'];
		$previous_m = $pre_result['total'];

		if($this_m >= $previous_m)
		{
			$diff = $this_m - $previous_m;
			if($diff == 0)
			{
				$diff = 1;
			}
			if($this_m == 0)
			{
				$this_m = 1;
			}
			$total_workout_percentage = $diff * 100 /  $this_m;
			$total_workout_prefix = 'up';
			$total_workout_percentage = explode('.', $total_workout_percentage);
			if($total_workout_percentage[0] > 100)
			{
				$total_workout_percentage[0] = '100';
			}
		}
		else
		{
			$diff = $previous_m - $this_m;
			if($diff == 0)
			{
				$diff = 1;
			}
			if($this_m == 0)
			{
				$this_m = 1;
			}
			$total_workout_percentage = $diff * 100 /  $this_m;
			$total_workout_prefix = 'down';
			$total_workout_percentage = explode('.', $total_workout_percentage);
			if($total_workout_percentage[0] > 100)
			{
				$total_workout_percentage[0] = '100';
			}
		}

		$twoweeks = strtotime($reg_date);
		$fdate = strtotime("+2 years", $twoweeks);
		$future_date = date('Y-m-d H:i:s', $fdate);
		$cur_date = date("Y-m-d H:i:s");

		if(strtotime($cur_date) >= strtotime($future_date))
		{
			$wo['is_show_per'] = true;
		}
		else
		{
			$wo['is_show_per'] = false;
		}

		$old_count = array();
		for($i = 365;$i <= 5000;$i= $i+7)
		{
			if($i == 365)
			{
				$start_check_date = $registered_date;
			}
			else{
				$start_check_date = $end_check_date;
			}
			$end_check_date = date('Y-m-d H:i:s', strtotime($registered_date . ' +'.$i.' day'));
			$sell = $con->query("SELECT COUNT(`id`) as total FROM tbl_exercise WHERE user_id='".$user_id."' AND goal_id='".$goal_id."' AND created_at BETWEEN '".$start_check_date."' AND '".$end_check_date."'");
			$resultt = $sell->fetch_assoc();

			$old_count[] = $resultt['total'];
			if(strtotime($end_check_date) >= strtotime($cur_date))
			{
				break;
			}
			//echo $i.PHP_EOL;
		}
		$wo['trophy'] = max($old_count);
		$wo['total_percentage'] = (string)$total_workout_percentage[0];
		$wo['total_prefix'] = (string)$total_workout_prefix;
	}
	return $wo;
}

/*function secondsToTime($seconds) {
	$dtF = new \DateTime('@0');
	$dtT = new \DateTime("@$seconds");
	return $dtF->diff($dtT)->format('%a:%h:%i:%s');
}*/

function secondsToTime($inputSeconds) {

	$secondsInAMinute = 60;
	$secondsInAnHour  = 60 * $secondsInAMinute;
	$secondsInADay    = 24 * $secondsInAnHour;

	// extract days
	$days = floor($inputSeconds / $secondsInADay);

	// extract hours
	$hourSeconds = $inputSeconds % $secondsInADay;
	$hours = floor($hourSeconds / $secondsInAnHour);

	// extract minutes
	$minuteSeconds = $hourSeconds % $secondsInAnHour;
	$minutes = floor($minuteSeconds / $secondsInAMinute);

	// extract the remaining seconds
	$remainingSeconds = $minuteSeconds % $secondsInAMinute;
	$seconds = ceil($remainingSeconds);

	$days = strlen($days) == 1?'0'.$days:$days;
	$hours = strlen($hours) == 1?'0'.$hours:$hours;
	$minutes = strlen($minutes) == 1?'0'.$minutes:$minutes;
	$seconds = strlen($seconds) == 1?'0'.$seconds:$seconds;
	return  $days.':'.$hours.':'.$minutes.':'.$seconds;
}

function convertSecToTime($sec){
	$date1 = new DateTime("@0"); //starting seconds
	$date2 = new DateTime("@$sec"); // ending seconds
	$interval =  date_diff($date1, $date2); //the time difference
	return $interval->format('%y Years, %m months, %d days, %h hours, %i minutes and %s seconds'); // convert into Years, Months, Days, Hours, Minutes and Seconds
}
?>
