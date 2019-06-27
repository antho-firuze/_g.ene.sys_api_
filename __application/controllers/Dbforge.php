<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dbforge extends CI_Controller 
{
	public $dbscripts_folder = '__dbscripts';
	public $prefix_table = 'table.';
	
	function __construct() {
		parent::__construct();
		$this->load->database('mariadb');
		// $this->load->database('postgres');
		
		/* Get input from request browser */
		$request = (object) $this->input->get();
		// ============================================================== 
		// Checking parameters
		// ============================================================== 
		if (isset($request->params) || !empty($request->params))
			if (is_string($request->params))
				$request->params = json_decode($request->params);
		
		if (isset($request->method)) {
			if (! method_exists($this, $request->method))
				die('DBForge: Method not exists');

			$this->{$request->method}($request);
		}
	}
	
	function index() { echo 'DBForge [OK]'; }

	function create_table($request)
	{
		$path = FCPATH . $this->dbscripts_folder . DIRECTORY_SEPARATOR;
		$tbl_name = $request->params->name;
		
		if(! file_exists($path.$this->prefix_table.$tbl_name.'.php'))
			die('Error: Table name not found ['.$tbl_name.']');

		// Load table fields
		include_once($path.$this->prefix_table.$tbl_name.'.php');
		
		/* Drop table if exists  */
		$this->load->dbforge();
		$this->dbforge->drop_table($tbl_name,TRUE);
		/* CREATE TABLE */
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_field($fields);
		if (! $result = $this->dbforge->create_table($tbl_name, TRUE)){
			die('DBForge: FAILED');
		}
		die('DBForge: SUCCESS');
	}
	
	function drop_table($tbl_name)
	{
		$tbl_name = $request->params->name;

		$this->load->dbforge();
		$this->dbforge->drop_table($tbl_name,TRUE);
		die('DBForge: SUCCESS');
	}
	
}