<?php defined('BASEPATH') OR exit('No direct script access allowed');

class System_model extends CI_Model 
{
	public function __construct() {
    parent::__construct();
    $this->load->database('mariadb');
  }
  
	function menu($request)
	{
		// list($success, $return) = $this->f->is_valid_appcode($request);
		// if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$table = '(
			select * from a_menu 
		) g0';
		// $table = $this->f->compile_qry($table, [$request->]);
		$this->db->from($table);
		return $this->f->get_result($request);
	}
	
	function menu_add($request)
	{
		// list($success, $return) = $this->f->is_valid_appcode($request);
		// if (!$success) return [FALSE, $return];
		
		if (isset($request->params->fields) && !empty($request->params->fields))
			$this->db->select($request->params->fields);

		$this->db->insert('a_menu',$request->params);
		return [TRUE, ['message' => $this->f->lang('success_insert')]];
	}

}