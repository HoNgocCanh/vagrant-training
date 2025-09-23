<?php
require_once 'session_middleware.php';
require_once 'models/UserModel.php';

// Khởi session từ token header (Redis)
start_session_from_request();

// Kiểm tra login
if (empty($_SESSION['id'])) {
    // Nếu là API, có thể trả 401
    // header('Content-Type: application/json');
    // http_response_code(401);
    // echo json_encode(['error' => 'Not authenticated']);
    // exit;

    // Hoặc redirect về login
    header('Location: login.php');
    exit;
}

$userModel = new UserModel();

// Lấy param search
$params = [];
if (!empty($_GET['keyword'])) {
    $params['keyword'] = $_GET['keyword'];
}

// Lấy danh sách user
$users = $userModel->getUsers($params);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
    <?php include 'views/header.php'?>
    <div class="container">
        <?php if (!empty($users)) { ?>
            <div class="alert alert-warning" role="alert">
                List of users!
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Fullname</th>
                        <th scope="col">Type</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) { ?>
                        <tr>
                            <th scope="row"><?php echo htmlspecialchars($user['id']) ?></th>
                            <td><?php echo htmlspecialchars($user['name']) ?></td>
                            <td><?php echo htmlspecialchars($user['fullname']) ?></td>
                            <td><?php echo htmlspecialchars($user['type']) ?></td>
                            <td>
                                <a href="form_user.php?id=<?php echo urlencode($user['id']) ?>">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true" title="Update"></i>
                                </a>
                                <a href="view_user.php?id=<?php echo urlencode($user['id']) ?>">
                                    <i class="fa fa-eye" aria-hidden="true" title="View"></i>
                                </a>
                                <a href="delete_user.php?id=<?php echo urlencode($user['id']) ?>" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fa fa-eraser" aria-hidden="true" title="Delete"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="alert alert-dark" role="alert">
                No users found.
            </div>
        <?php } ?>
    </div>
</body>
</html>
