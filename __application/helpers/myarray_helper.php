<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('is_array_assoc'))
{
  /**
   * Check whenever array key is numeric or not
   *
   * @param array $arr
   * @return bool
   */
	function is_array_assoc(array $arr) {
    if (array() === $arr) 
      return false;
    
    return array_keys($arr) !== range(0, count($arr) - 1);
	}
}

if (!function_exists('remove_empty_array'))
{
  /**
   * Remove whenever array value is empty or null
   *
   * @param array $arr
   * @return void
   */
	function remove_empty_array(array $arr) {
		return array_filter($arr, function($value) {
			return !empty($value) || $value === 0;
		});
	}
}

