<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/modules/z_rest/libraries/REST_Controller.php';

class System extends REST_Controller {
	
	function __construct() {
		parent::__construct();
		
		$this->load->model('api/system_model');
	}
	
	function authentication_get()
	{
		$this->load->library('z_auth/auth');
		
		// CHECK X-API-KEY HEADER
		$apps_key  	= $this->input->get_request_header('X-API-KEY');
		if (! $this->db->where('api_token', $apps_key)->get('a_system')->row() ) 
			$this->response(['status' => FALSE, 'message' => 'What are you doing...?'], 400);

		// BASIC AUTH
        $username = $this->input->server('PHP_AUTH_USER');
        $http_auth = $this->input->server('HTTP_X_AUTH');
        $password = NULL;
        if ($username !== NULL)
        {
            $password = $this->input->server('PHP_AUTH_PW');
        }
        elseif ($http_auth !== NULL)
        {
            if (strpos(strtolower($http_auth), 'basic') === 0)
            {
                list($username, $password) = explode(':', base64_decode(substr($http_auth, 6)));
            }
        }
		if (! $id = $this->auth->login($username, $password))
		{
			$this->response(['status' => FALSE, 'message' => $this->auth->errors()], 401);
		}
		
		// User Data
		// $user = $this->db->get_where('a_user', ['id'=>$id])->row();
		$params['select'] = 'au.id, au.client_id, au.org_id, au.role_id, au.name, au.description, au.email, 
			au.photo_url, ac.name as client_name, ao.name as org_name, ar.name as role_name';
		$params['where']['au.id'] = $id;
		$user = (object) $this->system_model->getUserAuthentication($params)[0];
		$dataUser = [
			'name'			=> $user->name,
			'description'	=> $user->description,
			'email'			=> $user->email,
			'client_name'	=> $user->client_name,
			'org_name'		=> $user->org_name,
			'role_name'		=> $user->role_name,
			// 'photo_url' 	=> empty($user->photo_url) ? urlencode('http://lorempixel.com/160/160/people/') : urlencode($user->photo_url),
			'photo_url' 	=> urlencode($user->photo_url),
		];
		
		$userConfig = (object) $this->system_model->getUserConfig([
			'select' => 'attribute, value', 
			'where' => ['user_id' => $id]
		]);
		$dataConfig = [];
		foreach($userConfig as $k => $v)
			$dataConfig[$v->attribute] = $v->value;
		
		$GLOBALS['identifier'] = [
			'user_id' 	=> $id,
			'client_id'	=> $user->client_id,
			'org_id'	=> $user->org_id,
			'role_id'	=> $user->role_id,
		];
		
		$data = array_merge($GLOBALS['identifier'], $dataUser, $dataConfig);
		// $this->load->library('encryption');
		// $data['authentication'] = $this->encryption->encrypt(json_encode($GLOBALS['identifier']));
		$result['data'] = urlsafeB64Encode(json_encode($data));
		$this->xresponse(TRUE, $result);
	}
	
	function unlockscreen_get()
	{
		$sess = $this->_check_token();
		
		$this->load->library('z_auth/auth');
		
		// BASIC AUTH
        $username = $this->input->server('PHP_AUTH_USER');
        $http_auth = $this->input->server('HTTP_X_AUTH');
        $password = NULL;
        if ($username !== NULL)
        {
            $password = $this->input->server('PHP_AUTH_PW');
        }
        elseif ($http_auth !== NULL)
        {
            if (strpos(strtolower($http_auth), 'basic') === 0)
            {
                list($username, $password) = explode(':', base64_decode(substr($http_auth, 6)));
            }
        }
		if (! $id = $this->auth->login($username, $password))
		{
			$this->response(['status' => FALSE, 'message' => $this->auth->errors()], 401);
		}
		
		$this->xresponse(TRUE, []);
	}
	
