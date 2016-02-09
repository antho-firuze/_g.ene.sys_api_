<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MYREST_Controller extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

	}
	
    /**
     * Requests are not made to methods directly, the request will be for
     * an "object". This simply maps the object and method to the correct
     * Controller method
     *
     * @access public
     * @param  string $object_called
     * @param  array $arguments The arguments passed to the controller method
     */
    public function _remap($object_called, $arguments)
    {
        // Should we answer if not over SSL?
        if ($this->config->item('force_https') && $this->request->ssl === FALSE)
        {
            $this->response([
                    $this->config->item('rest_status_field_name') => FALSE,
                    $this->config->item('rest_message_field_name') => $this->lang->line('text_rest_unsupported')
                ], self::HTTP_FORBIDDEN);
        }

        // Remove the supported format from the function name e.g. index.json => index
        $object_called = preg_replace('/^(.*)\.(?:' . implode('|', array_keys($this->_supported_formats)) . ')$/', '$1', $object_called);

        $controller_method = $object_called . '_' . $this->request->method;

        // Do we want to log this method (if allowed by config)?
        $log_method = !(isset($this->methods[$controller_method]['log']) && $this->methods[$controller_method]['log'] === FALSE);

        // Use keys for this method?
        $use_key = !(isset($this->methods[$controller_method]['key']) && $this->methods[$controller_method]['key'] === FALSE);

        // They provided a key, but it wasn't valid, so get them out of here
        if ($this->config->item('rest_enable_keys') && $use_key && $this->_allow === FALSE)
        {
            if ($this->config->item('rest_enable_logging') && $log_method)
            {
                $this->_log_request();
            }

            $this->response([
                    $this->config->item('rest_status_field_name') => FALSE,
                    $this->config->item('rest_message_field_name') => sprintf($this->lang->line('text_rest_invalid_api_key'), $this->rest->key)
                ], self::HTTP_FORBIDDEN);
        }

        // Check to see if this key has access to the requested controller
        if ($this->config->item('rest_enable_keys') && $use_key && empty($this->rest->key) === FALSE && $this->_check_access() === FALSE)
        {
            if ($this->config->item('rest_enable_logging') && $log_method)
            {
                $this->_log_request();
            }

            $this->response([
                    $this->config->item('rest_status_field_name') => FALSE,
                    $this->config->item('rest_message_field_name') => $this->lang->line('text_rest_api_key_unauthorized')
                ], self::HTTP_UNAUTHORIZED);
        }

        // Sure it exists, but can they do anything with it?
        if (method_exists($this, $controller_method) === FALSE)
        {
            $this->response([
                    $this->config->item('rest_status_field_name') => FALSE,
                    $this->config->item('rest_message_field_name') => $this->lang->line('text_rest_unknown_method')
                ], self::HTTP_NOT_FOUND);
        }

        // Doing key related stuff? Can only do it if they have a key right?
        if ($this->config->item('rest_enable_keys') && empty($this->rest->key) === FALSE)
        {
            // Check the limit
            if ($this->config->item('rest_enable_limits') && $this->_check_limit($controller_method) === FALSE)
            {
                $response = [$this->config->item('rest_status_field_name') => FALSE, $this->config->item('rest_message_field_name') => $this->lang->line('text_rest_api_key_time_limit')];
                $this->response($response, self::HTTP_UNAUTHORIZED);
            }

            // If no level is set use 0, they probably aren't using permissions
            $level = isset($this->methods[$controller_method]['level']) ? $this->methods[$controller_method]['level'] : 0;

            // If no level is set, or it is lower than/equal to the key's level
            $authorized = $level <= $this->rest->level;

            // IM TELLIN!
            if ($this->config->item('rest_enable_logging') && $log_method)
            {
                $this->_log_request($authorized);
            }

            // They don't have good enough perms
            $response = [$this->config->item('rest_status_field_name') => FALSE, $this->config->item('rest_message_field_name') => $this->lang->line('text_rest_api_key_permissions')];
            $authorized || $this->response($response, self::HTTP_UNAUTHORIZED);
        }

        // No key stuff, but record that stuff is happening
        elseif ($this->config->item('rest_enable_logging') && $log_method)
        {
            $this->_log_request($authorized = TRUE);
        }

        // Call the controller method and passed arguments
        try
        {
            call_user_func_array([$this, $controller_method], $arguments);
        }
        catch (Exception $ex)
        {
            // If the method doesn't exist, then the error will be caught and an error response shown
            $this->response([
                    $this->config->item('rest_status_field_name') => FALSE,
                    $this->config->item('rest_message_field_name') => [
                        'classname' => get_class($ex),
                        'message' => $ex->getMessage()
                    ]
                ], self::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

	/* public function _remap($method)
	{
		$jwt = $this->input->get_request_header('Token-Key');
		try {
			$data = JWT::checkToken($jwt);
		} catch (Exception $e) {
			return json_output(["error" => $e->getMessage()], 401); 
		}
		
		$this->userId = $data->userId;
		$this->$method();
	}
	*/
	
    protected function _force_login($nonce = '')
    {
        $rest_auth = $this->config->item('rest_auth');
        $rest_realm = $this->config->item('rest_realm');
        if (strtolower($rest_auth) === 'basic')
        {
            // See http://tools.ietf.org/html/rfc2617#page-5
            header('WWW-Authenticate: Basic realm="' . $rest_realm . '"');
        }
        elseif (strtolower($rest_auth) === 'digest')
        {
            // See http://tools.ietf.org/html/rfc2617#page-18
            header(
                'WWW-Authenticate: Digest realm="' . $rest_realm
                . '", qop="auth", nonce="' . $nonce
                . '", opaque="' . md5($rest_realm) . '"');
        }

        // Display an error response
        $this->response([
                $this->config->item('rest_status_field_name') => FALSE,
                $this->config->item('rest_message_field_name') => $this->lang->line('text_rest_unauthorized')
            ], self::HTTP_UNAUTHORIZED);
    }

	public function response($response=array(), $statusHeader=200)
	{
		$statusHeader = empty($statusHeader) ? 200 : $statusHeader;
		if (! is_numeric($statusHeader))
			show_error('Status codes must be numeric', 500);
		
		$response = is_array($response) ? $response : array($response);
		
		header("HTTP/1.0 $statusHeader");
		header('Content-Type: application/json');
		echo json_encode($response);
		exit();
	}

	function xresponse($status=TRUE, $response=array(), $statusHeader=200)
	{
		$statusHeader = empty($statusHeader) ? 200 : $statusHeader;
		if (! is_numeric($statusHeader))
			show_error('Status codes must be numeric', 500);
		
		$response = is_array($response) ? $response : array($response);
		
		// CREATE TOKEN
		$this->load->library('z_jwt/jwt');
		try {
			$token['Token-Key'] = $this->jwt->createToken($GLOBALS['identifier']);
			
		} catch (Exception $e) {
			$this->response(['status' => FALSE, 'error' => $e->getMessage()], 500);
		}
		
		header("HTTP/1.0 $statusHeader");
		header('Content-Type: application/json');
		echo json_encode(array_merge(['status' => $status], $token, $response));
		exit();
	}
	
	function _check_token()
	{
		$this->load->library('z_jwt/jwt');
		
		$jwt = $this->input->server('HTTP_TOKEN_KEY');
		try {
			$data = $this->jwt->checkToken($jwt);
			$GLOBALS['identifier']['id'] = $data->id;
		} catch (Exception $e) {
			$this->response(['status' => FALSE, 'error' => $e->getMessage()], 401);
		}
		return $data;
	}
	
}