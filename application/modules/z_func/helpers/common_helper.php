<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('get_api_sig'))
{
    function get_api_sig(){
		// apiKey generator
		$apiKey = "apikey";
		$secretKey = "secretkey";

		// Generates a random string of ten digits
		$salt = mt_rand();

		// Computes the signature by hashing the salt with the secret key as the key
		$signature = hash_hmac('sha256', $salt, $secretKey, true);

		// base64 encode...
		$encodedSignature = base64_encode($signature);

		// urlencode...
		$encodedSignature = urlencode($encodedSignature);

		return $encodedSignature;
    }
}

// NOTIFICATION
if ( ! function_exists('set_email_notif'))
{
    function set_email_notif($params = array())
    {
		$ci = get_instance();
		
		if ( !is_array($params) )
			return FALSE;
			
		$data['email']	 = $params['email'];
		$data['subject'] = $params['subject'];
		$data['message'] = $params['message'];
		$data['status']  = 'created';
		$data['created'] = date('Y-m-d H:i:s');
		
		$ci->db->insert('notification_email', $data);
        return TRUE;
	}
}

// FILE SIZE
if ( ! function_exists('formatSizeUnits'))
{
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
	}
}

// TRUNCATE FILE NAME
if ( ! function_exists('truncateFilename'))
{
	function truncateFilename($filename, $max = 30) {
		if (strlen($filename) <= $max) {
			return $filename;
		}
		if ($max <= 3) {
			return '...';
		}
		if (!preg_match('/^(.+?)(\.[^\.]+)?$/', $filename, $match)) {
			// has newlines or is an empty string
			return $filename;
		}
		list (, $name, $ext) = $match;
		$extLen = strlen($ext);
		$nameMax = $max - ($extLen == 0 ? 3 : $extLen + 2); // 2 for two dots of the elipses
		if ($nameMax <= 1) {
			$truncated = substr($filename, 0, $max - 3) . '...';
		}
		else {
			$truncated = substr($name, 0, $nameMax) . '...' . substr($ext, 1);
		}
		return $truncated;
	}
}

// TIME ELAPSED STRING
// LOGIC #1
if ( ! function_exists('time_elapsed_string'))
{
	function time_elapsed_string($ptime)
	{
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

// function test($ts){
    // echo time_passed($ts) . '<br />';
// } 
// test(time());
// test(time()-33);
// test(time()-(60*17));
// test(time()-((60*60*2) + (60*55)));
// test(time()-(60*60*3+60));
// test(strtotime('-1 day'));
// test(strtotime('-13 days'));
// test(strtotime('-25 days'));
// test(strtotime('July 23 2009'));
// test(strtotime('November 18 1999'));
// test(strtotime('March 1 1937'));

// MAIL
if ( ! function_exists('send_mail'))
{
	function send_mail( $email=NULL, $subject=NULL, $message=NULL ) {
		$ci = get_instance();
		
		$ci->load->library('email');

		$ci->email->clear();
		
		$ci->email->set_newline("\r\n");
		$ci->email->from('genesys0681@gmail.com', 'G.ENE.SYS');
		$ci->email->to($email); 
		// $ci->email->bcc('hertanto@fajarbenua.co.id');

		$ci->email->subject($subject);
		$ci->email->message($message);	

		return $ci->email->send();
	}
}
	
// OTHERS
if ( ! function_exists('set_statistics'))
{
	function set_statistics( $module, $action ) {
		$ci = get_instance();
		
		$data['company_id'] 	= $ci->session->userdata('company_id');
		$data['branch_id'] 		= $ci->session->userdata('branch_id');
		$data['department_id'] 	= $ci->session->userdata('department_id');
		$data['[module]'] 		= $module;
		$data['[action]'] 		= $action;
		$data['[action_date]'] 	= date('Y-m-d H:i:s');
		$ci->db->insert('[statistics]', $data);
	}
}
	
if ( ! function_exists('set_comet'))
{
	function set_comet( $param1, $param2=NULL ) {
		$ci = get_instance();
		
		$data['param1'] = $param1;
		$data['param2'] = $param2;
		$ci->db->insert('comet', $data);
	}
}
	
if ( ! function_exists('get_comet'))
{
	function get_comet() {
		$ci = get_instance();
		
		$row = $ci->db->order_by('id', 'asc')->get('comet')->row_array();
		if ($row) {
			// sleep for 5 seconds
			sleep(5);
			$ci->db->delete( 'comet', array('id'=>$row['id']) );
			return $row;
		}
	}
}
	
if ( ! function_exists('getCompany_Branch'))
{
	function getCompany_Branch($company_id=NULL, $branch_id=NULL) {
		$ci = get_instance();
		
		$ci->load->model('systems/systems_model');
		$company_id = empty($company_id) ? $ci->session->userdata('company_id') : $company_id;
		$branch_id = empty($branch_id) ? $ci->session->userdata('branch_id') : $branch_id;
		return empty( $result = $ci->systems_model->getCompany_Branch($company_id, $branch_id) ) ? '' : $result;
	}
}

if ( ! function_exists('getCompany_Branch_Bank'))
{
	function getCompany_Branch_Bank($company_id=NULL, $branch_id=NULL) {
		$ci = get_instance();
		
		$ci->load->model('systems/systems_model');
		$company_id = empty($company_id) ? $ci->session->userdata('company_id') : $company_id;
		$branch_id = empty($branch_id) ? $ci->session->userdata('branch_id') : $branch_id;
		return empty( $result = $ci->systems_model->getCompany_Branch($company_id, $branch_id) ) ? '' : $result->bank;
	}
}

// EXPORT TO EXCELL ===============
if ( ! function_exists('export_to_xls'))
{
	function export_to_xls($qry, $filename) {
		$ci = get_instance();
		
		$ci->load->library('Excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
 
		$objPHPExcel->setActiveSheetIndex(0);
		// Field names in the first row
		$fields = $qry->list_fields();
		$col = 0;
		foreach ($fields as $field) {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $field);
			$col++;
		}
		
		// Fetching the table data
		$row = 2;
		foreach($qry->result() as $data) {
			$col = 0;
			foreach ($fields as $field) {
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);
				$col++;
			}
			$row++;
		}
 
		// ================ AUTO SIZE ==================
		$columns = array('A');
		$current = 'A';
		while ($current != 'AZ') {
			$columns[] = ++$current;
		}
		foreach($columns as $column) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
		}
		// ================ AUTO SIZE ==================
		
		// Sending headers to force the user to download the file
		header('Set-Cookie: fileDownload=true; path=/');
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;filename='$filename.xls'");
		header("Cache-Control: max-age=0");
		// setcookie("fileDownload", "true", time() - 3600, "/");
		// setcookie("fileDownload", "true", time() - 3600);
		
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
	}
}

