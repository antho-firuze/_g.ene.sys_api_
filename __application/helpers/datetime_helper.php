<?php defined('BASEPATH') OR exit('No direct script access allowed');

// DATE & TIME ===========================
if ( ! function_exists('date_differ'))
{
	/* 
	*	ex: date_differ('2017-01-01', '2017-12-01'); 
	* 
	* @type = 'year' or 'month' or 'day' - Default: 'month'
	*/
	function date_differ($date1, $date2, $type='month') 
	{
		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		if ($type == 'month')
			$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
		if ($type == 'day')
			$diff = floor(($ts2 - $ts1) / 86400);
		return $diff;
	}
}

if ( ! function_exists('date_first'))
{
	function date_first($format=NULL, $y, $m) {
		
		if (empty($format)) 
			$format = 'Y-m-d';
			
		return date( $format, mktime(0, 0, 0, $m, 1, $y) );
	}
}

if ( ! function_exists('date_last'))
{
	function date_last($format=NULL, $y, $m) {
		
		if (empty($format)) 
			$format = 'Y-m-d';
			
		$d = cal_days_in_month(CAL_GREGORIAN, $m, $y);
		return date( $format, mktime(0, 0, 0, $m, $d, $y) );
	}
}

/* 
*	datetime 		: '22/03/2017' or '22/03/2017 07:07'
*	this_format	: 'dd/mm/yyyy', 'mm/dd/yyyy', 'dd-mm-yyyy', 'mm-dd-yyyy', 'dd/mm/yyyy hh:mm', 'mm/dd/yyyy hh:mm', 'dd-mm-yyyy hh:mm', 'mm-dd-yyyy hh:mm'
*	is_datetime	: TRUE/FALSE (With Time/Without Time)
*	
*	output_format : 'Y-m-d h:i:s' or 'Y-m-d'
*	
*/
if ( ! function_exists('datetime_db_format'))
{
	function datetime_db_format($datetime, $this_format, $is_datetime = TRUE)
	{
		if (empty($datetime))
			return FALSE;
		
		if (! in_array($this_format, ['yyyy-mm-dd', 'dd/mm/yyyy', 'mm/dd/yyyy', 'dd-mm-yyyy', 'mm-dd-yyyy', 'dd/mm/yyyy hh:mm', 'mm/dd/yyyy hh:mm', 'dd-mm-yyyy hh:mm', 'mm-dd-yyyy hh:mm']))
			return FALSE;
		
		/* seperate between date & time */
		$dt = [];
		$dt_format = [];
		$dt = explode(' ', $datetime);
		$dt_format = explode(' ', $this_format);

		$date = (count($dt) > 1) ? $dt[0] : $dt[0];
		$time = (count($dt) > 1) ? $dt[1].':00' : FALSE;
		$date_format = (count($dt_format) > 1) ? $dt_format[0] : $dt_format[0];
		$time_format = (count($dt_format) > 1) ? $dt_format[1] : FALSE;
		
		/* time */
		$time_result = ($time !== FALSE) ? $time : '00:00:00';
		
		/* date */
		if (strpos($date_format, '/') !== false) {
			list($f[0], $f[1], $f[2]) = explode('/', $date_format);
		} else {
			list($f[0], $f[1], $f[2]) = explode('-', $date_format);
		}
		if (strpos($date, '/') !== false) {
			list($d[0], $d[1], $d[2]) = explode('/', $date);
		} else {
			list($d[0], $d[1], $d[2]) = explode('-', $date);
		}
		$date_result = implode('-',[$d[array_search("yyyy",$f)], $d[array_search("mm",$f)], $d[array_search("dd",$f)]]);
		return $is_datetime ? implode(' ', [$date_result, $time_result]) : $date_result;
	}
}

if ( ! function_exists('date_mk'))
{
	// FORMAT $date = 'Y-m-d'
	function date_mk($date) {
		list($y, $m, $d) = explode('-', $date);
		return mktime(
				0,
				0,
				0,
				$m,
				$d, 
				$y
			);
	}
}

