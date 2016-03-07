<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/modules/z_rest/libraries/REST_Controller.php';

class Purchasing extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		
		$this->load->model('api/system_model');
	}
	
}