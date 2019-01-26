<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('get_ip_address'))
{
	/**
	 * Get IP Address of current request
	 *
	 * @return void
	 */
	function get_ip_address()
	{
		$ipaddress = '';
    // if ($_SERVER['HTTP_CLIENT_IP'])
			// $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    // if($_SERVER['HTTP_X_FORWARDED_FOR'])
			// $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    // if($_SERVER['HTTP_X_FORWARDED'])
			// $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    // else if($_SERVER['HTTP_FORWARDED_FOR'])
			// $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    // else if($_SERVER['HTTP_FORWARDED'])
			// $ipaddress = $_SERVER['HTTP_FORWARDED'];
    // if($_SERVER['REMOTE_ADDR'])
			// $ipaddress = $_SERVER['REMOTE_ADDR'];
    // else
			// $ipaddress = 'UNKNOWN';
		
		// if (getenv('HTTP_CLIENT_IP'))
			// $ipaddress = getenv('HTTP_CLIENT_IP');
    // else if(getenv('HTTP_X_FORWARDED_FOR'))
			// $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    // else if(getenv('HTTP_X_FORWARDED'))
			// $ipaddress = getenv('HTTP_X_FORWARDED');
    // else if(getenv('HTTP_FORWARDED_FOR'))
			// $ipaddress = getenv('HTTP_FORWARDED_FOR');
    // else if(getenv('HTTP_FORWARDED'))
			// $ipaddress = getenv('HTTP_FORWARDED');
    // else if(getenv('REMOTE_ADDR'))
			// $ipaddress = getenv('REMOTE_ADDR');
    // else
			// $ipaddress = 'UNKNOWN';
		
    // return $ipaddress;
		
		if ( function_exists( 'apache_request_headers' ) ) {
			$headers = apache_request_headers();
		} else {
			$headers = $_SERVER;
		}
		//Get the forwarded IP if it exists
		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )) {
			$the_ip = $headers['X-Forwarded-For'];
		} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )) {
			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
		} else {
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
		return $the_ip;
	}
}

if ( ! function_exists('is_private_ip'))
{
	/**
	 * Check if a client IP is in our Server subnet
	 *
	 * @param [string] $ip
	 * @return bool
	 */
	function is_private_ip($ip) 
	{
    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}
}

