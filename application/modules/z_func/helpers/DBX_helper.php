<?php defined('BASEPATH') OR exit('No direct script access allowed');

class DBX
{
	// $query = DBX::like_or('a, b, c', 'search value');
	public static function like_or($fields, $q)
	{
		$q = strtolower($q);
		foreach (explode(',', $fields) as $v)
		{
			$v = trim($v);
			$like[] =  "lower({$v}) LIKE '%{$q}%' ESCAPE '!'";
		}
		return '('.implode(' OR ', $like).')';
	}
	
	// DBX::join($this, $params['join']);
	public static function join($ci, $joins)
	{
		foreach($joins as $arbs)
		{
			$type = NULL;
			if (count($arbs)>2)
				list($table, $cond, $type) = $arbs;
			else
				list($table, $cond) = $arbs;
				
			$ci->db->join($table, $cond, $type);
		}
	}
	
	/* 
	// NOT IN USE
	public static function or_like($fields, $q)
	{
		foreach (explode(',', $fields) as $v)
		{
			$like[$v] =  $q;
		}
		return $like;
	} */
}