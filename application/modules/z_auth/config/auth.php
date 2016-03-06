<?php 	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = [

	'tables'	=> [
		'users'	=> 'a_user',
		'roles'	=> 'a_role',
		'user_role'	=> 'a_user_role',
		'login_attempts' => 'a_loginattempt'
	],
	'join'		=> [
		'users'	=> 'user_id',
		'roles'	=> 'role_id'
	],
	
	'hash_method' 	=>	'bcrypt',
	'default_rounds'	=> 8,
	'random_rounds'		=> FALSE,
	'min_rounds'		=> 5,
	'max_rounds'		=> 9,
	
	'store_salt'		=> TRUE,
	'salt_length'		=> 22,
	'salt_prefix'		=> version_compare(PHP_VERSION, '5.3.7', '<') ? '$2a$' : '$2y$',
	
	'default_role'				=> 'Paradise User',          
	'admin_role'				=> 'Paradise Admin',        
	'identity'					=> 'name',
	'identity_id'				=> 'userId',
	'min_password_length'		=> 8,
	'max_password_length'		=> 20,
	'email_activation'			=> FALSE,
	'manual_activation'			=> FALSE,
	'track_login_attempts'		=> TRUE,
	'maximum_login_attempts' 	=> 7,
	'track_login_ip_address'	=> FALSE,
	'lockout_time'				=> 600,
	'forgot_password_expiration' => 0,
	
	'message_start_delimiter'	=> '<p>',
	'message_end_delimiter'		=> '</p>',
	'error_start_delimiter'		=> '<p>',
	'error_end_delimiter'		=> '</p>',
];
