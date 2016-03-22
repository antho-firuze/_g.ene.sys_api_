<?php

/**
 * JSON Web Token implementation, based on this spec:
 * http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-06
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Authentication_JWT
 * @author   Neuman Vong <neuman@twilio.com>
 * @author   Anant Narayanan <anant@php.net>
 * @modified Ahmad Hertanto <antho.firuze@php.net>
 * @license  http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link     https://github.com/firebase/php-jwt
 */
 
class JWT
{
	public function __construct()
	{
		$this->load->config('z_jwt/jwt', TRUE);
	}
	
	public function __get($var)
	{
		return get_instance()->$var;
	}
	
	/*
	 * Create the token 
	 * 
	 * @param object|array	$payload    User custom data which want to add
	 * 
	 * By: Firuze
	 */
	public function createToken($payload)
	{
		$payload	= (is_array($payload)) ? $payload : array($payload);
		$key 		= $this->config->item('secret', 'jwt');
		$algo		= $this->config->item('algo', 'jwt');
		$issuedAt   = time();
		$notBefore  = $issuedAt + $this->config->item('nbf', 'jwt');        	
		$expire     = $notBefore + $this->config->item('exp', 'jwt'); 
		
		/*
		 * Create the token as an array
		 */
		$assets = [
			'iat'  => $issuedAt,         // Issued at: time when the token was generated
			'nbf'  => $notBefore,        // Not before
			'exp'  => $expire,           // Expire
			'data' => array_merge($payload)	// Data to be included
		];
		
		$result['token']  = JWT::encode($assets, $key, $algo);
		$result['expire'] = $this->config->item('exp', 'jwt');
		// $GLOBALS['identifier']['exp'] = $this->config->item('exp', 'jwt');
		
		// return JWT::encode($assets, $key, $algo);
		return (object)$result;
	}

	/*
	 * Check & Get the token data
	 * 
	 * @param string		$jwt    The JWT
	 * 
	 * By: Firuze
	 */
	public function checkToken($jwt)
	{
		$key 		= $this->config->item('secret', 'jwt');
		$notBefore  = $this->config->item('nbf', 'jwt');     
		$expire  	= $this->config->item('exp', 'jwt')/60;     
		
		$assets 	= JWT::decode($jwt, $key, true);
		
		// $issuedAt	= (new \Moment\Moment('@'.$assets->iat, 'Asia/Jakarta'))->format('Y-m-d H:i:s'); // today
		$issuedAt	= (new DateTime())->setTimestamp($assets->iat)->format('Y-m-d H:i:s');
		
		if ( time() < $assets->nbf ) {
			throw new Exception("The Token will begin to be valid $notBefore seconds after being issued at $issuedAt.");
		}
		
		if ( $assets->exp < time() ) {
			throw new Exception("The Token was expired. This token was created at $issuedAt, and expired after $expire minutes.");
		}
		
		return $assets->data;
	}

	/**
	 * Decodes a JWT string into a PHP object.
	 *
	 * @param string      $jwt    The JWT
	 * @param string|null $key    The secret key
	 * @param bool        $verify Don't skip verification process 
	 *
	 * @return object      The JWT's payload as a PHP object
	 * @throws Exception Provided JWT was invalid
	 * @throws Exception          Algorithm was not provided
	 * 
	 * @uses jsonDecode
	 * @uses urlsafeB64Decode
	 */
	public static function decode($jwt, $key = null, $verify = true)
	{
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			throw new Exception('Wrong number of segments');
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;
		if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))) {
			throw new Exception('Invalid segment encoding');
		}
		if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64))) {
			throw new Exception('Invalid segment encoding');
		}
		$sig = JWT::urlsafeB64Decode($cryptob64);
		if ($verify) {
			if (empty($header->alg)) {
				throw new Exception('Empty algorithm');
			}
			if ($sig != JWT::sign("$headb64.$bodyb64", $key, $header->alg)) {
				throw new Exception('Signature verification failed');
			}
		}
		return $payload;
	}
	/**
	 * Converts and signs a PHP object or array into a JWT string.
	 *
	 * @param object|array $payload PHP object or array
	 * @param string       $key     The secret key
	 * @param string       $algo    The signing algorithm. Supported
	 *                              algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string      A signed JWT
	 * @uses jsonEncode
	 * @uses urlsafeB64Encode
	 */
	public static function encode($payload, $key, $algo = 'HS256')
	{
		$header = array('typ' => 'JWT', 'alg' => $algo);
		$segments = array();
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($header));
		$segments[] = JWT::urlsafeB64Encode(JWT::jsonEncode($payload));
		$signing_input = implode('.', $segments);
		$signature = JWT::sign($signing_input, $key, $algo);
		$segments[] = JWT::urlsafeB64Encode($signature);
		return implode('.', $segments);
	}
	/**
	 * Sign a string with a given key and algorithm.
	 *
	 * @param string $msg    The message to sign
	 * @param string $key    The secret key
	 * @param string $method The signing algorithm. Supported
	 *                       algorithms are 'HS256', 'HS384' and 'HS512'
	 *
	 * @return string          An encrypted message
	 * @throws Exception Unsupported algorithm was specified
	 */
	public static function sign($msg, $key, $method = 'HS256')
	{
		$methods = array(
			'HS256' => 'sha256',
			'HS384' => 'sha384',
			'HS512' => 'sha512',
		);
		if (empty($methods[$method])) {
			throw new Exception('Algorithm not supported');
		}
		return hash_hmac($methods[$method], $msg, $key, true);
	}
	/**
	 * Decode a JSON string into a PHP object.
	 *
	 * @param string $input JSON string
	 *
	 * @return object          Object representation of JSON string
	 * @throws Exception Provided string was invalid JSON
	 */
	public static function jsonDecode($input)
	{
		$obj = json_decode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($obj === null && $input !== 'null') {
			throw new Exception('Null result with non-null input');
		}
		return $obj;
	}
	/**
	 * Encode a PHP object into a JSON string.
	 *
	 * @param object|array $input A PHP object or array
	 *
	 * @return string          JSON representation of the PHP object or array
	 * @throws Exception Provided object could not be encoded to valid JSON
	 */
	public static function jsonEncode($input)
	{
		$json = json_encode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::_handleJsonError($errno);
		} else if ($json === 'null' && $input !== null) {
			throw new Exception('Null result with non-null input');
		}
		return $json;
	}
	/**
	 * Decode a string with URL-safe Base64.
	 *
	 * @param string $input A Base64 encoded string
	 *
	 * @return string A decoded string
	 */
	public static function urlsafeB64Decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}
	/**
	 * Encode a string with URL-safe Base64.
	 *
	 * @param string $input The string you want encoded
	 *
	 * @return string The base64 encode of what you passed in
	 */
	public static function urlsafeB64Encode($input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
	/**
	 * Helper method to create a JSON error.
	 *
	 * @param int $errno An error number from json_last_error()
	 *
	 * @return void
	 */
	private static function _handleJsonError($errno)
	{
		$messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
		throw new Exception(
			isset($messages[$errno])
			? $messages[$errno]
			: 'Unknown JSON error: ' . $errno
		);
	}
}
