<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/modules/z_func/models/Z_Model.php';

class Frontend_Model extends Z_Model
{

	public function __construct()
	{
		parent::__construct();
		
	}

	function getMenu($params = NULL)
	{
		$params['select']	= !array_key_exists('select', $params) ? "wm.*" : $params['select'];
		$params['table'] 	= "w_menu wm";
		$params['where']	= "wm.is_active = '1' and wm.is_deleted = '0'";
		$params['order']	= "wm.line_no";
		
		return $this->mget_rec($params);
	}
	
}