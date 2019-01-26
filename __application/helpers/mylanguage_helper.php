<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('mylang'))
{
  /**
   * Fetches a language variable with mixed arguments
   *
   * @param string $key
   * @param mixed $args
   * @return void
   */
  function mylang(string $key, $args = NULL) 
  {
		$ci = get_instance()->load->helper(['myarray','mystring','language']);

		if (is_array($args)) {

			if (is_array_assoc($args))
				return sprintfx(lang($key), $args);
			else
        return vsprintf(lang($key), $args);
        
		}	elseif (!empty($args)) {

      return lang($key) ? sprintf(lang($key), $args) : sprintf("$key: [%s]", $args);
      
		} else {

      return lang($key) ? lang($key) : $key;

    }
	}
}

