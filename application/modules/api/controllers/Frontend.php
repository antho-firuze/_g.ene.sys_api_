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

	function menulist_get()
	{
		$org_id = $this->input->get('org_id');
		$org_id = is_numeric($org_id) ? $org_id : -1;
		
		$result['data'] = [];
		if ($org_id >= 0)
		{
			$result['data'] = $this->frontend_model->getMenu($org_id);
		}
		$this->xresponse(true, $result);
	}
	
	function dashboard_get()
	{
		$id = $this->input->get('id');
		$id = is_numeric($id) ? $id : -1;
		
		$result['data'] = [];
		if ($id >= 0)
		{
			$result['data'] = $this->frontend_model->getDashboard($id);
		}
		$this->xresponse(true, $result);
	}
	
	function infolist_get()
	{
		$arg = (object) $this->input->get();
		$params = (array) $arg;
		
		if (array_key_exists('client_id', $params))
			$params['where']['ai.client_id'] = $arg->client_id;
		if (array_key_exists('org_id', $params))
			$params['where']['ai.org_id'] 	 = $arg->org_id;
		$params['where']['ai.valid_from <='] = datetime_db_format();

		$result['data'] = $this->frontend_model->getInfo($params);
		$this->xresponse(true, $result);
	}
	
}