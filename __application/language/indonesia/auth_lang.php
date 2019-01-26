<?php

/*
 * Indonesia language
 * 
 * Sample: 
 * =======
 * $lang['err_sample'] = 'Incorrect Email or Password'; 
 * $lang['sucess_sample'] = 'Login Success'; 
 * $lang['conf_sample'] = 'Are you sure want to delete this record ?'; 
 * $lang['info_sample'] = 'Your password has been sent to your email'; 
 * $lang['notif_sample'] = 'You have unread email'; 
 */

$lang['err_token_invalid'] = 'Session tidak valid, silahkan login ulang'; 
$lang['err_token_expired'] = 'Session telah kadaluarsa, silahkan login ulang'; 

$lang['err_field_required'] = 'Field [%s] is required'; 
$lang['err_min_password_length'] = 'Minimum password length is %s'; 
$lang['err_max_password_length'] = 'Maximum password length is %s'; 
$lang['err_login_attempt_reached'] = 'Maksimum kesalahan login sudah tercapai, akun anda akan terkunci sementara. Silahkan coba lagi nanti, setelah %s'; 
$lang['err_login_failed'] = 'Email atau Kata Sandi salah, silahkan coba kembali'; 
$lang['err_unlocked_failed'] = 'Incorrect Password'; 
$lang['err_old_password'] = 'Incorrect Old Password'; 
$lang['err_email_not_found'] = 'Email not found'; 
$lang['err_email_has_register'] = 'Your email have registered, please login with your email & password !'; 
$lang['err_email_has_register_not_active'] = 'Your email have registered but not activate yet, please check your email to activate !'; 
$lang['err_old_client_lost_email'] = 'Your email is not recognized, please replace with your another email !'."\r\n".' Or you can ask our CS admin.'; 
$lang['err_activate_account'] = 'Token not found, or maybe your account has already activate'; 

$lang['success_login'] = 'Login Success'; 
$lang['success_unlocked'] = 'This account has been unlocked'; 
$lang['success_reset'] = 'Your password has been reset'; 
$lang['success_chg_password'] = 'Your password has been changed'; 
$lang['success_register'] = 'Your registration done, please check your email to validate your account'; 
$lang['success_activation'] = 'Thank you, your account has been active.<br>Now you can login in our Web/Android/IOS Apps !'; 

$lang['info_sent_email_password'] = 'Your password has been sent to your email'; 
$lang['info_sent_email_reset_password_link'] = 'Link address for reset password has been sent to your email'; 
$lang['info_sent_email_rst_password'] = 'Password has been reset successfully, & new password has been sent to user email'; 

$lang['email_subject_forgot_password_simple'] = 'Your New SIMPIPRO Password !';
$lang['email_body_forgot_password_simple'] = 'Dear {name}, <br><br>'.
		'Your SIMPIPRO password has been created. <br><br>'.
		'This is your new password <b>{new_password}</b><br><br><br>'.
		'This email was sent by: <b>PT. SIMPI PROFESSIONAL INDONESIA</b>,<br>'.
		'Palakali Raya Street, No.49C, Kukusan Depok, Indonesia';
		
$lang['email_subject_forgot_password'] = 'Forgot your SIMPIPRO Password?';
$lang['email_body_forgot_password'] = 'Dear {name}, <br><br>'.
		'You can reset your SIMPIPRO password by clicking link address below. <br><br>'.
		'{domain_frontend}frontend/x_auth?mode=activation&agent=android&token={token}<br><br><br>'.
		'Warning: This link is valid about 1 hour, start from your received this email, and can be use only one time.<br><br>'.
		'This email was sent by: <b>PT. SIMPI PROFESSIONAL INDONESIA</b>,<br>'.
		'Palakali Raya Street, No.49C, Kukusan Depok, Indonesia';
		
$lang['email_subject_reset_password'] = 'Your SIMPIPRO Password has been RESET !';
$lang['email_body_reset_password'] = 'Dear {name}, <br><br>'.
		'Your SIMPIPRO password has been RESET. <br><br>'.
		'Your new password is <b>{new_password}</b><br><br><br>'.
		'This email was sent by: <b>PT. SIMPI PROFESSIONAL INDONESIA</b>,<br>'.
		'Palakali Raya Street, No.49C, Kukusan Depok, Indonesia';

$lang['email_subject_rst_password'] = 'Your SIMPIPRO Password has been RESET by Admin !';
$lang['email_body_rst_password'] = 'Dear {name}, <br><br>'.
		'Your SIMPIPRO password has been RESET by Admin. <br><br>'.
		'Your new password is <b>{new_password}</b><br><br><br>'.
		'This email was sent by: <b>PT. SIMPI PROFESSIONAL INDONESIA</b>,<br>'.
		'Palakali Raya Street, No.49C, Kukusan Depok, Indonesia';

$lang['email_subject_chg_password'] = 'Your SIMPIPRO Password has been CHANGED !';
$lang['email_body_chg_password'] = 'Dear {name}, <br><br>'.
		'Your SIMPIPRO password has been CHANGED. <br><br>'.
		'Your new password is <b>{new_password}</b><br><br><br>'.
		'This email was sent by: <b>PT. SIMPI PROFESSIONAL INDONESIA</b>,<br>'.
		'Palakali Raya Street, No.49C, Kukusan Depok, Indonesia';

$lang['email_subject_register'] = 'SIMPIPRO account activation !';
$lang['email_body_register'] = 'Dear {name}, <br><br>'.
		'Your registration has been completed. <br><br>'.
		'This is your login email & password: <br><br>'.
		'Email : <b>{email}</b><br>'.
		'Password : <b>{new_password}</b><br><br>'.
		'Before you login, please activate first by clicking this link below :<br><br>'.
		'{domain_frontend}frontend/x_auth?mode=activation&agent=android&token={token}<br><br><br>'.
		'This email was sent by: <b>PT. SIMPI PROFESSIONAL INDONESIA</b>,<br>'.
		'Palakali Raya Street, No.49C, Kukusan Depok, Indonesia';

		