if ( ! function_exists('date_set'))
{
	function date_set($format=NULL, $date, $d=0,$m=0,$y=0) {
		
		if (empty($format)) 
			$format = 'Y-m-d';
			
		$date = strtotime($date);
		return date( $format, mktime(
				0,
				0,
				0,
				date('m',$date)+$m,
				date('d',$date)+$d, 
				date('Y',$date)+$y
			));
	}
}

if ( ! function_exists('datetime_set'))
{
	function datetime_set($date,$d=0,$m=0,$y=0,$h=0,$i=0,$s=0) {

		$cd = strtotime($date);
		return date(
			'Y-m-d h:i:s', 
			mktime(
				date('h',$cd)+$h, 
				date('i',$cd)+$i, 
				date('s',$cd)+$s, 
				date('m',$cd)+$m,
				date('d',$cd)+$d, 
				date('Y',$cd)+$y)
			);
	}
}

if ( ! function_exists('datetime_weekday'))
{
	function datetime_weekday($date,$d=0,$m=0,$y=0,$h=0,$i=0,$s=0) {

		$cd = strtotime($date);
		$new = date( 'Y-m-d h:i:s', mktime(
				date('h',$cd)+$h, 
				date('i',$cd)+$i, 
				date('s',$cd)+$s, 
				date('m',$cd)+$m,
				date('d',$cd)+$d, 
				date('Y',$cd)+$y)
			);
		if (date("N", strtotime($new))==6)
			return date( 'Y-m-d h:i:s', mktime(
				date('h',$cd)+$h, 
				date('i',$cd)+$i, 
				date('s',$cd)+$s, 
				date('m',$cd)+$m,
				date('d',$cd)+$d+2, 
				date('Y',$cd)+$y)
			);
		elseif (date("N", strtotime($new))==7)
			return date( 'Y-m-d h:i:s', mktime(
				date('h',$cd)+$h, 
				date('i',$cd)+$i, 
				date('s',$cd)+$s, 
				date('m',$cd)+$m,
				date('d',$cd)+$d+1, 
				date('Y',$cd)+$y)
			);
		else
			return $new;
	}
}

if ( ! function_exists('time_between_string'))
{
	function time_between_string($ttime, $ftime)
	{
		$etime = strtotime($ftime) - strtotime($ttime);

		if ($etime < 1)
		{
			return '0 seconds';
		}

		$a = array( 365 * 24 * 60 * 60  =>  'year',
					 30 * 24 * 60 * 60  =>  'month',
						  24 * 60 * 60  =>  'day',
							   60 * 60  =>  'hour',
									60  =>  'minute',
									 1  =>  'second'
					);
		$a_plural = array( 'year'   => 'years',
						   'month'  => 'months',
						   'day'    => 'days',
						   'hour'   => 'hours',
						   'minute' => 'minutes',
						   'second' => 'seconds'
					);

		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str);
			}
		}
	}
}

// TIME ELAPSED STRING
// LOGIC #2
if ( ! function_exists('nicetime'))
{
	function nicetime($date)
	{
		if(empty($date)) {
			return "No date provided";
		}
	 
		$periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths         = array("60","60","24","7","4.35","12","10");
	 
		$now             = time();
		$unix_date         = strtotime($date);
	 
		   // check validity of date
		if(empty($unix_date)) {    
			return "Bad date";
		}
	 
		// is it future date or past date
		if($now > $unix_date) {    
			$difference     = $now - $unix_date;
			$tense         = "ago";
	 
		} else {
			$difference     = $unix_date - $now;
			$tense         = "from now";
		}
	 
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
	 
		$difference = round($difference);
	 
		if($difference != 1) {
			$periods[$j].= "s";
		}
	 
		return "$difference $periods[$j] {$tense}";
	}
}
// $date = "2009-03-04 17:45";
// $result = nicetime($date); // 2 days ago
 

