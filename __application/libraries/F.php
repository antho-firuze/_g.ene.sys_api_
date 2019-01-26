<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * F Class
 *
 * This class contain various functions 
 *
 */
class F {

	function __construct()
	{
	}
	
	/**
	 * Shortcut for get language line key and with additional parameters
	 *
	 */
	function lang($key, $params = NULL)
	{
		$ci = &get_instance();

		if (is_array($params)){
			if (F::is_array_assoc($params))
				return F::sprintfx($ci->lang->line($key), $params);
			else
				return vsprintf($ci->lang->line($key), $params);
		}
		else
			return sprintf($ci->lang->line($key), $params);
	}
	
	/**
	 * For checking array is associative or sequential?
	 *
	 */
	function is_array_assoc(array $arr)
	{
		if (array() === $arr) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/* 
	 *	Extend for internal php "sprintf" function
	 *	Example:
	 *
	 * 			echo sprintfx( 
	 *					'Hello {your_name}, my name is {my_name}! I am {my_age}, how old are you? I like {object} and I want to {objective_in_life}!'
   *          , array( 'your_name'         => 'Matt'
   *                   'my_name'           => 'Jim'
   *                   'my_age'            => 'old'
   *                   'object'            => 'women'
   *                   'objective_in_life' => 'write code'
   *                 )
   *          ); 
	 */
	function sprintfx($str, $vars)
	{
		foreach ($vars as $k => $v) $r["{".$k."}"] = $v;
		return str_replace(array_keys($r), array_values($r), $str);
	}
	
	/**
	 * Generates a UUID version 4
	 *
	 */
	function gen_uuid() 
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		// 16 bits for "time_mid"
		mt_rand(0, 0xffff),
		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		mt_rand(0, 0x0fff) | 0x4000,
		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		mt_rand(0, 0x3fff) | 0x8000,
		// 48 bits for "node"
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}
	
	/*
	 * Generate random string with combination random salt & secret key
	 * 
	 */
	function gen_token(){
		$secretKey = "BismILLAHirrohmaanirrohiim";

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

	/*
	 * Generates a random salt value.
	 *
	 * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
	 *
	 * @return void
	 * @author Anthony Ferrera
	 */
	function gen_salt($salt_length=22)
	{
		$raw_salt_len = 16;
 		$buffer = '';
    $buffer_valid = false;

		if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
			$buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
			if ($buffer) {
				$buffer_valid = true;
			}
		}

		if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
			$buffer = openssl_random_pseudo_bytes($raw_salt_len);
			if ($buffer) {
				$buffer_valid = true;
			}
		}

		if (!$buffer_valid && @is_readable('/dev/urandom')) {
			$f = fopen('/dev/urandom', 'r');
			$read = strlen($buffer);
			while ($read < $raw_salt_len) {
				$buffer .= fread($f, $raw_salt_len - $read);
				$read = strlen($buffer);
			}
			fclose($f);
			if ($read >= $raw_salt_len) {
				$buffer_valid = true;
			}
		}

