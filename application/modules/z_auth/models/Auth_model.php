<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Model
*
* Version: 1.0.0
*
* Author:  Antho Firuze
* 		   antho.firuze@gmail.com
*
* Added Awesomeness: Phil Sturgeon
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  21.01.2016
*
* Last Change: 21.01.2016
*
* Changelog:
* * 3-22-13 - Additional entropy added - 52aa456eef8b60ad6754b31fbdcc77bb
*
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.
* Original Author name has been kept but that does not mean that the method has not been modified.
*
* Requirements: PHP5 or above
*
*/

class Auth_model extends CI_Model
{
	public $tables = array();
	public $activation_code;
	public $forgotten_password_code;
	public $new_password;
	public $identity;
	protected $_ion_hooks;
	protected $response = NULL;
	protected $messages;
	protected $errors;
	protected $error_start_delimiter;
	protected $error_end_delimiter;
	public $_cache_user_in_group = array();
	protected $_cache_groups = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('z_auth/auth', TRUE);
		$this->lang->load('auth');

		// initialize db tables data
		$this->tables  = $this->config->item('tables', 'auth');

		//initialize data
		$this->identity_column = $this->config->item('identity', 'auth');
		$this->store_salt      = $this->config->item('store_salt', 'auth');
		$this->salt_length     = $this->config->item('salt_length', 'auth');
		$this->join			   = $this->config->item('join', 'auth');


		// initialize hash method options (Bcrypt)
		$this->hash_method 		= $this->config->item('hash_method', 'auth');
		$this->default_rounds 	= $this->config->item('default_rounds', 'auth');
		$this->random_rounds 	= $this->config->item('random_rounds', 'auth');
		$this->min_rounds 		= $this->config->item('min_rounds', 'auth');
		$this->max_rounds 		= $this->config->item('max_rounds', 'auth');


		// load the bcrypt class if needed
		if ($this->hash_method == 'bcrypt') {
			if ($this->random_rounds)
			{
				$rand = rand($this->min_rounds,$this->max_rounds);
				$params = array('rounds' => $rand);
			}
			else
			{
				$params = array('rounds' => $this->default_rounds);
			}

			$params['salt_prefix'] = $this->config->item('salt_prefix', 'auth');
			$this->load->library('z_auth/bcrypt',$params);
		}

