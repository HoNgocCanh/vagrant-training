<?php
function start_session_from_request() {
    if (session_status() === PHP_SESSION_NONE) {
        $token = null;

        // Ưu tiên lấy từ POST (cho form submit)
        if (!empty($_POST['session_id'])) {
            $token = $_POST['session_id'];
            error_log("Session token from POST: $token");
        } else {
            // Fallback: lấy từ header hoặc cookie
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            
            if (!empty($headers['Authorization'])) {
                if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $m)) {
                    $token = trim($m[1]);
                    error_log("Session token from Authorization header: $token");
                }
            } elseif (!empty($_SERVER['HTTP_X_SESSION_ID'])) {
                $token = $_SERVER['HTTP_X_SESSION_ID'];
                error_log("Session token from X-Session-ID header: $token");
            } elseif (!empty($_COOKIE['PHPSESSID'])) {
                $token = $_COOKIE['PHPSESSID'];
                error_log("Session token from PHPSESSID cookie: $token");
            }
        }

        // Validate và set session ID
        if ($token && preg_match('/^[a-zA-Z0-9,-]{16,128}$/', $token)) {
            error_log("Setting session ID: $token");
            session_id($token);
        } else {
            error_log("No valid session token found or invalid format");
        }

        // Kiểm tra Redis connection trước khi start session
        try {
            error_log("Starting session...");
            session_start();
            error_log("Session started successfully. ID: " . session_id());
            
            // Test write to session to verify Redis is working
            $_SESSION['test'] = time();
            error_log("Session test write successful");
            
        } catch (Exception $e) {
            error_log("Session start failed: " . $e->getMessage());
            throw $e;
        }
    } else {
        error_log("Session already started. ID: " . session_id());
    }
}