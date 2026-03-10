<?php
session_start();

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    $sessionName = session_name();
    if (is_string($sessionName)) {
        setcookie(
            $sessionName,
            '',
            time() - 42000,
            (string)$params['path'],
            (string)$params['domain'],
            (bool)$params['secure'],
            (bool)$params['httponly']
        );
    }
}

session_destroy();

setcookie('admin_remember', '', time() - 3600, '/');
setcookie('admin_token', '', time() - 3600, '/');

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

header("Location: ../index.php?page=login");
exit();