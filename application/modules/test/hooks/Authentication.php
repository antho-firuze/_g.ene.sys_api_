<?php if ( !defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );
 
class Authentication {
    
    function validate_in() {
		$ci = &get_instance();
		$ci->load->config('jwt_auth/jwt_auth');
		
        $request = strstr( $_SERVER['REQUEST_URI'], 'api' );
		$parts = explode('/', $request);
		if ( empty($parts[0]) ) {
			// show_404();
			// if (! $ci->ion_auth->logged_in() )
				// redirect('login', 'refresh');
			// return;
		} else {
			
			// HOOK FOR LOGIN
			if ( strstr( $_SERVER['REQUEST_URI'], $ci->config->item('uri_login_name')) )
				return ;
			
			// HOOK FOR EXCEPT LOGIN
			$jwt = $ci->input->get_request_header($ci->config->item('auth_key_name'));
			try {
				$data = JWT::checkToken($jwt);
			} catch (Exception $e) {
				JWT::raise_json(['error' => $e->getMessage()], 401);
			}
			$GLOBALS[$ci->config->item('identity_id')] = $data->{$ci->config->item('identity_id')};
		}
	}
    
	function validate_out() {
		$ci = &get_instance();
		$ci->load->config('jwt_auth/jwt_auth');
		
        $request = strstr( $_SERVER['REQUEST_URI'], 'api' );
		$parts = explode('/', $request);
		if ( empty($parts[0]) ) {
			// if (! $ci->ion_auth->logged_in() )
				// redirect('login', 'refresh');
			// return;
		} else {
		
			try {
				$data = [$ci->config->item('identity_id') => $GLOBALS[$ci->config->item('identity_id')]];
				$token = JWT::createToken($data);
				
			} catch (Exception $e) {
				JWT::raise_json(['error' => $e->getMessage()], 500);
			}
			
			$response = empty($GLOBALS["response"]) 
				? [$ci->config->item('response_token_name') => $token]
				: array_merge($GLOBALS["response"], [$ci->config->item('response_token_name') => $token]);
			JWT::raise_json($response);
		
		}
	}
}