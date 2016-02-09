<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/modules/z_rest/libraries/REST_Controller.php';

class System extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		
		$this->load->model('api/system_model');
	}
	
	function login_get()
	{
		$this->load->library('z_auth/auth');
		
		// ADDITIONAL KEY
		$apps_key  	= $this->input->get_request_header('Application-Key');
		if (! $result = $this->db->where('api_token', $apps_key)->get('a_system')->row() ) 
			$this->response(['status' => FALSE, 'error' => 'What are you doing...?'], 400);

		// BASIC AUTH
        $username = $this->input->server('PHP_AUTH_USER');
        $http_auth = $this->input->server('HTTP_AUTHENTICATION');
        $password = NULL;
        if ($username !== NULL)
        {
            $password = $this->input->server('PHP_AUTH_PW');
        }
        elseif ($http_auth !== NULL)
        {
            if (strpos(strtolower($http_auth), 'basic') === 0)
            {
                list($username, $password) = explode(':', base64_decode(substr($this->input->server('HTTP_AUTHORIZATION'), 6)));
            }
        }
		if (! $id = $this->auth->login($username, $password))
			$this->response(['status' => FALSE, 'error' => 'Incorrect Username & Password !'], 401);
		
		$user = $this->db->get_where('a_user', ['id'=>$id])->row();
		$GLOBALS['identifier'] = [
			'user_id' 	=> $id,
			'client_id'	=> $user->client_id,
			'org_id'	=> $user->org_id,
			'role_id'	=> $user->role_id
		];
		
		$this->xresponse(TRUE);
	}
	
	function cektoken_get()
	{
		$sess['session'] = $this->_check_token();
		
		$this->xresponse(TRUE, $sess);
	}
	
	function user_get()
	{
		// GET FROM TOKEN :
		// $sess->user_id 
		// $sess->client_id 
		// $sess->org_id 
		// $sess->role_id 
		$sess = $this->_check_token();
		
		// ============================
		// 		QUERY PARAMETERS
		// ============================
		$arg = (object) $this->input->get();
		if (! empty($arg->id))
		{
			$params['where']['au.id'] = $arg->id;
		}
		
		if (! empty($arg->q)) 
		{
			$params['like'] = empty($arg->sf) 
				? DBX::like_or('au.name, au.description', $arg->q)
				: DBX::like_or($arg->sf, $arg->q);
		}
		
		$params['select'] = !empty($arg->fs) ? $arg->fs : 'au.name,au.description';
		$params['page'] = empty($arg->p) ? 1 : $arg->p;
		$params['rows'] = empty($arg->r) ? 10 : $arg->r;
		
		$params['sort'] = empty($arg->s) ? 'au.id' : $arg->s;
		$params['order'] = empty($arg->o) ? 'desc' : $arg->o;
		$params['ob'] = empty($arg->ob) ? '' : $arg->ob;
		
		$result['data'] = $this->system_model->getUser($params);
		$this->xresponse(TRUE, $result);
	}
	
	function user_post()
	{
		$sess = $this->_check_token();
		
		// Content-Type: application/json
		// Content-Type: application/x-www-form-urlencoded 
		$data = (object) $this->post();
		
		$additional_data = [
			'client_id'		=> $sess->client_id,
			'api_token'		=> get_api_sig(),
			'created_by'	=> $sess->user_id,
			'created_at'	=> date('Y-m-d H:i:s')
		];
		
		$this->load->library('z_auth/auth');
		if (! $id = $this->auth->register($data->username, $data->password, $data->email, $additional_data))
		{
			$this->xresponse(FALSE, ['message' => $this->auth->errors()], 401);
		}

		$this->xresponse(TRUE);
	}
	
	function user_put()
	{
		$sess = $this->_check_token();
		
		$arg = (object) $this->input->get();
		if (empty($arg->id))
		{
			$this->xresponse(FALSE, NULL, 400);
		}
		
		// Content-Type: application/json
		// Content-Type: application/x-www-form-urlencoded 
		$data = (object) $this->put();
		
		$data->updated_by = $sess->user_id;
		$data->updated_at = date('Y-m-d H:i:s');
		
		if (! $this->system_model->updateUser($data, ['id'=>$arg->id]))
			$this->xresponse(FALSE, ['error' => $this->db->error()->message], 401);
			
		$this->xresponse(TRUE);
	}
	
	function user_delete()
	{
		$sess = $this->_check_token();
		
		$arg = (object) $this->input->get();
		if (empty($arg->id))
		{
			$this->xresponse(FALSE, NULL, 400);
		}
		
		if (! $this->system_model->deleteUser($arg->id, $sess->user_id))
		{
			$this->xresponse(FALSE, ['message' => $this->system_model->errors()], 401);
		}
		
		$this->xresponse(TRUE);
	}

	function test(){
		// $this->db->
	}

}
