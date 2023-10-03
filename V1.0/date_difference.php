<?php
include("dbfunction.php");
echo "<pre>";
date_default_timezone_set('Asia/Kolkata');
$cur_date = date('Y-m-d H:i:s');
$work_date = '2021-01-10 10:56:00';
$time = '';

$yyyy = '00'; //year
$mm = '00'; //month
$ww = '00'; //week
$dd = '00'; //day
$h = '00'; //hour
$m = '00'; //minute
$s = '00'; //second

$date1 = strtotime($work_date);
$date2 = strtotime($cur_date);

// Formulate the Difference between two dates
$diff = abs($date2 - $date1);

// To get the year divide the resultant date into
// total seconds in a year (365*60*60*24)
$years = floor($diff / (365 * 60 * 60 * 24));
if ($years > 0) {
    $time .= $years.' year ';
    $yyyy = $years;
}

// To get the month, subtract it with years and
// divide the resultant date into
// total seconds in a month (30*60*60*24)
$months = floor(($diff - $years * 365 * 60 * 60 * 24)
    / (30 * 60 * 60 * 24));
if ($months > 0) {
    $time .= $months.' month ';
    $mm = $months;
}

// To get the day, subtract it with years and
// months and divide the resultant date into
// total seconds in a days (60*60*24)
$days = floor(($diff - $years * 365 * 60 * 60 * 24 -
        $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
if ($days > 0) {
    $time .= $days.' day ';
    $dd = $days;
    if($dd >= 7)
    {
        $ww = floor($days / 7);
        $dd = $days % 7;
    }

}

// To get the hour, subtract it with years,
// months & seconds and divide the resultant
// date into total seconds in a hours (60*60)
$hours = floor(($diff - $years * 365 * 60 * 60 * 24
        - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24)
    / (60 * 60));
if ($hours > 0) {
    $time .= $hours.' hours ';
    $h = $hours;
}

// To get the minutes, subtract it with years,
// months, seconds and hours and divide the
// resultant date into total seconds i.e. 60
$minutes = floor(($diff - $years * 365 * 60 * 60 * 24
        - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
        - $hours * 60 * 60) / 60);
if ($minutes > 0) {
    $time .= $minutes.' minutes ';
    $m = $minutes;
}
$min = $minutes;
// To get the minutes, subtract it with years,
// months, seconds, hours and minutes
$seconds = floor(($diff - $years * 365 * 60 * 60 * 24
    - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24
    - $hours * 60 * 60 - $minutes * 60));
$s = $seconds;

$minutes = $days * 24 * 60;
$minutes += $hours * 60;
$minutes += $min;

$hours = floor($minutes / 60);
$min = $minutes - ($hours * 60);

$hours = strlen($hours) == 1?'0'.$hours:$hours;
$min = strlen($min) == 1?'0'.$min:$min;
$seconds = strlen($seconds) == 1?'0'.$seconds:$seconds;

$yyyy = strlen($yyyy) == 1?'0'.$yyyy:$yyyy;
$mm = strlen($mm) == 1?'0'.$mm:$mm;
$ww = strlen($ww) == 1?'0'.$ww:$ww;
$dd = strlen($dd) == 1?'0'.$dd:$dd;
$h = strlen($h) == 1?'0'.$h:$h;
$m = strlen($m) == 1?'0'.$m:$m;
$s = strlen($s) == 1?'0'.$s:$s;
$ss = array();
if($yyyy != '00')
{
    $ss[] = $yyyy == '01'?$yyyy.'year':$yyyy.'years ';
}
if($mm != '00')
{
    $ss[] = $mm == '01'?$mm.'month':$mm.'months ';
}

if($ww != '00')
{
    $ss[] = $ww == '01'?$ww.'week':$ww.'weeks ';
}

if($dd != '00')
{
    $ss[] = $dd == '01'?$dd.'day':$dd.'days ';
}

if($h != '00')
{
    $ss[] = $h == '01'?$h.'hour':$h.'hours ';
}
if($m != '00')
{
    $ss[] = $m == '01'?$m.'minute':$m.'minutes ';
}
if($s != '00')
{
    $ss[] = $s == '01'?$s.'second':$s.'seconds ';
}

if(!empty($ss))
{
    $s1 = isset($ss[0])&&$ss[0]!=''?$ss[0]:'';
    $s2 = isset($ss[1])&&$ss[1]!=''?$ss[1]:'';
    $goal['last_time_sentance'] = $s1.$s2;
}
else{
    $goal['last_time_sentance'] = '';
}
print_r($goal);

/*if($yyyy != '00' && $mm != '00')
{
    $ss = $yyyy.'y '.$mm.'mon';
}
elseif($mm != '00' && $ww != '00')
{
    $ss = $mm.'mon '.$ww.'week';
}
elseif($ww != '00' && $dd != '00')
{
    $ss = $ww.'week '.$dd.'day';
}
elseif($dd != '00' && $h != '00')
{
    $ss = $dd.'day '.$h.'h';
}
elseif($h == 0 && $m == 0)
{
    $ss = '0min '.$s.'sec';
}
elseif($h == 0 && $m > 0)
{
    $ss = $m.'min '.$s.'sec';
}
else{
    $ss = $h.'h '.$m.'min';
}*/
//echo $ss; die;

$t = $yyyy.':'.$mm.':'.$ww.':'.$dd.':'.$h.':'.$m.':'.$s;
/*if($hours >= 24)
{
    $time_seconds = $hours * 3600 + $min * 60 + $seconds;
    $t = convertSecToTime($time_seconds);
}
else{
    $t = '00:00:00:00:'.$hours.':'.$min.':'.$seconds;
}*/
echo $t;