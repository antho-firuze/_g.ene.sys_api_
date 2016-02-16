<?php 	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this, as it will be used to sign your tokens.
    | A helper command is provided for this: `php artisan jwt:generate`
    |
    */

    'secret' => '786-AllahummaSholli-alaSayyidinaaMuhammad',

    /*
    |--------------------------------------------------------------------------
    | JWT not before
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in second) that the token will be valid for.
    | Defaults to 10 second
    |
    */

    'nbf' => 10,

    /*
    |--------------------------------------------------------------------------
    | JWT expired time
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in second) that the token will be valid for.
    | Defaults to 1 hour
    |
    */

    'exp' => 3600,

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm that will be used to sign the token.
    |
    | See here: https://github.com/namshi/jose/tree/2.2.0/src/Namshi/JOSE/Signer
    | for possible values
    |
    */

    'algo' => 'HS256',

    /*
    |--------------------------------------------------------------------------
    | User identifier
    |--------------------------------------------------------------------------
    |
    | Specify a unique property of the user that will be added as the 'sub'
    | claim of the token payload.
    |
    */

    'identifier' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    |
    | Specify the required claims that must exist in any token.
    | A TokenInvalidException will be thrown if any of these claims are not
    | present in the payload.
    |
    */

    'required_claims' => ['iss', 'iat', 'exp', 'nbf', 'sub', 'jti'],

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    |
    | In order to invalidate tokens, you must have the the blacklist enabled.
    | If you do not want or need this functionality, then set this to false.
    |
    */

    'blacklist_enabled' => true,

];
