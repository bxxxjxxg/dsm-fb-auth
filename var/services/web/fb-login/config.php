<?php

return array (
	'title' => "DS918+ Station",
	'app_id' => '',
	'app_secret' => '',
	'redirect_url' => 'https://your-ip/fb-login/index.php',
	'dsm_auth_url' => 'http://127.0.0.1:5000/webapi/auth.cgi',
	'dsm_auth_params' => '?api=SYNO.API.Auth&version=3&method=login&session=FileStation&format=cookie',
	'dsm_url' => 'https://your-ip:5001/',
	'groups' => array (
		'fb-group-id' => array('account' => 'public', 'passwd' => 'public_password'),
	),
);

?>
