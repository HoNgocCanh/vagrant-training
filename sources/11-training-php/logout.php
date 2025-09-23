<?php
require_once 'session_middleware.php';

// Khởi session từ token gửi bởi client
start_session_from_request();

// Xóa session trên Redis
session_destroy();

// Trả JSON cho frontend xử lý
header('Content-Type: application/json');
echo json_encode(['success' => true]);
