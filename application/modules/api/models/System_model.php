<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/modules/z_func/models/Z_Model.php';

class System_Model extends Z_Model
{

	public function __construct()
	{
		parent::__construct();
		
	}

	/* public function __call($method, $arguments)
	{
		if (!method_exists( $this, $method) )
		{
			throw new Exception('Undefined method ' . $method . '() called');
		}
		return call_user_func_array( array($this, $method), $arguments);
	} */
	
	function getUser($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "au.*" : $params['select'];
		$params['table'] 	= "a_user as au";
		$params['join'][] 	= ['a_client as ac', 'au.client_id = ac.id', 'left'];
		$params['join'][] 	= ['a_org as ao', 'au.org_id = ao.id', 'left'];
		$params['join'][] 	= ['a_role as ar', 'au.role_id = ar.id', 'left'];
		$params['where']['au.is_deleted'] 	= '0';
		
		return $this->mget_rec($params);
	}
	
	function getUserConfig($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "auc.*" : $params['select'];
		$params['table'] 	= "a_user_config auc";
		$params['where']['auc.is_active'] 	= '1';
		$params['where']['auc.is_deleted'] 	= '0';
		
		return $this->mget_rec($params);
	}
	
	function getUserWCount($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "au.*" : $params['select'];
		$params['table'] 	= "a_user as au";
		$params['join'][] 	= ['a_user_config as auc', 'au.id = auc.user_id', 'left'];
		$params['join'][] 	= ['a_client as ac', 'au.client_id = ac.id', 'left'];
		$params['join'][] 	= ['a_org as ao', 'au.org_id = ao.id', 'left'];
		$params['join'][] 	= ['a_role as ar', 'au.role_id = ar.id', 'left'];
		$params['where']['au.is_deleted'] 	= '0';
		
		return $this->mget_rec_count($params);
	}
	
	function updateUser($data, $cond)
	{
		$data = is_object($data) ? (array) $data : $data;
		
		return $this->db->update('a_user', $data, $cond);
	}
	
	function deleteUser($ids, $user_id)
	{
		$ids = array_filter(array_map('trim',explode(',',$ids)));
		
		$return = 0;
		
		foreach($ids as $v)
		{
			$data = ['is_deleted' => 1, 'deleted_by' => $user_id, 'deleted_at' => date('Y-m-d H:i:s')];
			if ($this->db->update('a_user', $data, ['id'=>$v]))
			{
				$return += 1;
			}
		}
		return $return;
	}
	
	function getRoleMenu($role_id)
	{
		$query = "select 
		am1.id as menu_id1, am1.role_id as role_id1, am1.name as name1, am1.is_parent as is_parent1, am1.url as url1, am1.icon as icon1, am1.is_readwrite as is_readwrite1, 
		am2.id as menu_id2, am2.role_id as role_id2, am2.name as name2, am2.is_parent as is_parent2, am2.url as url2, am2.icon as icon2, am2.is_readwrite as is_readwrite2, 
		am3.id as menu_id3, am3.role_id as role_id3, am3.name as name3, am3.is_parent as is_parent3, am3.url as url3, am3.icon as icon3, am3.is_readwrite as is_readwrite3
		from (
			select am.*, arm.role_id, arm.is_readwrite from a_role_menu arm left join a_menu am on am.id = arm.menu_id 
			where am.is_active = '1' and am.is_deleted = '0' and arm.is_active = '1' and arm.is_deleted = '0' and arm.role_id = $role_id
		) am1
		left join (
			select am.*, arm.role_id, arm.is_readwrite from a_role_menu arm left join a_menu am on am.id = arm.menu_id 
			where am.is_active = '1' and am.is_deleted = '0' and arm.is_active = '1' and arm.is_deleted = '0' and arm.role_id = $role_id
		) am2 on am1.id = am2.parent_id 
		left join (
			select am.*, arm.role_id, arm.is_readwrite from a_role_menu arm left join a_menu am on am.id = arm.menu_id 
			where am.is_active = '1' and am.is_deleted = '0' and arm.is_active = '1' and arm.is_deleted = '0' and arm.role_id = $role_id
		) am3 on am2.id = am3.parent_id 
		where am1.parent_id = '0'
		order by am1.line_no, am2.line_no, am3.line_no";
		
		/* $query = "select 
		am1.id as menu_id1, am1.role_id as role_id1, am1.name as name1, am1.is_parent as is_parent1, am1.url as url1, am1.is_readwrite as is_readwrite1, 
		am2.id as menu_id2, am2.role_id as role_id2, am2.name as name2, am2.is_parent as is_parent2, am2.url as url2, am2.is_readwrite as is_readwrite2, 
		am3.id as menu_id3, am3.role_id as role_id3, am3.name as name3, am3.is_parent as is_parent3, am3.url as url3, am3.is_readwrite as is_readwrite3
		from (
			select am.*, arm.role_id, arm.is_readwrite from a_role_menu arm left join a_menu_copy am on am.id = arm.menu_id 
			where am.is_active = '1' and am.is_deleted = '0' and arm.is_active = '1' and arm.is_deleted = '0' and arm.role_id = $role_id
		) am1
		left join (
			select am.*, arm.role_id, arm.is_readwrite from a_role_menu arm left join a_menu_copy am on am.id = arm.menu_id 
			where am.is_active = '1' and am.is_deleted = '0' and arm.is_active = '1' and arm.is_deleted = '0' and arm.role_id = $role_id
		) am2 on am1.id = am2.parent_id 
		left join (
			select am.*, arm.role_id, arm.is_readwrite from a_role_menu arm left join a_menu_copy am on am.id = arm.menu_id 
			where am.is_active = '1' and am.is_deleted = '0' and arm.is_active = '1' and arm.is_deleted = '0' and arm.role_id = $role_id
		) am3 on am2.id = am3.parent_id 
		where am1.parent_id = '0'
		order by am1.line_no, am2.line_no, am3.line_no"; */
		
		return $this->db->query($query)->result();
	}
	
	function createUserConfig($data)
	{
		return $this->db->insert('a_user_config', $data);
	}
	
	function updateUserConfig($data, $cond)
	{
		$this->db->update('a_user_config', $data, $cond);
		return $this->db->affected_rows();
	}
	
}