<?php defined('FCPATH') OR exit('No direct script access allowed'); 

require(APPPATH.'libraries'.DIRECTORY_SEPARATOR.'F.php');
$f = new F;

/* Time Zone */ 
define('TIME_ZONE', 'Asia/Jakarta'); 
@date_default_timezone_set(TIME_ZONE);

/* Base URL */ 
$protocol = isset($_SERVER["HTTPS"]) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$http_alias = '';
$http_port = '';
if (strpos($http_host, 'www') > -1) {
	list($http_alias, $http_host, $http_dot) = explode('.', $http_host);
	$http_host = implode('.', [$http_host, $http_dot]);
}

if (strpos($http_host, ':') > -1)
	list($http_host, $http_port) = explode(':', $http_host);

/* List available domain */
$domain_devel = ['localhost','127.0.0.1','192.168.1.15','192.168.100.105'];
$domain_live = ['example.com'];
$domain = array_merge($domain_devel, $domain_live);
if (!in_array($http_host, $domain))
	$f->bare_response(FALSE, ['message' => "Domain name <strong>$http_host</strong> is not available !"]);

$http_host_full = $http_alias ? $http_alias.'.'.$http_host : $http_host;
$http_host_full = $http_port ? $http_host_full.':'.$http_port : $http_host_full;
define('PROTOCOL', $protocol);
define('HTTP_HOST', $http_host_full);

if (isset($_SERVER['REQUEST_METHOD']))			// for bypass php cli execute. (This REQUEST_METHOD is not exist in cli mode)
	define('HTTP_METHOD', $_SERVER['REQUEST_METHOD']);

/* Define BASE_URL. Implement on $config['base_url'] */
define('SEPARATOR', '/');
define('BASE_URL', PROTOCOL.HTTP_HOST.SEPARATOR); 

/* Init TMP/CACHE Folder */
$tmp = '__tmp';
if (!file_exists($tmp) && !is_dir($tmp)) { mkdir($tmp); } 

if (in_array($http_host, $domain_devel))
	define('IS_LOCAL', TRUE);
else
	define('IS_LOCAL', FALSE);

/* Override php.ini config */
if (function_exists('ini_set')) {
	@ini_set('max_execution_time', 300);
	@ini_set('date.timezone', TIME_ZONE);
	@ini_set('post_max_size', '8M');
	@ini_set('upload_max_filesize', '2M');
	@ini_set('display_errors', IS_LOCAL ? on : off);					// on | off
	@ini_set('error_reporting', IS_LOCAL ? E_ALL : 0);					// 0 | E_ALL | E_ERROR | E_WARNING | E_PARSE | E_NOTICE
}

/* Define default path. Implement on $route['default_controller'] */
$path_localhost = [
	7000 => 'webapps',
	7001 => 'jsonrpc',
];
$path = [
	'localhost' 			=> $path_localhost[$http_port],
	'example.com' 		=> 'webapps',
	'api.example.com' => 'api',
];
define('PATH', $path[$http_host]);

define('REPOS_URL', BASE_URL.'__repository'.SEPARATOR);
define('REPOS_DIR', __dir__.DIRECTORY_SEPARATOR.'__repository'.DIRECTORY_SEPARATOR);
define('API_URL', BASE_URL);

// Prefix folder in application/model (for jsonrpc)
$prefix_localhost = [
	7000 => '',
	7001 => 'api',
];
$prefix = [
	'localhost' => $prefix_localhost[$http_port],
	'api.example.com' => 'api',
];
define('PREFIX_FOLDER', $prefix[$http_host]);