		if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
			$bl = strlen($buffer);
			for ($i = 0; $i < $raw_salt_len; $i++) {
				if ($i < $bl) {
					$buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
				} else {
					$buffer .= chr(mt_rand(0, 255));
				}
			}
		}

		$salt = $buffer;

		// encode string with the Base64 variant used by crypt
		$base64_digits   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
		$bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$base64_string   = base64_encode($salt);
		$salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

		$salt = substr($salt, 0, $salt_length);

		return $salt;
	}

	/*
	 * Simple random password generator with length limit
	 * 
	 */
	function gen_pwd($chars) 
	{
		$data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
		return substr(str_shuffle($data), 0, $chars);
	}

	/* 
	 * Method for checking token validation
	 * 
	 * params array()
	 * 
	 * return @error 		array(status = FALSE, message = 'Token not found !')
	 * return @error 		array(status = FALSE, message = 'Invalid token !')
	 * return @error 		array(status = FALSE, message = 'Token expired !')
	 * return @success 	array(status = TRUE, data = $row)
	 * 
	 */
	function is_valid_token($request)
	{
		if (!isset($request->token) || empty($request->token))
			return [FALSE, ['message' => F::lang('err_token_invalid')]];
		// else
			// $request->token = urldecode($request->token);
		
		if ($request->agent == 'android') {
			$token = ['android_token' => $request->token];
		}
		if ($request->agent == 'ios') {
			$token = ['ios_token' => $request->token];
		}
		if ($request->agent == 'web') {
			$token = ['web_token' => $request->token];
		}
		
		$ci = &get_instance();
		$ci->load->model('auth_model');
		$row = $ci->auth_model->get_token($token);
		if (!$row)
			return [FALSE, ['message' => F::lang('err_token_invalid')]];

		if ($request->agent == 'android') {
			$token = $row->android_token;
			$token_exp = $row->android_token_expired;
		}
		if ($request->agent == 'ios') {
			$token = $row->ios_token;
			$token_exp = $row->ios_token_expired;
		}
		if ($request->agent == 'web') {
			$token = $row->web_token;
			$token_exp = $row->web_token_expired;
		}
		
		if ($request->token != $token)
			return [FALSE, ['result' => NULL, 'message' => F::lang('err_token_invalid')]];
		
		if ($token_exp < date('Y-m-d H:i:s'))
			return [FALSE, ['result' => NULL, 'message' => F::lang('err_token_expired')]];
		
		return [TRUE, ['result' => $row, 'message' => NULL]];
	}
	
	/*
	 * Create avatar from word, like on google mail apps
	 * 
	 */
	function create_avatar_img($word = '', $img_path = '', $img_url = '', $font_path = '')
	{
		$defaults = array(
			'word'		=> '',
			'img_path'	=> '__tmp/',
			'img_url'	=> base_url('__tmp/'),
			'img_width'	=> '215',
			'img_height'	=> '215',
			'img_type'	=> 'png',
			'font_path'	=> BASEPATH.'fonts/texb.ttf',
			'word_length'	=> 1,
			'font_size'	=> 100,
			'img_id'	=> '',
		);
		
		if (is_array($word)) {
			foreach ($word as $k => $v)
			{
				$defaults[$k] = !empty($v) ? $v : $defaults[$k];
			}
		} else {
			$defaults['word'] 			= !empty($word) ? $word : $defaults['word'];
			$defaults['img_path'] 	= !empty($img_path) ? $img_path : $defaults['img_path'];
			$defaults['img_url'] 		= !empty($img_url) ? $img_url : $defaults['img_url'];
			$defaults['font_path'] 	= !empty($font_path) ? $font_path : $defaults['font_path'];
		}

		extract($defaults);

		if (! extension_loaded('gd'))
		{
			show_error('This '.__CLASS__.'->'.__FUNCTION__.' Function requires the php_gd extension.');
		}
		
		if ($img_path === '' OR $img_url === '' OR ! is_dir($img_path) OR ! is_really_writable($img_path))
		{
			return FALSE;
		}
		
		$im = function_exists('imagecreatetruecolor')
			? imagecreatetruecolor($img_width, $img_height)
			: imagecreate($img_width, $img_height);
		
		$i = strtoupper(substr($word, 0, 1));
		$r = rand(0, 255);
		$g = rand(0, 255);
		$b = rand(0, 255);
		$x = (imagesx($im) - $font_size * strlen($i)) / 2;
		$y = (imagesy($im) + ($font_size-($font_size*0.25))) / 2;
		$bg = imagecolorallocate($im, $r, $g, $b);
		$tc = imagecolorallocate($im, 255, 255, 255);
		
		// Create the rectangle
		ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg);
		
		$use_font = ($font_path !== '' && file_exists($font_path) && function_exists('imagettftext'));
		if ($use_font === FALSE)
		{
			($font_size > 5) && $font_size = 5;
			imagestring($im, $font_size, $x, $y, $i, $tc);
		}
		else
		{
			// ($font_size > 30) && $font_size = 30;
			imagettftext($im, $font_size, 0, $x, $y, $tc, $font_path, $i);
		}

		// -----------------------------------
		//  Generate the image
		// -----------------------------------
		$now = microtime(TRUE);
		$img_url = rtrim($img_url, '/').'/';

		if ($img_type == 'jpeg')
		{
			$img_filename = $now.'.jpg';
			imagejpeg($im, $img_path.$img_filename);
		}
		elseif ($img_type == 'png')
		{
			$img_filename = $now.'.png';
			imagepng($im, $img_path.$img_filename);
		}
		else
		{
			return FALSE;
		}

		$img = '<img '.($img_id === '' ? '' : 'id="'.$img_id.'"').' src="'.$img_url.$img_filename.'" style="width: '.$img_width.'; height: '.$img_height .'; border: 0;" alt=" " />';
		ImageDestroy($im);

		return array(
			'image' 	=> $img, 
			'file_path' => $img_path.$img_filename, 
			'file_url'	=> $img_url.$img_filename,
			'filename' 	=> $img_filename
		);
	}

	/*
	 * Standard encoding for URL
	 * 
	 */
	function urlsafe_encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}

	/*
	 * Standard decoding for URL
	 * 
	 */
	function urlsafe_decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}

	/*
	 * Send Mail Method from CI
	 * 
	 */
	function send_mail($header, $body, $config = [])
	{
		if (!$config)
			$config = [
				'useragent'			=> 'CI Webservice',
				'newline'				=> "\r\n",
				'protocol'			=> 'smtp',
				'smtp_host'			=> 'ssl://smtp.gmail.com',
				'smtp_port'			=> '465',
				'smtp_user'			=> 'simpi.tfs@gmail.com',
				'smtp_pass'			=> 'ranwid94',
				'smtp_timeout'	=> '7',
				'charset'				=> 'iso-8859-1',
				'mailtype'			=> 'html',
				'priority'			=> '1',
			];
		
		$ci = &get_instance();
		$ci->load->library('email');
		$ci->email->initialize($config);
		$ci->email->clear();
		
		if (!$header->from)
			return [FALSE, 'Email From is required !'];
		if (!$header->to)
			return [FALSE, 'Email To is required !'];
		if (!$header->subject)
			return [FALSE, 'Email Subject is required !'];
		if (isset($header->cc))
			$ci->email->cc($header->cc);
		if (isset($header->bcc))
			$ci->email->bcc($header->bcc);
		
		$ci->email->from($header->from);
		$ci->email->to($header->to); 
		$ci->email->subject($header->subject);
		$ci->email->message($body);	

		if (!$ci->email->send())
			return [FALSE, $ci->email->print_debugger()];

		return [TRUE, ''];
	}

	/*
	 * Standard json output from me antho.firuze@gmail.com
	 * 
	 */
	function response($status = TRUE, $response = [], $statusHeader = FALSE, $exit = TRUE)
	{
		if ($statusHeader !== FALSE && !is_numeric($statusHeader)){
			header("HTTP/1.0 400");
			header('Content-Type: application/json');
			echo json_encode(['status' => FALSE, 'message' => 'Status Header must be numeric']);
			exit();
		}
		
		$BM =& load_class('Benchmark', 'core');
		$statusCode = $status  
										? 200 
										: $statusHeader ? $statusHeader : 200;
		
		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');

		$output['status'] 				= $status;
		$output['execution_time'] = $elapsed;
		// $output['environment'] 		= ENVIRONMENT;
		
		if (!$exit)
			return array_merge($output, $response);
			// return json_encode(array_merge($output, $response));
		
		header("HTTP/1.0 $statusCode");
		header('Content-Type: application/json');
		echo json_encode(array_merge($output, $response));
		exit();
	}

	/*
	 * Bare json output from me antho.firuze@gmail.com
	 * 
	 */
	function bare_response($status = TRUE, $response = [], $statusHeader = FALSE, $exit = TRUE)
	{
		if ($statusHeader !== FALSE && !is_numeric($statusHeader)){
			header("HTTP/1.0 400");
			header('Content-Type: application/json');
			echo json_encode(['status' => FALSE, 'message' => 'Status Header must be numeric']);
			exit();
		}
		
		$statusCode = $status ? 200 
								: $statusHeader ? $statusHeader 
								: 200;

		$output['status'] 				= $status;
		$output['environment'] 		= ENVIRONMENT;
		
		header("HTTP/1.0 $statusCode");
		header('Content-Type: application/json');
		echo json_encode(array_merge($output, $response));
		if ($exit) 
			exit();
	}

	/*
	 * Standard debug output from me antho.firuze@gmail.com
	 * 
	 */
	function debug($data = '', $type = '')
	{
		if ($type == '') {
			echo var_dump($data); 
		} else if ($type == 'json') {
			header("HTTP/1.0 200");
			header('Content-Type: application/json');
			echo json_encode($data); 
		}
		exit;
	}

	function print_query()
	{
		$ci = &get_instance();
		$qry = $ci->db->get();
		return [FALSE, ['message' => $ci->db->last_query()]];
	}
	
	function get_row()
	{
		$ci = &get_instance();
		if (!$qry = $ci->db->get())
			return [FALSE, ['message' => $ci->db->error()['message']]];

		if (!$row = $qry->row())
			return [FALSE, ['message' => 'Record not found']];
		
		return [TRUE, ['result' => $row]];
	}
	
	function get_result()
	{
		$ci = &get_instance();
		if (!$qry = $ci->db->get())
			return [FALSE, ['message' => $ci->db->error()['message']]];

		if (!$result = $qry->result())
			return [FALSE, ['message' => 'Records not found']];
		
		return [TRUE, ['result' => $result]];
	}
	
	function get_result_datatables($request)
	{
		list($success, $return) = F::get_result();
		
		$res['total'] = count($return);
		$res['rows']		= $return['result'];
		if (isset($request->params->footer) && !empty($request->params->footer)) {
			// $res['summary'] = $this->mget_rec($params, TRUE, explode(',', $request->params->footer));
			// foreach($summary as $k => $v){
				// $a[$v] = array_sum(array_column($result, $v)); 
			// }
			// $response['summary'] = $a;
		}
		
		return [TRUE, ['result' => $res]];
	}

	/*
	 * For setting URL Address
	 * 
	 * backend: 	http://localhost:8080/backend?lang=id&state=auth&page=login&token=845j2h5lkj24352kjnb3545
	 * frontend: 	http://localhost:8080/frontend?lang=id&page=home
	 * 
	 */
	function setURL($path, $lang, $state = null, $page, $token = null)
	{
		if (!in_array($path, ['backend','frontend']))
			return '';
		
		if ($path == 'backend')
			
			return BASE_URL.$path
				.'?lang='.$lang
				.'&state='.$state
				.'&page='.$page
				.(isset($token) ? '&token='.urlencode($token) : '');
		else if ($path == 'frontend')
			
			return BASE_URL.$path
				.'?lang='.$lang
				.'&page='.$page;
	}
	
}
