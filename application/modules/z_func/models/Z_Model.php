<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Z_Model extends CI_Model
{

	protected $errors;
	protected $error_start_delimiter;
	protected $error_end_delimiter;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->error_start_delimiter   = '<p>';
		$this->error_end_delimiter     = '</p>';
	}
	
	/* =================================================== 
	 *
	 * Function helper for DATATABLES
	 * 
	 */
	function mget_rec($params = NULL)
	{
		$this->db->select($params['select']);
		$this->db->from($params['table']);
		if ( array_key_exists('join', $params)) DBX::join($this, $params['join']);
		if ( array_key_exists('where', $params)) $this->db->where($params['where']);
		if ( array_key_exists('like', $params)) $this->db->where($params['like']);
		if ( array_key_exists('sort', $params)) $this->db->order_by($params['sort'], $params['order']);
		if ( array_key_exists('ob', $params)) 	$this->db->order_by($params['ob']);
		
		if ( array_key_exists('start', $params) && array_key_exists('length', $params) )
		{
			$this->db->limit($params['length'], $params['start']);
		}
		
		if ( array_key_exists('page', $params) && array_key_exists('rows', $params))
		{
			$params['page'] = empty($params['page']) ? 1 : $params['page'];
			$offset = ($params['page']-1)*$params['rows'];
			$this->db->limit($params['rows'], $offset);
		}
		return $this->db->get()->result();
	}
	
	/* function mget_rec_value($params = NULL)
	{
		$this->db->select($params['select']);
		$this->db->from($params['table']);
		if ( array_key_exists('join', $params)) DBX::join($this, $params['join']);
		if ( array_key_exists('where', $params)) $this->db->where($params['where']);
		if ( array_key_exists('like', $params)) $this->db->where($params['like']);
		if ( array_key_exists('sort', $params)) $this->db->order_by($params['sort'], $params['order']);
		if ( array_key_exists('ob', $params)) 	$this->db->order_by($params['ob']);
		if ( array_key_exists('page', $params) && array_key_exists('rows', $params))
		{
			$params['page'] = empty($params['page']) ? 1 : $params['page'];
			$offset = ($params['page']-1)*$params['rows'];
			$this->db->limit($params['rows'], $offset);
		}
		$res = $this->db->get()->result();
		foreach($res as $idx => $v){
			foreach($res[$idx] as $val){
				$row[] = $val;
			}
			$result[] = $row;
			$row = [];
		}
		return $result;
	} */
	
	function mget_rec_count($params = NULL)
	{
		$this->db->select($params['select']);
		$this->db->from($params['table']);
		if ( array_key_exists('join', $params)) DBX::join($this, $params['join']);
		if ( array_key_exists('where', $params)) $this->db->where($params['where']);
		if ( array_key_exists('like', $params)) $this->db->where($params['like']);
		$result = $this->db->get();
		$num_row = ($result->num_rows() > 0) ? $result->num_rows() : 0;
		
		if ( array_key_exists('start', $params) && array_key_exists('length', $params) )
		{
			$this->db->limit($params['length'], $params['start']);
		}
		
		if ( array_key_exists('page', $params) && array_key_exists('rows', $params))
		{
			$params['page'] = empty($params['page']) ? 1 : $params['page'];
			$offset = ($params['page']-1)*$params['rows'];
			$this->db->limit($params['rows'], $offset);
		}
		$result = $this->mget_rec($params);
		
		$response['total'] = $num_row;
		$response['rows']  = $result;
		return $response;
	}
	
	function mget_rec_tree($params)
	{ 
		if ( empty($params['id']) ) {
			$params['where']['parent_id'] = '0';
			$params['ob'] = 'line_no asc';
			$result = (array)$this->mget_rec($params);

			$results = array();
			foreach ( $result as $r ) {
				$r->state = ($this->mhas_child_tree( $params, $r->menu_id )) ? 'closed' : 'open';
				array_push($results, $r);
			}
		} else {
			$params['where']['parent_id'] = $params['id'];
			$params['ob'] = 'line_no asc';
			$result = $this->mget_rec($params);

			$results = array();
			foreach ( $result as $r ) {
				$r->state = ($this->mhas_child_tree( $params, $r->menu_id )) ? 'closed' : 'open';
				array_push($results, $r);
			}
		}
		
		return $results;
	}
	
	function mhas_child_tree($params, $id) 
	{
		$this->db->select('COUNT(*) AS rec_count');
		$this->db->from($params['table']);
		if ( array_key_exists('join', $params)) DBX::join($this, $params['join']);
		if ( array_key_exists('where', $params)) $this->db->where($params['where']);
		$this->db->where('parent_id', $id);
		// $this->db->where('is_deleted', 0);
		return ($this->db->get()->row()->rec_count > 0) ? TRUE : FALSE;
	}

	
	function re_sorting_tree($params=NULL){
		$rows = $this->db->order_by('sort_no', 'asc')->get_where( $params['table'], $params['where'] )->result();
		$i = 1;
		foreach ($rows as $row){
			$this->db->update( $params['table'], array('sort_no'=>$i), array('id'=>$row->id) );
			$i++;
		}
	}
	
	function update_relation_n_n( $table=NULL, $primary_field=NULL, $primary_value=NULL, $foreign_field=NULL, $foreign_values=NULL ) {

		$this->db->delete( $table, array($primary_field=>$primary_value));
		if ( !empty($foreign_values) ) {
			foreach ($foreign_values as $value) {	
				$this->db->insert( $table, array($primary_field=>$primary_value, $foreign_field=>$value));
			}
			return TRUE;
		}
		return FALSE;
	}
	
	function push_notification_email() {
		$qry = $this->db->get_where( 'notification_email', array('status'=>'created') );
		if ( $qry->num_rows() < 1)
			return FALSE;
	
		foreach ($qry->result() as $row) {
			$result = send_mail($row->email, $row->subject, $row->message);
			if ( $result ) 
				$this->db->update( 'notification_email', array('status'=>'sent', 'sent'=>date('Y-m-d H:i:s')), array('id'=>$row->id) );
			else
				$this->db->update( 'notification_email', array('status'=>'failed', 'sent'=>date('Y-m-d H:i:s')), array('id'=>$row->id) );
		}
		return TRUE;
	}
	
	function get_notif_note() {
		
		$notif = $this->db->get( 'notif' );
		if ( $notif->num_rows() < 1 )
			return FALSE;
			
		return $notif->row()->note;
	}
	
	function get_document_sign( $company_id, $branch_id, $department_id, $doc_code ) {
		$filter['company_id'] 	 = $company_id;
		$filter['branch_id'] 	 = $branch_id;
		$filter['department_id'] = $department_id;
		$filter['code'] 	 	 = $doc_code;
		
		$qry = $this->db->get_where( 'setup_documents', $filter );
		if ($qry->num_rows() < 1) {
			$data['sign1'] = NULL;
			$data['sign2'] = NULL;
			$data['sign3'] = NULL;
		} else {
			$row = $qry->row();
			$data['sign1'] = $row->sign1;
			$data['sign2'] = $row->sign2;
			$data['sign3'] = $row->sign3;
		}
		
		return $data;
	}
	
	function is_duplicate_code( $table=NULL, $code=NULL ) {
		return empty($this->db->get_where($table, array('code'=>$code, 'deleted'=>0), 1)->row()->id) ? FALSE : TRUE;
	}
	
	function is_duplicate_username( $table=NULL, $username=NULL ) {
		return empty($this->db->get_where($table, array('username'=>$username), 1)->row()->id) ? FALSE : TRUE;
	}
	
	function is_customer_exists( $company_id=NULL, $customer_id=NULL ) {
		$qry = $this->db->get_where( 'customer', array('id'=>intval($customer_id), 'company_id'=>$company_id) );
		return ($qry->num_rows() < 1) ? FALSE : TRUE;
		if ( $qry->num_rows() < 1 ) 
			return FALSE;
		else
			return TRUE;
	}
	
	function is_data_exists_on( $table=NULL, $fields=NULL, $search_value=NULL ) {
		$f = array();
		foreach ( $fields as $field ) {
			$f[$field] = $search_value;
		}
		return empty($this->db->get_where($table, $f, 1)->row()->id) ? FALSE : TRUE;
	}
	
	function updateTotalAmount($table, $id)
	{
		$filter['id'] = $id;
		$qry = $this->db->get_where( $table, $filter );
		foreach ($qry->result() as $row) 
		{
			$this->db->select_sum('amount', 'total_amount');
			$this->db->where($table.'_id', $row->id);
			// $this->db->where('void', 0);
			$summary = $this->db->get($table.'_dt')->row();

			$data1['total_amount'] = $summary->total_amount;
			$this->db->update( $table, $data1, $filter );
		}
		return;
	}

}