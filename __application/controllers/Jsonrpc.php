<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Jsonrpc Class
 *
 * This Remote Procedure Call was design for Cross Domain Web Apps & for Mobile Apps 
 *
 * 
 */
class Jsonrpc extends CI_Controller 
{
	/*  
	*  JSONRPC Sample Request:
	Request:
		{
			"agent": "web",
			"licenseKey": "REMUrktGyRvoWUUgxfUDmVOTOcoskMWVlOaiykkHnczGXzzFLoNUheeVQRjpVbvKTydiTOHnUnpjxgVnCIGVXbOgKeqGaTKmHNLzvqLYGBaSNUyKUVMnuzOSJVyPvamaCdBIuxQlWHqwbUrNFGLdhwQicROzRTDFoYqLtPpxfBqMjpQMEJkOpiRfmtGCHrtcdKibFGuNqnXGgdYOacTiKMpfhkyakMPMinKyhPiXpvzckmYiuSBFxksqucTuhuE",
			"id": 1,
			"method": "auth.login",
			"params": {"email":"ahmad.firuze@gmail.com","password":"12345","time":"2018-09-07 08:30:00","time_epoch":1532313049}
		}
	
	Response Success:
		{
			"status": true,
			"execution_time": "0.2705",
			"result": {
				"user": {
					"ClientID": "3064",
					"email": "ahmad.firuze@gmail.com",
					"full_name": "NOVIZAR HADI SAPUTRA",
					"token": "KI7o6IXpqkaTnPRE0%2FfsR7WtCTb9OjXbhq5OAUyjMW4%3D",
					"token_exp": "2018-07-24T09:30:49Z",
					"token_exp_epoch": 1532424649
				}
			},
			"message": "Login Success",
			"id": 1
		}	

	Response Failed:
		{
			"status": false,
			"execution_time": "0.2705",
			"message": "Required parameter: licenseKey",
			"id": 1
		}	
	*  
	*/
	// ==============================================
	/* JSONRPC Config: Default config      				 */
	// ==============================================
	public $r_method_allowed = ['GET','DELETE','POST','PUT','OPTIONS','PATCH','UNLOCK','LOCK'];
	public $agent = ['android','ios','web','desktop'];
	public $languages 	= [
		'us' => ['id'	=> 'us', 'name' => 'English', 	'idiom' => 'english', 	'icon' => 'flag-icon-us'],
		'id' => ['id'	=> 'id', 'name' => 'Indonesia', 'idiom' => 'indonesia', 'icon' => 'flag-icon-id'],
	];
	public $bypass = [
		'bypass1'	=> '000',		// license
		'bypass2'	=> '0000',		// appcode
		'bypass3'	=> '00000',		// token
		'bypass4'	=> '000000',	// apimethod
	];
	public $allowed_origins = [
		"http://public.app.moxio.com",
		"https://foo.app.moxio.com",
		"https://lorem.app.moxio.com"
	];

	function __construct()
	{
		parent::__construct();
		
		$this->load->library('f');
		$this->load->helper('network');
		$this->lang->load('jsonrpc','english');
		
		// if (isset($_SERVER["HTTP_ORIGIN"])) {
		// 	$origin = $_SERVER["HTTP_ORIGIN"];
		// 	if (in_array($origin, $this->allowed_origins)) {
		// 		header('Access-Control-Allow-Origin: ' . $origin);
		// 		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		// 		header('Access-Control-Allow-Headers: Content-Type');
		// 	}
		// }

		$this->r_method = $_SERVER['REQUEST_METHOD'];
		if (!in_array($this->r_method, $this->r_method_allowed))
			$this->f->response(FALSE, ['message' => $this->f->lang('err_request_method_unsupported')], 403);
		
		// if (in_array($this->r_method, ['GET','DELETE'])) {
		// 	$this->params = (object) $this->input->get();
		// }
		
		// This request params must be place in body and with header: Content-Type: application/json
		// Request can be taken with :
		// $this->requests = file_get_contents('php://input');
		// or
		// $this->requests = $this->input->raw_input_stream;
		if (in_array($this->r_method, ['POST','PUT','OPTIONS','PATCH','UNLOCK','LOCK'])) {

			// 1. First check this Non Form-Data Request
			$request = json_decode(file_get_contents('php://input'));

			if (! $request || empty($request)) {
				// 2. Next check Form-Data Request : Using for uploading file/image
				$request = json_decode(json_encode($_POST));
			}
			
			if (!in_array(gettype($request), ['object', 'array']))
				$this->f->response(FALSE, ['message' => $this->f->lang('err_request_invalid')]);
			
			$result = $this->requery_request($request);
			if ($result) $this->json_out($result);
		} 
		else if (in_array($this->r_method, ['GET','DELETE'])) {
			$this->params = (object) $this->input->get();

			if (isset($this->params->method) && !empty($this->params->method)) {
				$result = $this->requery_request($this->params);
				if ($result) $this->json_out($result);
			}
		}
	}
	