		$this->messages    = array();
		$this->errors      = array();
		$this->message_start_delimiter = $this->config->item('message_start_delimiter', 'ion_auth');
		$this->message_end_delimiter   = $this->config->item('message_end_delimiter', 'ion_auth');
		$this->error_start_delimiter   = $this->config->item('error_start_delimiter', 'auth');
		$this->error_end_delimiter     = $this->config->item('error_end_delimiter', 'auth');
	}
	
	/**
	 * Misc functions
	 *
	 * Hash password : Hashes the password to be stored in the database.
	 * Hash password db : This function takes a password and validates it
	 * against an entry in the users table.
	 * Salt : Generates a random salt value.
	 *
	 * @author Mathew
	 */

	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password($password, $salt=false, $use_sha1_override=FALSE)
	{
		if (empty($password))
		{
			return FALSE;
		}

		// bcrypt
		if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
		{
			return $this->bcrypt->hash($password);
		}


		if ($this->store_salt && $salt)
		{
			return  sha1($password . $salt);
		}
		else
		{
			$salt = $this->salt();
			return  $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
		}
	}

	/**
	 * Get number of attempts to login occured from given IP-address or identity
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param	string $identity
	 * @return	int
	 */
	function get_attempts_num($identity)
	{
        if ($this->config->item('track_login_attempts', 'auth')) {
            $ip_address = $this->_prepare_ip($this->input->ip_address());
            $this->db->select('1', FALSE);
            if ($this->config->item('track_login_ip_address', 'auth')) {
            	$this->db->where('ip_address', $ip_address);
            	$this->db->where('login', $identity);
            } else if (strlen($identity) > 0) $this->db->or_where('login', $identity);
            $qres = $this->db->get($this->tables['login_attempts']);
            return $qres->num_rows();
        }
        return 0;
	}

	/**
	 * is_max_login_attempts_exceeded
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity
	 * @return boolean
	 **/
	public function is_max_login_attempts_exceeded($identity) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$max_attempts = $this->config->item('maximum_login_attempts', 'auth');
			if ($max_attempts > 0) {
				$attempts = $this->get_attempts_num($identity);
				return $attempts >= $max_attempts;
			}
		}
		return FALSE;
	}

	/**
	 * Get the time of the last time a login attempt occured from given IP-address or identity
	 *
	 * @param	string $identity
	 * @return	int
	 */
	public function get_last_attempt_time($identity) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$ip_address = $this->_prepare_ip($this->input->ip_address());

			$this->db->select_max('time');
            if ($this->config->item('track_login_ip_address', 'auth')) $this->db->where('ip_address', $ip_address);
			else if (strlen($identity) > 0) $this->db->or_where('login', $identity);
			$qres = $this->db->get($this->tables['login_attempts'], 1);

			if($qres->num_rows() > 0) {
				return $qres->row()->time;
			}
		}

		return 0;
	}

	/**
	 * Get a boolean to determine if an account should be locked out due to
	 * exceeded login attempts within a given period
	 *
	 * @return	boolean
	 */
	public function is_time_locked_out($identity) {

		return $this->is_max_login_attempts_exceeded($identity) && $this->get_last_attempt_time($identity) > time() - $this->config->item('lockout_time', 'auth');
	}

	/**
	 * add_to_role
	 *
	 * @return bool
	 * @author Antho Firuze
	 **/
	public function add_to_role($role_ids, $user_id)
	{
		$role_ids = array_filter(array_map('trim',explode(',',$role_ids)));
		
		$return = 0;

		// Then insert each into the database
		foreach ($role_ids as $role_id)
		{
			$data = [
				$this->join['roles'] => (float)$role_id, 
				$this->join['users'] => (float)$user_id,
				'client_id'		=> $GLOBALS['identifier']['client_id'],
				'created_by'	=> $GLOBALS['identifier']['user_id'],
				'created_at'	=> date('Y-m-d H:i:s')
			];
			if ($this->db->insert($this->tables['user_role'], $data))
			{
				$return += 1;
			}
		}

		return $return;
	}

	/**
	 * Identity check
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function identity_check($identity = '')
	{
		if (empty($identity))
		{
			return FALSE;
		}

		return $this->db->where($this->identity_column, $identity)
		                ->count_all_results($this->tables['users']) > 0;
	}

	/**
	 * Generates a random salt value.
	 *
	 * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
	 *
	 * @return void
	 * @author Anthony Ferrera
	 **/
	public function salt()
	{

		$raw_salt_len = 16;

 		$buffer = '';
        $buffer_valid = false;

        if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && @is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len) {
                $buffer .= fread($f, $raw_salt_len - $read);
                $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $raw_salt_len) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
                if ($i < $bl) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }

        $salt = $buffer;

        // encode string with the Base64 variant used by crypt
        $base64_digits   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string   = base64_encode($salt);
        $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

	    $salt = substr($salt, 0, $this->salt_length);


		return $salt;

	}

	/**
	 * reset password
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function reset_password($identity, $new) 
	{
		$query = $this->db->select('id, password, salt')
		                  ->where($this->identity_column, $identity)
		                  ->limit(1)
		    			  ->order_by('id', 'desc')
		                  ->get($this->tables['users']);

		if ($query->num_rows() !== 1)
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$result = $query->row();

		$new = $this->hash_password($new, $result->salt);

		// store the new password and reset the remember code so all remembered instances have to re-login
		// also clear the forgotten password code
		$data = array(
		    'password' => $new,
		    'remember_token' => NULL
		);

		$this->db->update($this->tables['users'], $data, array($this->identity_column => $identity));

		$return = $this->db->affected_rows() == 1;
		if ($return)
		{
			$this->set_message('password_change_successful');
		}
		else
		{
			$this->set_error('password_change_unsuccessful');
		}

		return $return;
	}

	/**
	 * change password
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function change_password($identity, $old, $new)
	{
		$query = $this->db->select('id, password, salt')
		                  ->where($this->identity_column, $identity)
		                  ->limit(1)
		    			  ->order_by('id', 'desc')
		                  ->get($this->tables['users']);

		if ($query->num_rows() !== 1)
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$user = $query->row();

		$old_password_matches = $this->hash_password_db($user->id, $old);

		if ($old_password_matches === TRUE)
		{
			// store the new password and reset the remember code so all remembered instances have to re-login
			$hashed_new_password  = $this->hash_password($new, $user->salt);
			$data = array(
			    'password' => $hashed_new_password,
			    'remember_token' => NULL,
			);

			$successfully_changed_password_in_db = $this->db->update($this->tables['users'], $data, array($this->identity_column => $identity));
			if ($successfully_changed_password_in_db)
			{
				$this->set_message('password_change_successful');
			}
			else
			{
				$this->set_error('password_change_unsuccessful');
			}

			return $successfully_changed_password_in_db;
		}

		$this->set_error('password_change_unsuccessful');
		return FALSE;
	}

	/**
	 * register
	 *
	 * @return bool
	 * @author Antho Firuze
	 **/
	public function register($identity, $password, $email, $additional_data = array(), $roles = NULL)
	{
		$manual_activation = $this->config->item('manual_activation', 'auth');

		if ($this->identity_check($identity))
		{
			$this->set_error('account_creation_duplicate_identity');
			return FALSE;
		}
		elseif ( !$this->config->item('default_role', 'auth') && empty($roles) )
		{
			$this->set_error('account_creation_missing_default_role');
			return FALSE;
		}

		// check if the default set in config exists in database
		$query = $this->db->get_where($this->tables['roles'],array('name' => $this->config->item('default_role', 'auth')),1)->row();
		if( !isset($query->id) && empty($roles) )
		{
			$this->set_error('account_creation_invalid_default_role');
			return FALSE;
		}

		// capture default role details
		$default_role = $query;

		// IP Address
		$ip_address = $this->_prepare_ip($this->input->ip_address());
		$salt       = $this->store_salt ? $this->salt() : FALSE;
		$password   = $this->hash_password($password, $salt);

		// Users table.
		$data = array(
		    $this->identity_column   => $identity,
		    'password'   => $password,
		    'email'      => $email,
		    'ip_address' => $ip_address,
			'role_id'	 => isset($default_role->id) ? $default_role->id : 0,
		    'is_active'     => ($manual_activation === false ? 1 : 0)
		);

		if ($this->store_salt)
		{
			$data['salt'] = $salt;
		}

		// filter out any data passed that doesnt have a matching column in the users table
		// and merge the set user data and the additional data
		$user_data = array_merge($this->_filter_data($this->tables['users'], $additional_data), $data);
		
		if (! $this->db->insert($this->tables['users'], $user_data))
		{
			$error = (object) $this->db->error();
			$this->set_error($error->message);
			return FALSE;
		}

		$id = $this->db->insert_id();

		// add in roles array if it doesn't exits and stop adding into default role if default role ids are set
		if( isset($default_role->id) && empty($roles) )
		{
			$roles = $default_role->id;
		} 

		$this->add_to_role($roles, $id);

		return (isset($id)) ? $id : FALSE;
	}

	/**
	 * login
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function login($identity, $password, $remember=FALSE)
	{
		if (empty($identity) || empty($password))
		{
			$this->set_error('login_unsuccessful');
			return FALSE;
		}

		$query = $this->db->select($this->identity_column.', email, id, password, is_active, last_login')
		                  ->where($this->identity_column, $identity)
		                  ->limit(1)
		    			  ->order_by('id', 'desc')
		                  ->get($this->tables['users']);

		if($this->is_time_locked_out($identity))
		{
			// Hash something anyway, just to take up time
			// $this->hash_password($password);

			$this->set_error('login_timeout');
			return FALSE;
		}

		if ($query->num_rows() === 1)
		{
			$user = $query->row();

			if ($user->is_active == 0)
			{
				$this->set_error('login_unsuccessful_not_active');
				return FALSE;
			}
			
			$password = $this->hash_password_db($user->id, $password);

			if ($password === TRUE)
			{

				$this->update_last_login($user->id);

				$this->clear_login_attempts($identity);

				if ($remember)
				{
					$this->remember_user($user->id);
				}
				
				return $user->id;
			}
		}

		// Hash something anyway, just to take up time
		$this->hash_password($password);

		$this->increase_login_attempts($identity);

		$this->set_error('login_unsuccessful');

		return FALSE;
	}
	
	/**
	 * update_last_login
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function update_last_login($id)
	{
		$this->db->update($this->tables['users'], array('last_login' => time()), array('id' => $id));

		return $this->db->affected_rows() == 1;
	}

	/**
	 * increase_login_attempts
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity
	 **/
	public function increase_login_attempts($identity) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$ip_address = $this->_prepare_ip($this->input->ip_address());
			return $this->db->insert($this->tables['login_attempts'], array('ip_address' => $ip_address, 'login' => $identity, 'time' => time()));
		}
		return FALSE;
	}

	/**
	 * clear_login_attempts
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity
	 **/
	public function clear_login_attempts($identity, $expire_period = 86400) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$ip_address = $this->_prepare_ip($this->input->ip_address());

			$this->db->where(array('ip_address' => $ip_address, 'login' => $identity));
			// Purge obsolete login attempts
			$this->db->or_where('time <', time() - $expire_period, FALSE);

			return $this->db->delete($this->tables['login_attempts']);
		}
		return FALSE;
	}

	/**
	 * This function takes a password and validates it
	 * against an entry in the users table.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password_db($id, $password, $use_sha1_override=FALSE)
	{
		if (empty($id) || empty($password))
		{
			return FALSE;
		}

		$query = $this->db->select('password, salt')
		                  ->where('id', $id)
		                  ->limit(1)
		                  ->order_by('id', 'desc')
		                  ->get($this->tables['users']);

		$hash_password_db = $query->row();

		if ($query->num_rows() !== 1)
		{
			return FALSE;
		}

		// bcrypt
		if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
		{
			if ($this->bcrypt->verify($password,$hash_password_db->password))
			{
				return TRUE;
			}

			return FALSE;
		}

		// sha1
		if ($this->store_salt)
		{
			$db_password = sha1($password . $hash_password_db->salt);
		}
		else
		{
			$salt = substr($hash_password_db->password, 0, $this->salt_length);

			$db_password =  $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
		}

		if($db_password == $hash_password_db->password)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * set_message
	 *
	 * Set a message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message($message)
	{
		$this->messages[] = $message;

		return $message;
	}

	/**
	 * messages
	 *
	 * Get the messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function messages()
	{
		$_output = '';
		foreach ($this->messages as $message)
		{
			$messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
			$_output .= $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
		}

		return $_output;
	}

	/**
	 * set_error
	 *
	 * Set an error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error($error)
	{
		$this->errors[] = $error;

		return $error;
	}

	/**
	 * errors
	 *
	 * Get the error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function errors()
	{
		$_output = '';
		foreach ($this->errors as $error)
		{
			$errorLang = $this->lang->line($error) ? $this->lang->line($error) : $error;
			$_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
		}

		return $_output;
	}

	/**
	 * errors as array
	 *
	 * Get the error messages as an array
	 *
	 * @return array
	 * @author Raul Baldner Junior
	 **/
	public function errors_array($langify = TRUE)
	{
		if ($langify)
		{
			$_output = array();
			foreach ($this->errors as $error)
			{
				$errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
				$_output[] = $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
			}
			return $_output;
		}
		else
		{
			return $this->errors;
		}
	}

	/**
	 * clear_errors
	 *
	 * Clear Errors
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function clear_errors()
	{
		$this->errors = array();

		return TRUE;
	}

	protected function _filter_data($table, $data)
	{
		$filtered_data = array();
		$columns = $this->db->list_fields($table);

		if (is_array($data))
		{
			foreach ($columns as $column)
			{
				if (array_key_exists($column, $data))
					$filtered_data[$column] = $data[$column];
			}
		}

		return $filtered_data;
	}

	protected function _prepare_ip($ip_address) {
		// just return the string IP address now for better compatibility
		return $ip_address;
	}

}