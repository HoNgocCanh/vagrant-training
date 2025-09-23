<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'models/UserModel.php';

$userModel = new UserModel();
$user = NULL;
$_id = NULL;

if (!empty($_GET['id'])) {
    $_id = $_GET['id'];
    $user = $userModel->findUserById($_id);
}

if (!empty($_POST['submit'])) {
    if (!empty($_id)) {
        $userModel->updateUser($_POST);
    } else {
        $userModel->insertUser($_POST);
    }
    header('location: list_users.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User form</title>
    <?php include 'views/meta.php'; ?>
</head>
<body>
    <?php include 'views/header.php'; ?>
    <div class="container">
        <div class="alert alert-warning" role="alert">
            User form
        </div>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $_id ?>">
            
            <div class="form-group">
                <label for="name">Username</label>
                <input class="form-control" name="name" placeholder="Username" value="<?php echo $user[0]['name'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="fullname">Fullname</label>
                <input class="form-control" name="fullname" placeholder="Fullname" value="<?php echo $user[0]['fullname'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-control" type="email" name="email" placeholder="Email" value="<?php echo $user[0]['email'] ?? ''; ?>">
            </div>

            <div class="form-group">
                <label for="type">Type</label>
                <select class="form-control" name="type">
                    <option value="user" <?php echo (isset($user[0]['type']) && $user[0]['type']=='user')?'selected':''; ?>>User</option>
                    <option value="admin" <?php echo (isset($user[0]['type']) && $user[0]['type']=='admin')?'selected':''; ?>>Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password <?php echo empty($_id)?'':'(leave blank if not change)'; ?></label>
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