	/**
	 * Default index, for checking OK or NOT
	 *
	 * @return void
	 */
	function index() { echo 'JSON RPC OK !'; }
	
	/**
	 * Method for adding default params to Request
	 *
	 * @param object $request
	 * @return void
	 */
	private function set_default_params($request)
	{
		foreach ($this->bypass as $key => $value) {
			$request->{$key} = $value;
		}

		$request->ip_address = get_ip_address();
		$request->is_local = is_private_ip(get_ip_address()) ? '1' : '0';
	}
	
	/**
	 * Method for checking is request valid?
	 *
	 * @param object $request
	 * @return bool
	 */
	private function is_valid_request($request)
	{
		if (!$request || is_string($request))
			return [FALSE, ['message' => $this->f->lang('err_request_invalid')]];
		
		if (!is_object($request))
			return [FALSE, ['message' => $this->f->lang('err_request_invalid')]];
		
		$this->set_default_params($request);
				
		// list($success, $return) = $this->f->is_valid_apimethod($request);
		// if (!$success) return [FALSE, $return];
		
		list($success, $result) = $this->is_valid_agent($request);
		if (!$success) return [FALSE, $result];
		
		list($success, $result) = $this->is_valid_language($request);
		if (!$success) return [FALSE, $result];
		
		return [TRUE, $request];
	}
	
	/**
	 * Method for checking request agent
	 *
	 * @param object $request
	 * @return bool
	 */
	private function is_valid_agent($request)
	{
		if (!isset($request->agent))
			return [FALSE, ['message' => $this->f->lang('err_param_requiredc', 'agent')]];	

		if (empty($request->agent))
			return [FALSE, ['message' => $this->f->lang('err_param_unsupported', '{agent: null}')]];	

		if (!in_array($request->agent, $this->agent))
			return [FALSE, ['message' => $this->f->lang('err_param_unsupported', "{agent: $request->agent}")]];	
		
		return [TRUE, NULL];
	}
	
	/**
	 * Method for checking request language
	 *
	 * @param object $request
	 * @return bool
	 */
	private function is_valid_language($request)
	{
		if (isset($request->lang)) {
			if (! in_array($request->lang, array_keys($this->languages))) 
				return [FALSE, ['message' => $this->f->lang('err_param_unsupported', "{lang: $request->lang}")]];	
				
			$request->idiom = $this->languages[$request->lang]['idiom'];
		} else {
			$request->lang = 'us';
			$request->idiom = 'english';
		}
		
		return [TRUE, NULL];
	}
	
	/**
	 * Method for checking request params, is valid JSON Object or not?
	 *
	 * @param object $request
	 * @return void
	 */
	private	function pre_checking_params($request){
		if (isset($request->params) || !empty($request->params))
			if (is_string($request->params))
				$request->params = json_decode($request->params);
		
		return $request;
	}
		
	/**
	 * Function for requering request
	 *
	 * @param object $request
	 * @return void
	 */
	private function requery_request($request)
	{
		if (is_object($request)){
			list($success, $result) = $this->is_valid_request($request);
			if (!$success) {
				if (isset($request->id)) 
					$result['id'] = $request->id;
				
				return $this->f->response(FALSE, $result, FALSE, FALSE);
			} else {
				$request = $this->pre_checking_params($request);
			
				return $this->exec_method($request);
			}
		} elseif (is_array($request)) {
			if (count((array)$request) < 1)
				$this->f->response(FALSE, ['message' => $this->f->lang('err_request_invalid'), 'id' => null]);
			
			$result = [];
			foreach($request as $k => $r) {

				$result[$k] = $this->requery_request($r);
			}

			return $result;
		} 
	}
	
