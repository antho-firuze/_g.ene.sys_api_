<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('strpos_array'))
{
  /**
   * Find the position of the first occurrence of a substring in a array of string
   *
   * @param string $haystack  The string to search in
   * @param mixed $needles    String or Array with string value
   * @return void
   */
	function strpos_array(string $haystack, $needles) {
		if (is_array($needles)) {
			foreach ($needles as $str) {
        $pos = strpos_array($haystack, $str);
				if ($pos !== FALSE) 
					return $pos;
			}
		} else {
			return strpos($haystack, $needles);
		}
	}
}

if (!function_exists('sprintfx'))
{
  /**
   * Return a formatted string
   *
   * @param string $str
   * @param array $vars     Paired value array
   * @param string $prefix  Default '{'
   * @param string $suffix  Default '}'
   * @return void
   */
	function sprintfx(string $str, array $vars, string $prefix = '{', string $suffix = '}') {
    if (array() === $vars) 
      return $str;

    foreach ($vars as $key => $val) 
      $arr[$prefix.$key.$suffix] = $val;
    
    return str_replace(array_keys($arr), array_values($arr), $str);
	}
}

