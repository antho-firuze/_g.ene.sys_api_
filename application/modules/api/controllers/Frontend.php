<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/modules/z_rest/libraries/REST_Controller.php';

class Frontend extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		
		$this->load->model('api/frontend_model');
	}
	
	function _remap($method, $params = array())
	{
		// CHECK X-API-KEY HEADER
		$apps_key  	= $this->input->get_request_header('X-API-KEY');
		if (! $this->db->where('api_token', $apps_key)->get('a_system')->row() ) 
			$this->response(['status' => FALSE, 'message' => 'What are you doing...?'], 400);

		$controller_method = $method . '_' . $this->request->method;

		return call_user_func_array(array($this, $controller_method), $params);
	}

	function menu_get()
	{
		$arg = (object) $this->input->get();
		
		$result['status'] = true;
		$result['data'] = $this->frontend_model->getMenu();
		$this->response($result);
	}
	
}