	/**
	 * JSON Output
	 *
	 * @param object|array $result
	 * @return void
	 */
	private function json_out($result)
	{
		header("HTTP/1.0 200");
		// === for Allow Cross Domain Webservice ===
		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type");
		// header("Access-Control-Allow-Headers: Origin");
		// === for Allow Cross Domain Webservice ===
		header('Accept-Ranges: bytes');
		header('Content-Type: application/json');

		if (is_array($result)){
			if (count($result) > 0)
				die(json_encode($result));
		} else {
			if ($result || !empty($result))
				die(json_encode($result));
		}
	}
		
	/**
	 * Execute Request Method
	 *
	 * 1. If result error then request->id == null
	 * 2. If request->id <> null
	 * 3. Except No. 1 & 2 => no output (that's notification)
	 * 
	 * @param object $request
	 * @return void
	 */
	private function exec_method($request)
	{
		if (!isset($request->method))
			return $this->f->response(FALSE, ['message' => $this->f->lang('err_param_required', 'method'), 'id' => (isset($request->id) ? $request->id : null)], FALSE, FALSE);

		if (empty($request->method))
			return $this->f->response(FALSE, ['message' => $this->f->lang('err_method_unknown', 'null'), 'id' => (isset($request->id) ? $request->id : null)], FALSE, FALSE);

		$this->lang->load(['auth','simpi'], $request->idiom);
		
		// =================== Check is valid method ======================
		$parseMethod = explode('.', $request->method);
		if (count($parseMethod) < 2)
			return $this->f->response(FALSE, ['message' => $this->f->lang('err_method_unknown', $request->method), 'id' => (isset($request->id) ? $request->id : null)], FALSE, FALSE);
		
		$model_path = APPPATH.'models'.DIRECTORY_SEPARATOR.(PREFIX_FOLDER ? PREFIX_FOLDER.DIRECTORY_SEPARATOR : '');
		$dir = strtolower(PREFIX_FOLDER ? PREFIX_FOLDER.SEPARATOR : '');
		if ($tmp_dir = array_slice($parseMethod, 0, count($parseMethod)-2)) {
			$model_path = $model_path . implode(DIRECTORY_SEPARATOR, $tmp_dir) . DIRECTORY_SEPARATOR;
			$dir = strtolower(PREFIX_FOLDER ? PREFIX_FOLDER.SEPARATOR : '') . implode(SEPARATOR, $tmp_dir) . SEPARATOR;
		} 

		list($class, $method) = explode('.', implode('.', array_slice($parseMethod, -2, 2)));
	
		$model = ucfirst($class).'_model';
		if(!file_exists($model_path.$model.'.php'))
			return $this->f->response(FALSE, ['message' => $this->f->lang('err_method_unknown', $request->method), 'id' => (isset($request->id) ? $request->id : null)], FALSE, FALSE);
		
		$this->load->model($dir.$model);
		if (!method_exists($this->{$model}, $method))
			return $this->f->response(FALSE, ['message' => $this->f->lang('err_method_unknown', $request->method), 'id' => (isset($request->id) ? $request->id : null)], FALSE, FALSE);
		// =================== Check is valid method ======================

		// Clear any cache before execute any method
		// $this->db->reset_query();
		
		// Load the language
		if(file_exists(APPPATH.'language/'.$request->idiom.'/'.strtolower($class).'_lang.php'))
			$this->lang->load(['jsonrpc', strtolower($class)], $request->idiom);

		// Execute the process
		list($success, $result) = $this->{$model}->{$method}($request);
		if (!$success) {
			$result['id'] = $request->id;
			return $this->f->response(FALSE, $result, FALSE, FALSE);
		}
		
		if (isset($request->id) && !empty($request->id)) {
			$result['id'] = $request->id;
			return $this->f->response(TRUE, $result, FALSE, FALSE);
		}
	}
	
}