<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('urlsafeB64Encode'))
{
  /**
   * URL Save Base 64 Encoding
   *
   * @param string $input
   * @return void
   */
	function urlsafeB64Encode(string $input)
	{
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}
}

if (!function_exists('urlsafeB64Decode'))
{
    /**
     * URL Save Base 64 Decoding
     *
     * @param string $input
     * @return void
     */
	function urlsafeB64Decode(string $input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}
}

if ( ! function_exists('create_token'))
{
    /**
     * Generates a random string for token
     *
     * @return void
     */
    function create_token()
    {
        $secretKey = "B1sm1LLAH1rrohmaan1rroh11m";

        // Generates a random string of ten digits
        $salt = mt_rand();

        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac('sha256', $salt, $secretKey, false);
        // $signature = hash('md5', $salt.$secretKey);

        // return $signature;
        return urlsafeB64Encode($signature);
    }
}

if ( ! function_exists('salt'))
{
    /**
     * Generates a random salt value
     *
	 * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
	 *
     * @param int $salt_length
     * @return void
     */
	function salt(int $salt_length=22)
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
}

if ( ! function_exists('UUIDv4'))
{
    /**
     * Generate a random UUID version 4
     *
     * @return void
     */
	function UUIDv4() 
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
}

if ( ! function_exists('random_str'))
{
	/**
	 * Create a "Random" String
	 *
	 * @param	int	$len        number of characters
	 * @param	string $type	Type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
	 * @return	string
	 */
    function random_str(int $len = 8, string $type = 'alnum')
    {
        $ci = get_instance()->load->helper('string');

        return random_string($type, $len);
    }
}

