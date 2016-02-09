<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/
$hook['post_controller_constructor'] = array(
                                'class'    => 'Authentication',
                                'function' => 'validate_in',
                                'filename' => 'Authentication.php',
                                'filepath' => 'modules/jwt_auth/hooks',
                                'params'   => array()
                                );
								
$hook['post_controller'] = array(
                                'class'    => 'Authentication',
                                'function' => 'validate_out',
                                'filename' => 'Authentication.php',
                                'filepath' => 'modules/jwt_auth/hooks',
                                'params'   => array()
                                );