// TIME ELAPSED STRING
// LOGIC #3
if ( ! function_exists('time_passed'))
{
	function time_passed($timestamp){
		//type cast, current time, difference in timestamps
		$timestamp      = (int) $timestamp;
		$current_time   = time();
		$diff           = $current_time - $timestamp;
		
		//intervals in seconds
		$intervals      = array (
			'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60
		);
		
		//now we just find the difference
		if ($diff == 0)
		{
			return 'just now';
		}    

		if ($diff < 60)
		{
			return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
		}        

		if ($diff >= 60 && $diff < $intervals['hour'])
		{
			$diff = floor($diff/$intervals['minute']);
			return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
		}        

		if ($diff >= $intervals['hour'] && $diff < $intervals['day'])
		{
			$diff = floor($diff/$intervals['hour']);
			return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
		}    

		if ($diff >= $intervals['day'] && $diff < $intervals['week'])
		{
			$diff = floor($diff/$intervals['day']);
			return $diff == 1 ? $diff . ' day ago' : $diff . ' days ago';
		}    

		if ($diff >= $intervals['week'] && $diff < $intervals['month'])
		{
			$diff = floor($diff/$intervals['week']);
			return $diff == 1 ? $diff . ' week ago' : $diff . ' weeks ago';
		}    

		if ($diff >= $intervals['month'] && $diff < $intervals['year'])
		{
			$diff = floor($diff/$intervals['month']);
			return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
		}    

		if ($diff >= $intervals['year'])
		{
			$diff = floor($diff/$intervals['year']);
			return $diff == 1 ? $diff . ' year ago' : $diff . ' years ago';
		}
	}
}

// TIME ELAPSED STRING
// LOGIC #1
if ( ! function_exists('time_elapsed_string'))
{
	function time_elapsed_string($ptime)
	{
		$ptime = is_numeric($ptime) 
							? $ptime
							: strtotime($ptime);
							
		$etime = time() - $ptime;

		if ($etime < 1)
		{
			return '0 seconds';
		}

		$a = array( 365 * 24 * 60 * 60  =>  'year',
					 30 * 24 * 60 * 60  =>  'month',
						  24 * 60 * 60  =>  'day',
							   60 * 60  =>  'hour',
									60  =>  'minute',
									 1  =>  'second'
					);
		$a_plural = array( 'year'   => 'years',
						   'month'  => 'months',
						   'day'    => 'days',
						   'hour'   => 'hours',
						   'minute' => 'minutes',
						   'second' => 'seconds'
					);

		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
			}
		}
	}
}


/* For changing second to elapsed string
 *
 * Ex: 700 second => 11 minutes 6 seconds
 * 
 */
if ( ! function_exists('nicetime_lang'))
{
	function nicetime_lang($ptime, $lang = 'english')
	{
		$ptime = is_numeric($ptime) 
							? $ptime
							: strtotime($ptime);
							
		$etime = abs(time() - $ptime);

		$l = [
			'english' => [
				'year'	=> 'year',
				'month'	=> 'month',
				'day'		=> 'day',
				'hour'	=> 'hour',
				'minute'	=> 'minute',
				'second'	=> 'second',
			],
			'indonesia' => [
				'year'	=> 'tahun',
				'month'	=> 'bulan',
				'day'		=> 'hari',
				'hour'	=> 'jam',
				'minute'	=> 'menit',
				'second'	=> 'detik',
			],
		];
		
		if ($etime < 1)
			return '0' . ' ' . $l[$lang]['second'];

		$a = array( 365 * 24 * 60 * 60  =>  'year',
					 30 * 24 * 60 * 60  =>  'month',
						  24 * 60 * 60  =>  'day',
							   60 * 60  =>  'hour',
									60  =>  'minute',
									 1  =>  'second'
					);

		foreach ($a as $secs => $str)
		{
			$d = $etime / $secs;
			if ($d >= 1)
			{
				$r = round($d);
				return $r . ' ' . ($r > 1 && $lang != 'indonesia' ? $l[$lang][$str].'s' : $l[$lang][$str]);
			}
		}
	}
}