<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('rn'))
{
	/**
	 * Generates ASCII Carriage Return
	 *
	 * @param	int	$count	Number of times to repeat the tag
	 * @return	string
	 */
	function rn(int $count = 1)
	{
		return str_repeat("\r\n", $count);
	}
}

if (!function_exists('run_shell'))
{
  /**
   * Running CLI command via WScript.Shell
   *
   * @param string $php_bin   PHP Bin file
   * @param string $php_file  PHP Script file (.php) to be execute (In CI it's must be path/index.php on root folder CI)
   * @param string $class     Class or Controller (CI)
   * @param string $method    Method of Function
   * @param mixed $params     Mixed Parameters
   * @return void
   */
	function run_shell(string $php_bin, string $php_file = NULL, string $class = NULL, string $method = NULL, $params = NULL)
	{
    $WshShell = new COM("WScript.Shell"); 
    
    if (!empty($php_file)) {
      $command = "$php_bin $php_file $class/$method $params";
    } else {
      $command = $php_bin;
    }

		$oExec = $WshShell->Run($command, 0, false); 
		return $oExec == 0 ? true : false; 		
		// $pid 	=  $WshShell->Exec($command);
		// return $pid;
	}
}

if (!function_exists('get_PHP_WIN'))
{
  /**
   * Get path\php.exe in Windows OS
   *
   * @return void
   */
	function get_PHP_WIN() {
		$paths = explode(PATH_SEPARATOR, getenv('PATH'));
		foreach ($paths as $path) {
			// we need this for XAMPP (Windows)
			if (strstr($path, 'php.exe') && isset($_SERVER["WINDIR"]) && file_exists($path) && is_file($path)) {
				return $path;
			}
			else {
				$php_executable = $path . DIRECTORY_SEPARATOR . "php" . (isset($_SERVER["WINDIR"]) ? ".exe" : "");
				if (file_exists($php_executable) && is_file($php_executable)) {
					return $php_executable;
				}
			}
    }
    
		return FALSE; // not found
	}
}

