<?php

class Rest
{
	public function __construct()
	{
		$this->load->library('z_jwt/jwt');
	}
	
	public function __get($var)
	{
		return get_instance()->$var;
	}
	
	public static function output($response=array(), $statusHeader=200)
	{
		$statusHeader = empty($statusHeader) ? 200 : $statusHeader;
		if (! is_numeric($statusHeader))
			show_error('Status codes must be numeric', 500);
		
		header("HTTP/1.0 $statusHeader");
		header('Content-Type: application/json');
		echo json_encode(array_merge(['status' => $statusHeader], $response));
		exit();
	}
	
	public function output_wtkn($response=array(), $statusHeader=200)
	{
		$statusHeader = empty($statusHeader) ? 200 : $statusHeader;
		if (! is_numeric($statusHeader))
			show_error('Status codes must be numeric', 500);
		
		$response = is_array($response) ? $response : array($response);
		
		// CREATE TOKEN
		try {
			$token = $this->jwt->createToken($GLOBALS['identifier']);
			
		} catch (Exception $e) {
			Rest::output(['error' => $e->getMessage()], 500);
		}
		
		header("HTTP/1.0 $statusHeader");
		header('Content-Type: application/json');
		echo json_encode(array_merge(['status' => $statusHeader], ['Token-Key' => $token], $response));
		exit();
	}
	
}