	function change_passwd_post()
	{
		$sess = $this->_check_token();
		
		$data = (object) $this->post();
		
		$this->load->library('z_auth/auth');
		
		// BASIC AUTH
        $username = $this->input->server('PHP_AUTH_USER');
        $http_auth = $this->input->server('HTTP_X_AUTH');
        $password = NULL;
        if ($username !== NULL)
        {
            $password = $this->input->server('PHP_AUTH_PW');
        }
        elseif ($http_auth !== NULL)
        {
            if (strpos(strtolower($http_auth), 'basic') === 0)
            {
                list($username, $password) = explode(':', base64_decode(substr($http_auth, 6)));
            }
        }
		
		if (! $this->auth->change_password($username, $password, $data->password_new))
		{
			$this->response(['status' => FALSE, 'message' => $this->auth->errors()], 401);
		}
		
		$this->xresponse(TRUE, ['message' => $this->auth->messages()]);
	}
	
	function reset_passwd_post()
	{
		$sess = $this->_check_token();
		
		$data = (object) $this->post();
		
		$this->load->library('z_auth/auth');
		
		// BASIC AUTH
        $username = $this->input->server('PHP_AUTH_USER');
        $http_auth = $this->input->server('HTTP_X_AUTH');
        $password = NULL;
        if ($username !== NULL)
        {
            $password = $this->input->server('PHP_AUTH_PW');
        }
        elseif ($http_auth !== NULL)
        {
            if (strpos(strtolower($http_auth), 'basic') === 0)
            {
                list($username, $password) = explode(':', base64_decode(substr($http_auth, 6)));
            }
        }
		
		if (! $this->auth->reset_password($username, $password))
		{
			$this->response(['status' => FALSE, 'message' => $this->auth->errors()], 401);
		}
		
		$this->xresponse(TRUE, ['message' => $this->auth->messages()]);
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
		$params = (array) $arg;
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
		
		// $params['select'] = !empty($arg->fs) ? $arg->fs : 'au.id,au.name,au.description,au.email,';
		// $params['page'] = empty($arg->p) ? 1 : $arg->p;
		// $params['rows'] = empty($arg->r) ? 10 : $arg->r;
		
		// $params['sort'] = empty($arg->s) ? 'au.id' : $arg->s;
		// $params['order'] = empty($arg->o) ? 'desc' : $arg->o;
		// $params['ob'] = empty($arg->ob) ? '' : $arg->ob;
		// log_message('error', $params['length'].$params['start']);
		// log_message('error', $arg->length);
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
			'created_by'	=> $sess->user_id,
			'created_at'	=> date('Y-m-d H:i:s')
		];
		
		$this->load->library('z_auth/auth');
		if (! $id = $this->auth->register($data->username, $data->password, $data->email, $additional_data))
		{
			$this->xresponse(FALSE, ['message' => $this->auth->errors()], 401);
		}

