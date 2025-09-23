<?php
/**
 * Middleware để khởi session từ token gửi bởi client hoặc cookie
 */
function start_session_from_request() {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $token = null;

    // Lấy token từ header Authorization: Bearer <token>
    if (!empty($headers['Authorization'])) {
        if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $m)) {
            $token = trim($m[1]);
        }
    } elseif (!empty($_SERVER['HTTP_X_SESSION_ID'])) {
        $token = $_SERVER['HTTP_X_SESSION_ID'];
    } elseif (!empty($_COOKIE['PHPSESSID'])) {
        // 👉 fallback: lấy từ cookie
        $token = $_COOKIE['PHPSESSID'];
    }

    if ($token) {
        session_id($token); // set session ID từ client
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
