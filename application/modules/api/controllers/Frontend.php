<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/modules/z_rest/libraries/REST_Controller.php';

class Frontend extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		
		$this->load->model('api/frontend_model');
	}
	
	function menu_get()
	{
		$arg = (object) $this->input->get();
		
		$params['select'] = 'wm.*';
		$result['data'] = $this->frontend_model->getMenu($params);
		$this->xresponse(TRUE, $result);
	}
	
}