		$this->xresponse(TRUE, ['message' => $this->lang->line('success_saving')]);
	}
	
	function user_put()
	{
		$sess = $this->_check_token();
		
		$arg = (object) $this->input->get();
		if (empty($arg->id))
		{
			$this->xresponse(FALSE, NULL, 400);
		}
		
		$data = (object) $this->put();
		
		$fields = ['name', 'description', 'email', 'is_active', 'is_fullbpaccess', 'supervisor_id'];
		foreach($fields as $f){
			if (array_key_exists($f, $data)){
				$datas[$f] = $data->{$f};
			}
		}
		// CHECK VALIDATIONS
		$datas['is_active'] 			= empty($datas['is_active']) ? 0 : 1; 
		$datas['is_fullbpaccess'] = empty($datas['is_fullbpaccess']) ? 0 : 1; 
		$datas['supervisor_id'] 	= ($datas['supervisor_id']=='') ? NULL : $datas['supervisor_id']; 
		
		$datas['updated_by'] = $sess->user_id;
		$datas['updated_at'] = date('Y-m-d H:i:s');
		if (! $this->system_model->updateUser($datas, ['id'=>$arg->id]))
			$this->xresponse(FALSE, ['message' => $this->db->error()->message], 401);
		
		// log_message('error', $data->name .':'. $data->password);
		if (! empty($data->password)){
			$this->load->library('z_auth/auth');
			if (! $this->auth->reset_password($data->name, $data->password))
				$this->response(['status' => FALSE, 'message' => $this->auth->errors()], 401);
		}
		
		$this->xresponse(TRUE, ['message' => $this->lang->line('success_update')]);
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
		
		$this->xresponse(TRUE, ['message' => $this->lang->line('success_delete')]);
	}

	function userlist_get()
	{
		$sess = $this->_check_token();
		
		$id = $this->input->get('id');
		$id = is_numeric($id) ? $id : -1;
		
		$result['data'] = [];
		
		$params['where']['au.client_id'] = $sess->client_id;
		$params['where']['au.org_id'] 	 = $sess->org_id;
		if ($id >= 0)
		{
			$params['where']['au.id'] 	 = $id;
		}
		$result['data'] = $this->system_model->getUser($params);
		$this->xresponse(TRUE, $result);
	}
	
	function userRecent_post()
	{
		$sess = $this->_check_token();
		
		$params = (object) $this->post();
		
		$result['data'] = [];
		$data = [
			'user_id'	=> $sess->user_id,
			'value'		=> $params->value
		];
		
		$this->system_model->createUserRecent($data);

		$this->xresponse(TRUE, $result);
	}
	
	function userUploadAvatar_post()
	{
		
	}
	
	function menulist_get()
	{
		$sess = $this->_check_token();
		
		$arg = (object) $this->input->get();
		
		$result['data'] = [];
		
		$params['select'] = "am.name, am.url, am.icon";
		if (! empty($arg->q)) 
		{
			$params['like'] = empty($arg->sf) 
				? DBX::like_or('am.name', $arg->q)
				: DBX::like_or($arg->sf, $arg->q);
			
		}
		
		$result['data'] = $this->system_model->getMenu($params);
		$this->xresponse(true, $result);
	}
	
	function role_get()
	{
		$sess = $this->_check_token();
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
	
	function role_post()
	{
		
	}
	
	function role_put()
	{
		
	}
	
	function role_delete()
	{
		
	}
	
	function rolemenu_get()
	{
		$sess = $this->_check_token();
		
		$arg = (object) $this->input->get();
		
		$result['data'] = [];
		if (! empty($arg->id))
		{
			$result['data'] = $this->system_model->getRoleMenu($arg->id);
		}
		$this->xresponse(TRUE, $result);
	}
	
	function roledashboard_get()
	{
		$sess = $this->_check_token();
		
		$arg = (object) $this->input->get();
		
		$result['data'] = [];
		if (! empty($arg->id))
		{
			$params['where']['ard.role_id'] = $arg->id;
			$result['data'] = $this->system_model->getRoleDashboard($params);
		}
		$this->xresponse(TRUE, $result);
	}
	
	function userConfig_post()
	{
		$sess = $this->_check_token();
		
		$data = (object) $this->post();
		
		$return = 0; 
		$result['data'] = [];
		foreach($data as $key => $value)
		{
			$cond = ['user_id' => $sess->user_id, 'attribute' => $key];
			$qry = $this->db->get_where('a_user_config', $cond, 1);
			if ($qry->num_rows() < 1)
			{
				$data = array_merge($cond, ['value' => $value]);
				$this->system_model->createUserConfig($data);
				$return++;
			}
			else
			{
				if ($arow = $this->system_model->updateUserConfig(['value' => $value], $cond))
				{
					$return += $arow;
				}
			}
		}
		$this->xresponse(TRUE, $result);
	}
	
	function info_get()
	{
		$sess = $this->_check_token();
		
		$arg = (object) $this->input->get();
		$params = (array) $arg;
		
		// $params['where']['ai.client_id'] = $sess->client_id;
		// $params['where']['ai.org_id'] 	 = $sess->org_id;
		if (! empty($arg->id))
		{
			$params['where']['ai.id'] = $arg->id;
		}
		if (! empty($arg->q)) 
		{
			$params['like'] = empty($arg->sf) 
				? DBX::like_or('ai.description', $arg->q)
				: DBX::like_or($arg->sf, $arg->q);
		}
		if (! empty($arg->validf)) 
		{
			$params['where']['ai.valid_from <='] = $arg->validf;
		}
		$result['data'] = $this->system_model->getInfo($params);
		$this->xresponse(TRUE, $result);
	}
	
	function infolist_get()
	{
		$sess = $this->_check_token();
		
		$params['where']['ai.client_id'] = $sess->client_id;
		$params['where']['ai.org_id'] 	 = $sess->org_id;
		$params['where']['ai.valid_from <='] = datetime_db_format();
		$result['data'] = $this->system_model->getInfo($params);
		$this->xresponse(TRUE, $result);
	}
	
	function countrylist_get()
	{
		$sess = $this->_check_token();
		
		$params = $this->input->get();
		
		if (key_exists('q', $params)) 
			$params['like'] = empty($params['sf']) 
				? DBX::like_or('name', $params['q'])
				: DBX::like_or($params['sf'], $params['q']);
		if (key_exists('id', $params)) 
			$params['where']['id'] = $params['id'];
		
		$result['data'] = $this->system_model->getCountry($params);
		$this->xresponse(TRUE, $result);
	}
	
	function provincelist_get()
	{
		$sess = $this->_check_token();
		
		$params = $this->input->get();
		
		if (key_exists('q', $params)) 
			$params['like'] = empty($params['sf']) 
				? DBX::like_or('name', $params['q'])
				: DBX::like_or($params['sf'], $params['q']);
		if (key_exists('id', $params)) 
			$params['where']['id'] = $params['id'];
		if (key_exists('country_id', $params)) 
			$params['where']['country_id'] = $params['country_id'];
		
		$result['data'] = $this->system_model->getProvince($params);
		$this->xresponse(TRUE, $result);
	}
	
	function citylist_get()
	{
		$sess = $this->_check_token();
		
		$params = $this->input->get();
		
		if (key_exists('q', $params)) 
			$params['like'] = empty($params['sf']) 
				? DBX::like_or('name', $params['q'])
				: DBX::like_or($params['sf'], $params['q']);
		if (key_exists('id', $params)) 
			$params['where']['id'] = $params['id'];
		if (key_exists('province_id', $params)) 
			$params['where']['province_id'] = $params['province_id'];
		
		$result['data'] = $this->system_model->getCity($params);
		$this->xresponse(TRUE, $result);
	}
	
	function districtlist_get()
	{
		$sess = $this->_check_token();
		
		$params = $this->input->get();
		
		if (key_exists('q', $params)) 
			$params['like'] = empty($params['sf']) 
				? DBX::like_or('name', $params['q'])
				: DBX::like_or($params['sf'], $params['q']);
		if (key_exists('id', $params)) 
			$params['where']['id'] = $params['id'];
		if (key_exists('city_id', $params)) 
			$params['where']['city_id'] = $params['city_id'];
		
		$result['data'] = $this->system_model->getDistrict($params);
		$this->xresponse(TRUE, $result);
	}
	
	function villagelist_get()
	{
		$sess = $this->_check_token();
		
		$params = $this->input->get();
		
		if (key_exists('q', $params)) 
			$params['like'] = empty($params['sf']) 
				? DBX::like_or('name', $params['q'])
				: DBX::like_or($params['sf'], $params['q']);
		if (key_exists('id', $params)) 
			$params['where']['id'] = $params['id'];
		if (key_exists('district_id', $params)) 
			$params['where']['district_id'] = $params['district_id'];
		
		$result['data'] = $this->system_model->getVillage($params);
		$this->xresponse(TRUE, $result);
	}
	
	function rolemenutest_get()
	{
		$arg = (object) $this->input->get();
		
		if (! empty($arg->id))
		{
			$result['data'] = $this->system_model->getRoleMenu($arg->id);
			$this->response($result);
		}
		$this->response([], 401);
	}
	
	function test_get()
	{
		$userConfig = (object) $this->system_model->getUserConfig([
			'select' => 'attribute, value', 
			'where' => ['user_id' => 11]
		]);
		foreach($userConfig as $k => $v)
			$data->{$v->attribute} = $v->value;

		return out($data);
	}

}
