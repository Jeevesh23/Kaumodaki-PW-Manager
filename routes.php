<?php

require_once __DIR__ . '/router.php';

// ##################################################
// ##################################################
// ##################################################

get('/', 'start/index.php');
get('/start', 'start/index.php');
get('/authentication', 'authentication/index.php');
post('/authentication/register', 'authentication/register.php');
get('/authentication/otp', 'authentication/register-form.php');
post('/authentication/register-otp', 'authentication/register-otp.php');
post('/authentication/signin', 'authentication/signin.php');
get('/authentication/verify-otp', 'authentication/verify-otp.php');
any('/authentication/reset-mail', 'authentication/reset-mail.php');
any('/authentication/reset-password', 'authentication/reset-password.php');
any('/authentication/contact', 'authentication/contact.php');

any('/vault', 'vault/index.php');
any('/vault/enter-password', 'vault/add.php');
get('/vault/store-old', 'vault/storeold.php');
get('/vault/password', 'vault/randpwd.php');
get('/vault/passphrase', 'vault/randphr.php');
any('/vault/uploads', 'vault/upload_files.php');
get('/vault/filecontrol', 'vault/filecontrol.php');
get('/vault/logout', 'vault/logout.php');

get('/advanced-strength', '/password_strength_analysis/zxcvbn.php');

any('/404', '404/404.php');
