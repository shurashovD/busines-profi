<?php

if (!isset($_SESSION['is_admin'])) {
    header("WWW-Authenticate: Basic");
    header("HTTP/1.0 401 Unauthorized");
}

$headers = apache_request_headers();
if (isset($headers['Authorization'])) {
    ['0' => $type, '1' => $token] = explode(' ', $headers['Authorization']);
    if ($type === 'Basic' && isset($token)) {
        $login_pass = base64_decode($token);
        $_SESSION['is_admin'] = true;
    }
}

?>