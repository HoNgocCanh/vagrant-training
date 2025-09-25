<?php
session_start();
require_once 'models/UserModel.php';
$userModel = new UserModel();

// Tạo CSRF token nếu chưa có
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Chỉ xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        // Trả về JSON dễ đọc trong fetch
        echo json_encode(['success' => false, 'message' => 'CSRF token invalid']);
        exit;
    }

    // Xóa user nếu id hợp lệ
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $userModel->deleteUserById($id);
        echo json_encode(['success' => true, 'message' => "User ID $id deleted successfully."]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'User ID missing.']);
        exit;
    }
}

// Nếu không phải POST
echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