// EXPORT TO PDF ===============
if ( ! function_exists('export_to_pdf'))
{
	function export_to_pdf($qry, $filename, $paper_size='A4', $is_portrait=TRUE) {
		$ci = get_instance();
		
		$company	= $ci->systems_model->getCompany_ById(sesCompany()->id);
		
		$ci->load->library('mpdf');
		//=====================================================================================================\\
		
		if ($paper_size == 'F4') 
			if ($is_portrait)
				$paper_setup = array(215.9,330.2);
			else
				$paper_setup = array(330.2,215.9);
		
		if ($paper_size == 'A3') 
			if ($is_portrait)
				$paper_setup = array(297,420);
			else
				$paper_setup = array(420,297);
		
		$mpdf = new mPDF( 'utf-8', $paper_setup,'','',15,15,35,16,10,10 ); 
		$mpdf->SetTitle("Example");
		$mpdf->SetAuthor("Example");
		$logo_path = base_url()."assets/images/logo-$company->code.png";
		
		$title = join(" ", explode("_", strtoupper($filename)));
		$html_head = "<html><head>
		<style>
		.logo 	{ float: left; margin-top: -80px; width: 100px; height: 100px; }
		body  	{ font-family: Courier; font-size: 10pt; }
		td 		{ vertical-align: top; }
		.top-border 	{ border-top: 0.1mm solid #000000; }
		.bottom-border 	{ border-bottom: 0.1mm solid #000000; }
		.left-border 	{ border-left: 0.1mm solid #000000; }
		.right-border 	{ border-right: 0.1mm solid #000000; }
		table thead td { 
			text-align: center;
			border: 0.1mm solid #000000;
			border-collapse: collapse;
		}
		.items td {
			border-left: 0.1mm solid #000000;
			border-right: 0.1mm solid #000000;
		}
		.items td.blanktotal {
			background-color: #FFFFFF;
			border: 0mm none #000000;
			border-top: 0.1mm solid #000000;
			/* border-right: 0.1mm solid #000000; */
		}		
		.items td.totals {
			text-align: right;
			border: 0.1mm solid #000000;
		}
		</style>
		</head>
		<body>
		
		<!--mpdf
		<htmlpageheader name='myheader'>
			<div class='logo'><img src='$logo_path' width='100' /></div>
			<table width='100%'>
				<tr><td><center><h1>$company->name</h1></center></td></tr>
				<tr><td><center>|||</center></td></tr>
				<tr><td><center><h3>$title</h3></center></td></tr>
			</table>
		</htmlpageheader>

		<sethtmlpageheader name='myheader' value='on' show-this-page='1' />
		mpdf-->";
		$mpdf->WriteHTML($html_head);
		$mpdf->SetFooter("|Page {PAGENO} of {nb}|Printed @ ". date('d M Y H:i'));
		
		$header = "
		<table class='items' width='100%' style='margin-top: 1.25em; border-collapse: collapse;' cellpadding='8'>
		<thead>
			<tr>
				<td><strong>NO.</strong></td>";
			
		$fields = $qry->list_fields();
		$fields_count = count($fields);
		foreach ($fields as $field) {
			$header .= "<td><strong>$field</strong></td>";
		}
				
		$header .= "</tr>
		</thead>
		<tbody>";
		$mpdf->WriteHTML($header);

		if ($qry->num_rows() < 1) 
			crud_error( l('report_no_data') );
		
		$num = 1;
		foreach ( $qry->result() as $row ) {
			
			$detail .= "
				<tr>
					<td align='right'>$num</td>
					";
					
			foreach ($fields as $field) {
				$detail .= "<td>".$row->$field."</td>";
			}
			
			/* foreach ($fields as $field) {
				$detail .= "<td style='white-space: nowrap;>".$row->$field."</td>";
			} */
			
			$detail .= "
				</tr>
			";
			$num++;
		}
		$mpdf->WriteHTML($detail);
		
		$fields_count = $fields_count+1;
		$footer = "
				<tr>
					<td colspan=".$fields_count." class='blanktotal'>&nbsp;</td>
				</tr>
				</tbody>
			</table>";
		$mpdf->WriteHTML($footer);
		
		$mpdf->WriteHTML("</body></html>");
		
		// Sending headers to force the user to download the file
		header('Set-Cookie: fileDownload=true; path=/');
		// setcookie("fileDownload", "true", time() - 3600, "/");
		// setcookie("fileDownload", "true", time() - 3600);
		
		// $mpdf->Output();
		$mpdf->Output($filename.'.pdf','D');
	}
}

// SESSION DATA ===================
if ( ! function_exists('sesUser'))
{
	function sesUser($more=TRUE) {
		$ci = get_instance();
		
		if ($more)
		{
			$ci->load->model('systems/systems_model');
			return $ci->systems_model->getUsers_ById($ci->session->userdata('user_id'));
		}
		return $ci->session->userdata('user_id');
	}
}

if ( ! function_exists('sesCompany'))
{
	function sesCompany($company_id=TRUE) {
		$ci = get_instance();
		
		if (!empty($company_id)) {
			if ($company_id===TRUE)
			{
				$ci->load->model('systems/systems_model');
				return $ci->systems_model->getCompany_ById($ci->session->userdata('company_id'));
			} elseif ($company_id) {
				$ci->load->model('systems/systems_model');
				return $ci->systems_model->getCompany_ById($company_id);
			}
		} else {
			return $ci->session->userdata('company_id');
		} 
		// return $ci->session->userdata('company_id');
	}
}

if ( ! function_exists('sesBranch'))
{
	function sesBranch($more=TRUE) {
		$ci = get_instance();
		
		if ($more)
		{
			$ci->load->model('systems/systems_model');
			return $ci->systems_model->getBranch_ById($ci->session->userdata('branch_id'));
		}
		return $ci->session->userdata('branch_id');
	}
}

if ( ! function_exists('sesDepartment'))
{
	function sesDepartment($more=TRUE) {
		$ci = get_instance();
		
		if ($more)
		{
			$ci->load->model('systems/systems_model');
			return $ci->systems_model->getDepartment_ById($ci->session->userdata('department_id'));
		}
		return $ci->session->userdata('department_id');
	}
}

if ( ! function_exists('title_case'))
{
	function title_case($string, $exceptions = array()) {
		$words = explode(" ", $string);
		$newwords = array();

		foreach ($words as $word)
		{
			if (!in_array($word, $exceptions)) {
				$word = strtolower($word);
				$word = ucfirst($word);
			}
			array_push($newwords, $word);

		}

		return ucfirst(join(" ", $newwords));
	}
}

// TERBILANG INDONESIAN & ENGLISH ===================
if ( ! function_exists('terbilang_ina'))
{
	function terbilang_ina($x)
	{
		$abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		if ($x < 12)
			$result = " " . $abil[$x];
		elseif ($x < 20)
			$result = terbilang_ina($x - 10) . "belas";
		elseif ($x < 100)
			$result = terbilang_ina($x / 10) . " puluh" . terbilang_ina($x % 10);
		elseif ($x < 200)
			$result = " seratus" . terbilang_ina($x - 100);
		elseif ($x < 1000)
			$result = terbilang_ina($x / 100) . " ratus" . terbilang_ina($x % 100);
		elseif ($x < 2000)
			$result = " seribu" . terbilang_ina($x - 1000);
		elseif ($x < 1000000)
			$result = terbilang_ina($x / 1000) . " ribu" . terbilang_ina($x % 1000);
		elseif ($x < 1000000000)
			$result = terbilang_ina($x / 1000000) . " juta" . terbilang_ina($x % 1000000);
		elseif ($x < 1000000000000)
			$result = terbilang_ina($x / 1000000000) . " miliar" . terbilang_ina($x % 1000000000);
			
		return ucwords($result);
	}	
}

if ( ! function_exists('terbilang_ina2'))
{
	function terbilang_ina2($x)
	{
		$decimal = ' koma ';
		$fraction = null;
		if (strpos($x, '.') !== false) {
			list($x, $fraction) = explode('.', $x);
		}
		$result = terbilang_ina($x);
		
		if (null !== $fraction && is_numeric($fraction)) {
			$result .= $decimal.terbilang_ina($fraction);
		}
		
		return ucwords($result);
	}	
}

if ( ! function_exists('terbilang_eng'))
{
	function terbilang_eng($number) {
		
		$hyphen      = '-';
		$conjunction = ' and ';
		$separator   = ', ';
		$negative    = 'negative ';
		$decimal     = ' point ';
		$dictionary  = array(
			0                   => 'zero',
			1                   => 'one',
			2                   => 'two',
			3                   => 'three',
			4                   => 'four',
			5                   => 'five',
			6                   => 'six',
			7                   => 'seven',
			8                   => 'eight',
			9                   => 'nine',
			10                  => 'ten',
			11                  => 'eleven',
			12                  => 'twelve',
			13                  => 'thirteen',
			14                  => 'fourteen',
			15                  => 'fifteen',
			16                  => 'sixteen',
			17                  => 'seventeen',
			18                  => 'eighteen',
			19                  => 'nineteen',
			20                  => 'twenty',
			30                  => 'thirty',
			40                  => 'fourty',
			50                  => 'fifty',
			60                  => 'sixty',
			70                  => 'seventy',
			80                  => 'eighty',
			90                  => 'ninety',
			100                 => 'hundred',
			1000                => 'thousand',
			1000000             => 'million',
			1000000000          => 'billion',
			1000000000000       => 'trillion',
			1000000000000000    => 'quadrillion',
			1000000000000000000 => 'quintillion'
		);
		
		if (!is_numeric($number)) {
			return false;
		}
		
		if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
			// overflow
			trigger_error(
				'terbilang_eng only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
				E_USER_WARNING
			);
			return false;
		}

		if ($number < 0) {
			return $negative . terbilang_eng(abs($number));
		}
		
		$string = $fraction = null;
		
		if (strpos($number, '.') !== false) {
		// if ( strpos($number, '.') !== false && empty(substr($number, -strpos($number, '.')+1)) ) {
			list($number, $fraction) = explode('.', $number);
		}
		
		switch (true) {
			case $number < 21:
				$string = $dictionary[$number];
				break;
			case $number < 100:
				$tens   = ((int) ($number / 10)) * 10;
				$units  = $number % 10;
				$string = $dictionary[$tens];
				if ($units) {
					$string .= $hyphen . $dictionary[$units];
				}
				break;
			case $number < 1000:
				$hundreds  = $number / 100;
				$remainder = $number % 100;
				$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
				if ($remainder) {
					$string .= $conjunction . terbilang_eng($remainder);
				}
				break;
			default:
				$baseUnit = pow(1000, floor(log($number, 1000)));
				$numBaseUnits = (int) ($number / $baseUnit);
				$remainder = $number % $baseUnit;
				$string = terbilang_eng($numBaseUnits) . ' ' . $dictionary[$baseUnit];
				if ($remainder) {
					$string .= $remainder < 100 ? $conjunction : $separator;
					$string .= terbilang_eng($remainder);
				}
				break;
		}
		
		if (null !== $fraction && is_numeric($fraction) && !empty((int)$fraction)) {
			$string .= $decimal;
			$words = array();
			foreach (str_split((string) $fraction) as $number) {
				$words[] = $dictionary[$number];
			}
			$string .= implode(' ', $words);
		}
		
		return $string;
	}
}

// GENERATE AUTO CODE ===============================
if ( ! function_exists('get_doc_code'))
{
	function get_doc_code( $company_id=NULL, $branch_id=NULL, $department_id=NULL, $date, $code, $custom_1=NULL, $custom_2=NULL, $custom_3=NULL ) {
		$ci = get_instance();
		
		$filter['code'] = $code;
		if ( !empty($company_id) ) $filter['company_id'] = $company_id;
		if ( !empty($branch_id) ) $filter['branch_id'] = $branch_id;
		if ( !empty($department_id) ) $filter['department_id'] = $department_id;
		$qry = $ci->db->get_where( 'setup_documents', $filter );
		if ($qry->num_rows() < 1) 
			return FALSE;
		
		// NEW METHOD (with back date support)
		$row = $qry->row();
		$qry2 = $ci->db->get_where( 'setup_documents_num', array("document_id"=>$row->id, "year"=>date("Y", strtotime($date))) );
		if ($qry2->num_rows() < 1) {
			$data1['document_id'] = $row->id;
			$data1['year']		  = empty($date) ? date('Y') : date("Y", strtotime($date));
			$data1['number']	  = 1;
			$ci->db->insert( 'setup_documents_num', $data1);
			
			$id_num = $ci->db->insert_id();
			$last_number = $data1['number'];
		} else {
			$row2 = $qry2->row();
			
			$id_num 	 = $row2->id;
			$last_number = $row2->number+1;
		}
		
		$prefix_code_length = $row->prefix_code_length;
		for ($i = 1; $i <= $prefix_code_length; $i++){
			if ($i==1) {
				if ( !empty($row->prefix_code1) )
					$newcode[$i] = get_doc_prefix($row->prefix_code1, $date, $row->number_digit, $last_number, $custom_1, $custom_2, $custom_3);
			} elseif ($i==2) {
				if ( !empty($row->prefix_code2) )
					$newcode[$i] = get_doc_prefix($row->prefix_code2, $date, $row->number_digit, $last_number, $custom_1, $custom_2, $custom_3);
			} elseif ($i==3) {
				if ( !empty($row->prefix_code3) )
					$newcode[$i] = get_doc_prefix($row->prefix_code3, $date, $row->number_digit, $last_number, $custom_1, $custom_2, $custom_3);
			} elseif ($i==4) {
				if ( !empty($row->prefix_code4) )
					$newcode[$i] = get_doc_prefix($row->prefix_code4, $date, $row->number_digit, $last_number, $custom_1, $custom_2, $custom_3);
			} elseif ($i==5) {
				if ( !empty($row->prefix_code5) )
					$newcode[$i] = get_doc_prefix($row->prefix_code5, $date, $row->number_digit, $last_number, $custom_1, $custom_2, $custom_3);
			} elseif ($i==6) {
				if ( !empty($row->prefix_code6) )
					$newcode[$i] = get_doc_prefix($row->prefix_code6, $date, $row->number_digit, $last_number, $custom_1, $custom_2, $custom_3);
			}
		}
		
		// UPDATE & SAVE LAST NUMBER
		$data3['number'] = $last_number;
		$ci->db->update( 'setup_documents_num', $data3, array("id"=>$id_num) );
		
		return implode($row->separator,$newcode);
	}
}

if ( ! function_exists('number_code'))
{
	function number_code($num, $len = 5) {
		
		for ($i = 1, $n = (string)$num; strlen($n) < $len; $i++)
			$n = '0'.$n;
			
		return $n;
	}
}

if ( ! function_exists('get_doc_prefix'))
{
	function get_doc_prefix( $prefix_code, $date=NULL, $number_digit=NULL, $number=NULL, $custom_1=NULL, $custom_2=NULL, $custom_3=NULL ) {
		if ($prefix_code=='YYYY') 
			return date("Y", strtotime($date));
		elseif ($prefix_code=='YY') 
			return substr(date("Y", strtotime($date)), -2);
		elseif ($prefix_code=='MM') 
			return date('m', strtotime($date));
		elseif ($prefix_code=='NUMBER') 
			return number_code($number, $number_digit);
		elseif ($prefix_code=='CUSTOM_1') 
			return $custom_1;
		elseif ($prefix_code=='CUSTOM_2') 
			return $custom_2;
		elseif ($prefix_code=='CUSTOM_3') 
			return $custom_3;
		else
			return $prefix_code;
	}
}

if ( ! function_exists('set_doc_last_number'))
{
	function set_doc_last_number( $company_id, $branch_id, $department_id, $code, $auto_code ) {
		$ci = get_instance();
		
		$data['company_id']    = $company_id;
		$data['branch_id'] 	   = $branch_id;
		$data['department_id'] = $department_id;
		$data['department_id'] = $department_id;
		$data['code'] 		   = $code;
		$qry = $ci->db->get_where( 'setup_documents', $data );
		if ($qry->num_rows() < 1) 
			return FALSE;
		
		$row = $qry->row();
		if ( empty($row->separator) ) {
			$start = 0;
			for ($i = 1; $i <= $row->prefix_code_length; $i++){
				if ($i==1) {
					$prefix_code = $row->prefix_code1;
				} elseif ($i==2) {
					$prefix_code = $row->prefix_code2;
				} elseif ($i==3) {
					$prefix_code = $row->prefix_code3;
				} elseif ($i==4) {
					$prefix_code = $row->prefix_code4;
				} elseif ($i==5) {
					$prefix_code = $row->prefix_code5;
				} elseif ($i==6) {
					$prefix_code = $row->prefix_code6;
				}
				
				if ($prefix_code=='YYYY') {
					$year = (int)substr($auto_code, $start, 4);
					$start += 4;
				} elseif ($prefix_code=='MM') {
					$start += 2;
				} elseif ($prefix_code=='NUMBER') {
					$num = (int)substr($auto_code, $start, $row->number_digit);
					$start += $row->number_digit;
				} else {
					$start += strlen($prefix_code);
				}
			}
		} else {
		
			$tmp = explode($row->separator, $auto_code);
			for ($i = 1, $a = 0; $a < $row->prefix_code_length; $i++, $a++){
				if ($i==1) {
					$prefix_code = $row->prefix_code1;
				} elseif ($i==2) {
					$prefix_code = $row->prefix_code2;
				} elseif ($i==3) {
					$prefix_code = $row->prefix_code3;
				} elseif ($i==4) {
					$prefix_code = $row->prefix_code4;
				} elseif ($i==5) {
					$prefix_code = $row->prefix_code5;
				} elseif ($i==6) {
					$prefix_code = $row->prefix_code6;
				}
				
				if ($prefix_code=='YYYY') {
					$year = (int)$tmp[$a];
				} elseif ($prefix_code=='MM') { 
					$month = (int)$tmp[$a];
				} elseif ($prefix_code=='NUMBER') {
					$num = (int)$tmp[$a];
				} else {
					$cod = $tmp[$a];
				}
			}
		}
		
		$data1['last_year'] = $year;
		$data1['last_number'] = $num;
		$ci->db->update( 'setup_documents', $data1, $data );
		
		return TRUE;
	}
}
// DATE & TIME ===========================
if ( ! function_exists('first_date'))
{
	function first_date($format=NULL, $y, $m) {
		
		if (empty($format)) 
			$format = 'Y-m-d';
			
		return date( $format, mktime(0, 0, 0, $m, 1, $y) );
	}
}

if ( ! function_exists('last_date'))
{
	function last_date($format=NULL, $y, $m) {
		
		if (empty($format)) 
			$format = 'Y-m-d';
			
		$d = cal_days_in_month(CAL_GREGORIAN, $m, $y);
		return date( $format, mktime(0, 0, 0, $m, $d, $y) );
	}
}

if ( ! function_exists('db_date_format'))
{
	function db_date_format($date=NULL)
	{
		if ( empty($date) )
			return NULL;
		
		// dd/mm/yyyy => yyyy-mm-dd
		// $tmp = explode('/', $date);
		// return $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
		
		// dd/mm/yyyy => yyyy-mm-dd
		list($tmp[2], $tmp[1], $tmp[0]) = explode('/', $date);
		return implode('-', $tmp);
	}
}

if ( ! function_exists('mk_date'))
{
	// FORMAT $date = 'Y-m-d'
	function mk_date($date) {
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

if ( ! function_exists('set_date'))
{
	function set_date($format=NULL, $date, $d=0,$m=0,$y=0) {
		
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

if ( ! function_exists('set_datetime'))
{
	function set_datetime($date,$d=0,$m=0,$y=0,$h=0,$i=0,$s=0) {

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

if ( ! function_exists('set_datetime_weekday'))
{
	function set_datetime_weekday($date,$d=0,$m=0,$y=0,$h=0,$i=0,$s=0) {

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

// USER ========================================
if ( ! function_exists('get_default_user_company'))
{
	function get_default_user_company($user_id=NULL) {
		$ci = get_instance();
		
		$ci->db->select('company_id');
		$ci->db->from('users_company');
		$ci->db->where('user_id', (empty($user_id) ? $this->session->userdata('user_id') : $user_id));
		$ci->db->order_by('user_id, id');
		return $ci->db->get()->row()->company_id;
	}
}

if ( ! function_exists('get_default_user_branch'))
{
	function get_default_user_branch($user_id=NULL) {
		$ci = get_instance();
		
		$ci->db->select('branch_id');
		$ci->db->from('users_branch');
		$ci->db->where('user_id', (empty($user_id) ? $this->session->userdata('user_id') : $user_id));
		$ci->db->order_by('user_id, id');
		return $ci->db->get()->row()->branch_id;
	}
}

if ( ! function_exists('get_default_user_department'))
{
	function get_default_user_department($user_id=NULL) {
		$ci = get_instance();
		
		$ci->db->select('department_id');
		$ci->db->from('users_department');
		$ci->db->where('user_id', (empty($user_id) ? $this->session->userdata('user_id') : $user_id));
		$ci->db->order_by('user_id, id');
		return $ci->db->get()->row()->department_id;
	}
}

// CRUD STATUS ======================================
if ( ! function_exists('crud_error'))
{
	function crud_error($err_message=NULL)
	{
		if ( is_array($err_message) )
			$arr1 = array("isError"=>true,"errorMsg"=>l('-') );
		else
			$arr1 = array("isError"=>true,"errorMsg"=>l($err_message) );
		$result = is_array($err_message) ? array_merge($arr1, $err_message) : $arr1;
		echo json_encode( $result );
		exit;
	}
}

if ( ! function_exists('crud_success'))
{
	function crud_success($arrMsg=NULL)
	{
		$arr1 	= array("success"=>1,"infoMsg"=>l('success_saving'));
		$result = is_array($arrMsg) ? array_merge($arr1, $arrMsg) : $arr1;
		echo json_encode( $result );
		exit;
	}
}

if ( ! function_exists('crud_result'))
{
	function crud_result($result)
	{
		echo json_encode( $result );
		exit;
	}
}

if ( ! function_exists('crud_result_html'))
{
	function crud_result_html($result)
	{
		echo $result;
		exit;
	}
}

// CURRENCY ====================================
if ( ! function_exists('getCurrencyRateById'))
{
	function getCurrencyById($id, $date=NULL) {
		$ci = get_instance();
		
		$params['table'] = 'currency';
		
		$date = empty($date) ? date('Y-m-d') : $date;
		
		$ci->db->select("c.*, 
			COALESCE((select rate from currency_rate as cr where cr.currency_id = c.id and date = '$date'),
			(select rate from currency_rate as cr where cr.currency_id = c.id order by id desc limit 1)) as rate", FALSE);
		$ci->db->from($params['table'].' as c');
		$ci->db->where('id', $id);
		return $ci->db->get()->row();
	
	}
}

if ( ! function_exists('get_currency'))
{
	function get_currency($id, $date=NULL) {
		$ci = get_instance();
		
		$params['table'] = 'currency';
		
		$date = empty($date) ? date('Y-m-d') : $date;
		
		$ci->db->select("c.*, 
			(select top 1 rate from currency_rate where currency_id = c.id and date <= '$date' order by date desc) as rate", FALSE);
		$ci->db->from($params['table'].' as c');
		$ci->db->where('id', $id);
		return $ci->db->get()->row();
	
	}
}

if ( ! function_exists('format_rupiah'))
{
	function format_rupiah($val, $precision = 0) {
		//1. cek apakah negatif?
		$n = '';
		if(strstr($val,"-")) { 
			$val = str_replace("-","",$val); 
			$n = "-"; 
		} 
		//2. cek apakah pecahan?
		$val = round((float) $val, (int) $precision);
		if (strpos($val, '.') !== false) {
			list($a, $b) = explode('.', $val); 
		} else {
			$a = $val;
			$b = '';
		}
		//3. format rupiah ! (cara pertama)
		$x = '';
		$i = strlen($a);
		while ($i > 3) {
			$x = "." . substr($a, -3) . $x;
			$a = substr($a, 0, strlen($a)-3);
			$i = strlen($a);
		}
		$a = $a . $x;
		
/* 		//3. format rupiah ! (cara kedua)
		for ($i=0, $j=1, $x=''; $i<strlen($a); $i++, $j++) {
			if (($j % 3) == 0) 
				$x = '.'.substr(strrev($a), $i,1).$x;
			else
				$x = substr(strrev($a), $i,1).$x;
		}
		if ((strlen($a) % 3) == 0)
			$x = substr($x, 1, strlen($x));
		$a = $x;
 */		
		//4. pembulatan
		if (strlen($b) < $precision) $b = str_pad($b, $precision, '0', STR_PAD_RIGHT); 
		
		return $precision ? "$n$a,$b" : "$n$a"; 
	}
}

if ( ! function_exists('seo_friendly'))
{
	function seo_friendly($realname) {

		$seoname = preg_replace('/\%/',' percentage',$realname); 
		$seoname = preg_replace('/\@/',' at ',$seoname); 
		$seoname = preg_replace('/\&/',' and ',$seoname); 
		$seoname = preg_replace('/\s[\s]+/','-',$seoname);    // Strip off multiple spaces 
		$seoname = preg_replace('/[\s\W]+/','-',$seoname);    // Strip off spaces and non-alpha-numeric 
		$seoname = preg_replace('/^[\-]+/','',$seoname); // Strip off the starting hyphens 
		$seoname = preg_replace('/[\-]+$/','',$seoname); // // Strip off the ending hyphens 
		$seoname = strtolower($seoname); 
		return $seoname;
	}
}

if ( ! function_exists('open_pdf'))
{
	function open_pdf($file) {
		
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename='.basename($file));
		//header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Length: ' . filesize($file));
		//@readfile($file);			
		readfile($file);			
	}
}

if ( ! function_exists('tempnam_sfx'))
{
   function tempnam_sfx($path, $suffix) 
   { 
      do 
      { 
         $file = $path."/".mt_rand().$suffix; 
         $fp = @fopen($file, 'x'); 
      } 
      while(!$fp); 

      fclose($fp); 
      return $file; 
   } 

   // call it like this: 
   //$file = tempnam_sfx("/tmp", ".jpg"); 
 }

if ( ! function_exists('is_allow'))
{
	function is_allow($crud=NULL, $mdl_grp=NULL, $mdl=NULL) {
		$ci = get_instance();
		
		$user_id = $ci->session->userdata('user_id');
		
		// {begin} cek module_group, apakah sudah ada?
		$qry = $ci->db->get_where( 'modules_groups', array('code'=>$mdl_grp) );
		if ( $qry->num_rows() < 1 ) {
			$ci->db->insert( 'modules_groups', array('code'=>strtoupper($mdl_grp), 'name'=>strtoupper($mdl_grp), 'active'=>1));
			$module_group_id = $ci->db->insert_id();
		} else {
			$module_group_id = $qry->row()->id;
		}
		
		// {begin} cek module, apakah sudah ada?
		$qry = $ci->db->get_where( 'modules', array('code'=>$mdl, 'module_group_id'=>$module_group_id) );
		if ( $qry->num_rows() < 1 ) {
			$ci->db->insert('modules', array('module_group_id'=>$module_group_id, 'code'=>strtoupper($mdl), 'name'=>strtoupper($mdl), 'active'=>1));
			$module_id = $ci->db->insert_id();
		} else {
			$module_id = $qry->row()->id;
		}
		
		$query = $ci->db->query("select group_id from users_groups where user_id = $user_id");
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$query2 = $ci->db->query("select * from groups_auth where group_id = $row->group_id and module_id = $module_id and $crud = 1");
				if ($query2->num_rows() > 0) 
					return TRUE;
			}
		}
		return FALSE;
	}
}

if ( ! function_exists('set_upload_folder'))
{
	function set_upload_folder( $filepath=NULL ) {
		$ci = get_instance();
		
		$user_id 		 = $ci->session->userdata('user_id');
		$module_group_id = $ci->db->get_where('modules_groups', array('name'=>$mdl_grp))->row()->id;
		
		// {begin} cek module, apakah sudah ada?
		$module_id 	= $ci->db->get_where('modules', array('code'=>$mdl))->row()->id;
		if ( empty($module_id) ) {
			$ci->db->insert('modules', array('module_group_id'=>$module_group_id, 'code'=>strtoupper($mdl), 'name'=>strtoupper($mdl), 'active'=>1));
			$module_id = $ci->db->insert_id();
		} // {end}
		
		$query = $ci->db->query("select group_id from users_groups where user_id = $user_id");
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$query2 = $ci->db->query("select * from groups_auth where group_id = $row->group_id and module_id = $module_id and $crud = 1");
				if ($query2->num_rows() > 0) 
					return TRUE;
			}
		}
		return FALSE;
	}
}

// DEBUGGING 
if ( ! function_exists('log_p'))
{
	function log_p($data='')
	{
		$ci =& get_instance();
		
		if ( is_array($data) ){
			var_dump($data);
		} elseif ( is_object($data) ){
			$ci->output->set_content_type('application/json');
			$ci->output->set_output(json_encode($data));
		} else {
			$ci->output->set_output($data);
		}
	}
}

if ( ! function_exists('l'))
{
	function l($langkey)
	{
		include('assets/languages/english.php');
		return empty($lang[$langkey]) ? "Undefined language: $langkey" : $lang[$langkey];
	}
}

if ( ! function_exists('getBranchAliasById'))
{
	function getBranchAliasById($id) {
		$ci = get_instance();
		
		$ci->db->select('alias');
		$ci->db->from('branch');
		$ci->db->where('id', $id);
		$qry = $ci->db->get();
		return ( $qry->num_rows() > 0) ? $qry->row()->alias : NULL;
	
	}
}

