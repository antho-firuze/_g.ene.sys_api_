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
		$query = "select 
		am1.id as menu_id1, am1.name as name1, am1.is_parent as is_parent1, am1.url as url1, am1.icon as icon1,  
		am2.id as menu_id2, am2.name as name2, am2.is_parent as is_parent2, am2.url as url2, am2.icon as icon2,  
		am3.id as menu_id3, am3.name as name3, am3.is_parent as is_parent3, am3.url as url3, am3.icon as icon3
		from (
			select am.* from w_menu am 
			where am.is_active = '1' and am.is_deleted = '0'
		) am1
		left join (
			select am.* from w_menu am 
			where am.is_active = '1' and am.is_deleted = '0'
		) am2 on am1.id = am2.parent_id 
		left join (
			select am.* from w_menu am 
			where am.is_active = '1' and am.is_deleted = '0'
		) am3 on am2.id = am3.parent_id 
		where am1.parent_id = '0'
		order by am1.line_no, am2.line_no, am3.line_no";
		
		return $this->db->query($query)->result();
	}
	
}