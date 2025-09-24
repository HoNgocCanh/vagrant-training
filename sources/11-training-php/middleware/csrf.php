<?php
function csrf_token() {
    // Đảm bảo session đã được start
    if (session_status() !== PHP_SESSION_ACTIVE) {
        error_log("WARNING: Session not active when generating CSRF token");
        return null;
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        error_log("Generated new CSRF token: " . $_SESSION['csrf_token'] . " for session: " . session_id());
        
        // Force session write để đảm bảo token được lưu vào Redis
        session_write_close();
        session_start(); // Restart session
    }
    
    return $_SESSION['csrf_token'];
}

function csrf_input() {
    $token = csrf_token();
    if (!$token) {
        error_log("ERROR: Cannot generate CSRF token - session issue");
        return '<input type="hidden" name="csrf_token" value="">';
    }
    
    $token_escaped = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
    $sid_escaped   = htmlspecialchars(session_id(), ENT_QUOTES, 'UTF-8');
    
    error_log("CSRF input - Token: $token_escaped, Session ID: $sid_escaped");
    
    return '<input type="hidden" name="csrf_token" value="'.$token_escaped.'">' .
           '<input type="hidden" name="session_id" value="'.$sid_escaped.'">';
}

function validate_csrf_token($token) {
    // Kiểm tra session active
    if (session_status() !== PHP_SESSION_ACTIVE) {
        error_log("ERROR: Session not active during CSRF validation");
        return false;
    }
    
    // Kiểm tra token existence
    if (empty($_SESSION['csrf_token'])) {
        error_log("ERROR: No CSRF token in session");
        return false;
    }
    
    if (empty($token)) {
        error_log("ERROR: No CSRF token provided");
        return false;
    }
    
    $isValid = hash_equals($_SESSION['csrf_token'], $token);
    
    error_log("CSRF validation - Session ID: " . session_id() . 
              ", Session token: " . $_SESSION['csrf_token'] . 
              ", Provided token: $token, Valid: " . ($isValid ? 'YES' : 'NO'));
    
    return $isValid;
}

// Helper function để debug session
function debug_session_info() {
    error_log("=== SESSION DEBUG INFO ===");
    error_log("Session status: " . session_status());
    error_log("Session ID: " . session_id());
    error_log("Session name: " . session_name());
    error_log("Session save handler: " . ini_get('session.save_handler'));
    error_log("Session save path: " . ini_get('session.save_path'));
    error_log("Session data: " . print_r($_SESSION, true));
    error_log("PHPSESSID cookie: " . ($_COOKIE['PHPSESSID'] ?? 'null'));
    error_log("========================");
}