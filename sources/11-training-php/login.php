<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once 'models/UserModel.php';
require_once 'session_middleware.php';
require_once 'middleware/csrf.php';

// Khởi session
start_session_from_request();

// Debug session info
debug_session_info(); // Thêm dòng này

error_log("PHPSESSID cookie: " . ($_COOKIE['PHPSESSID'] ?? 'null'));
error_log("Session ID hiện tại: " . session_id());
error_log("Session array: " . print_r($_SESSION, true));





$userModel = new UserModel();

// Xử lý AJAX login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');

    // Kiểm tra CSRF token
    $csrfToken = $_POST['csrf_token'] ?? '';
    error_log("SESSION CSRF: " . ($_SESSION['csrf_token'] ?? 'null'));
    error_log("POST CSRF: " . $csrfToken);

    if (!validate_csrf_token($csrfToken)) {
        echo json_encode([
            'success' => false,
            'message' => 'CSRF token invalid'
        ]);
        exit;
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $userModel->auth($username, $password);

    if ($user) {
        $_SESSION['id'] = $user[0]['id'];

        // Không regen session để giữ CSRF token
        echo json_encode([
            'success' => true,
            'user_id' => $_SESSION['id'],
            'csrf_token' => $_SESSION['csrf_token']
        ]);
        exit;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Login failed'
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <?php include 'views/meta.php'; ?>
</head>
<body>
<?php include 'views/header.php'; ?>

<div class="container">
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Login</div>
                <div style="float:right; font-size:80%; top:-10px; position:relative;">
                    <a href="#">Forgot password?</a>
                </div>
            </div>

            <div class="panel-body" style="padding-top:30px;">
                <form id="loginForm" class="form-horizontal" role="form">
                    <!-- CSRF token -->
                    <?php echo csrf_input(); ?>

                    <div class="input-group margin-bottom-25">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username or email">
                    </div>

                    <div class="input-group margin-bottom-25">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>

                    <div class="margin-bottom-25">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember"> Remember Me</label>
                    </div>

                    <div class="input-group margin-bottom-25">
                        <div class="col-sm-12 controls">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 control">
                            Don't have an account!
                            <a href="form_user.php">Sign Up Here</a>
                        </div>
                    </div>
                </form>

                <div id="loginMessage" style="margin-top:10px;color:red;"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('ajax', '1');

    try {
        const res = await fetch('login.php', {
            method: 'POST',
            body: formData,
            credentials: 'include' // gửi cookie PHPSESSID
        });

        const data = await res.json();
        const msgDiv = document.getElementById('loginMessage');

        if (data.success) {
            msgDiv.style.color = 'green';
            msgDiv.textContent = 'Login successful! User ID: ' + data.user_id;

            // Cập nhật CSRF token mới trong localStorage nếu cần
            localStorage.setItem('csrf_token', data.csrf_token);

            setTimeout(()=> window.location.href = 'list_users.php', 1000);
        } else {
            msgDiv.style.color = 'red';
            msgDiv.textContent = data.message;
        }
    } catch (err) {
        console.error('Fetch error:', err);
        document.getElementById('loginMessage').textContent = "Network error!";
    }
});
</script>

</body>
</html>
