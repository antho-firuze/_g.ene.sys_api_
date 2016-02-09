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
		$params['table'] 	= "a_user as au";
		$params['join'][] 	= ['a_user_config as auc', 'au.id = auc.user_id', 'left'];
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
}