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
	
	function getUserAuthentication($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "au.*" : $params['select'];
		$params['table'] 	= "a_user as au";
		$params['join'][] 	= ['a_client as ac', 'au.client_id = ac.id', 'left'];
		$params['join'][] 	= ['a_org as ao', 'au.org_id = ao.id', 'left'];
		$params['join'][] 	= ['a_role as ar', 'au.role_id = ar.id', 'left'];
		$params['where']['au.is_deleted'] 	= '0';
		
		return $this->mget_rec($params);
	}
	
	function getUser($params)
	{
		$select = "au.id,au.client_id,au.org_id,au.role_id,au.is_active,au.is_deleted,
			au.created_by,au.updated_by,au.deleted_by,au.created_at,au.updated_at,au.deleted_at,
			au.name,au.description,au.email,au.last_login,au.is_online,au.supervisor_id,
			au.bpartner_id,au.is_fullbpaccess,au.is_expired,au.security_question,au.security_answer,
			au.ip_address,au.photo_url,ao.name as org_name, ar.name as role_name, au4.name as supervisor_name,
			au1.name as _created_by, au2.name as _updated_by, au3.name as _deleted_by";
		$params['select']	= array_key_exists('select', $params) ? $params['select'] : $select;
		$params['table'] 	= "a_user as au";
		$params['join'][] 	= ['a_client as ac', 'au.client_id = ac.id', 'left'];
		$params['join'][] 	= ['a_org as ao', 'au.org_id = ao.id', 'left'];
		$params['join'][] 	= ['a_role as ar', 'au.role_id = ar.id', 'left'];
		$params['join'][] 	= ['a_user as au1', 'au.created_by = au1.id', 'left'];
		$params['join'][] 	= ['a_user as au2', 'au.updated_by = au2.id', 'left'];
		$params['join'][] 	= ['a_user as au3', 'au.deleted_by = au3.id', 'left'];
		$params['join'][] 	= ['a_user as au4', 'au.supervisor_id = au4.id', 'left'];
		$params['where']['au.is_deleted'] 	= '0';
		
		return $this->mget_rec_count($params);
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
	
	function getMenu($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "am.*" : $params['select'];
		$params['table'] 	= "a_role_menu arm";
		$params['join'][] 	= ['a_menu am', 'am.id = arm.menu_id', 'left'];
		$params['where']	= "am.is_active = '1' and am.is_deleted = '0' and arm.is_active = '1' and arm.is_deleted = '0' and am.is_parent = '0'";
		$params['order']	= "am.name";
		
		return $this->mget_rec($params);
	}
	
	function getRole($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "au.*" : $params['select'];
		$params['table'] 	= "a_user as au";
		$params['join'][] 	= ['a_client as ac', 'au.client_id = ac.id', 'left'];
		$params['join'][] 	= ['a_org as ao', 'au.org_id = ao.id', 'left'];
		$params['join'][] 	= ['a_role as ar', 'au.role_id = ar.id', 'left'];
		$params['where']['au.is_deleted'] 	= '0';
		
		return $this->mget_rec($params);
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
	
	function getRoleDashboard($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "ad.*, ard.role_id, ard.is_readwrite" : $params['select'];
		$params['table'] 	= "a_role_dashboard ard";
		$params['join'][] 	= ['a_dashboard ad', 'ad.id = ard.dashboard_id', 'left'];
		$params['where']	= "ad.is_active = '1' and ad.is_deleted = '0' and ard.is_active = '1' and ard.is_deleted = '0'";
		$params['order']	= "ad.type, ad.lineno";
		
		return $this->mget_rec($params);
	}
	
	function createUserRecent($data)
	{
		/* $qry = $this->db
			   ->select('*')
			   ->from('a_user_recent')
			   ->where($data)
			   ->limit(1)
			   ->order_by('id desc'); */
		$qry = $this->db->order_by('id desc')->get_where('a_user_recent', $data, 1);
		if ($qry->num_rows() > 0)
			return TRUE;
		
		return $this->db->insert('a_user_recent', $data);
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
	
	function getInfo($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "ai.*" : $params['select'];
		$params['table'] 	= "a_info as ai";
		$params['where']['ai.is_deleted'] 	= '0';
		
		return $this->mget_rec_count($params);
	}
	
	function getCountry($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "*" : $params['select'];
		$params['table'] 	= "c_1country";
		
		return $this->mget_rec_count($params);
	}
	
	function getProvince($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "*" : $params['select'];
		$params['table'] 	= "c_2province";
		
		return $this->mget_rec_count($params);
	}
	
	function getCity($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "*" : $params['select'];
		$params['table'] 	= "c_3city";
		
		return $this->mget_rec_count($params);
	}
	
	function getDistrict($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "*" : $params['select'];
		$params['table'] 	= "c_4district";
		
		return $this->mget_rec_count($params);
	}
	
	function getVillage($params)
	{
		$params['select']	= !array_key_exists('select', $params) ? "*" : $params['select'];
		$params['table'] 	= "c_5village";
		
		return $this->mget_rec_count($params);
	}
	
}