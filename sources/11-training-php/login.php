<?php
require_once 'models/UserModel.php';
require_once 'session_middleware.php';

$userModel = new UserModel();

// Nếu request là AJAX POST, xử lý login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ajax'])) {
    header('Content-Type: application/json');

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $user = $userModel->auth($username, $password);

    if ($user) {
        session_start();
        $_SESSION['id'] = $user[0]['id'];
        session_regenerate_id(true); // bảo mật

        echo json_encode([
            'success' => true,
            'token' => session_id(),
            'user_id' => $_SESSION['id']
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
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php'?>

<div class="container">
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Login</div>
                <div style="float:right; font-size: 80%; position: relative; top:-10px">
                    <a href="#">Forgot password?</a>
                </div>
            </div>

            <div style="padding-top:30px" class="panel-body" >
                <form id="loginForm" class="form-horizontal" role="form">

                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="login-username" type="text" class="form-control" name="username" placeholder="username or email">
                    </div>

                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                    </div>

                    <div class="margin-bottom-25">
                        <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                        <label for="remember"> Remember Me</label>
                    </div>

                    <div class="margin-bottom-25 input-group">
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
// AJAX login
document.getElementById('loginForm').addEventListener('submit', async function(e){
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('ajax', '1'); // đánh dấu request là AJAX

    const res = await fetch('login.php', {
        method: 'POST',
        body: formData
    });

    const data = await res.json();

    const msgDiv = document.getElementById('loginMessage');
    if(data.success){
        // Lưu token vào localStorage
        localStorage.setItem('session_token', data.token);
        msgDiv.style.color = 'green';
        msgDiv.textContent = 'Login successful! User ID: ' + data.user_id;

        // Redirect sau 1 giây
        setTimeout(()=> {
            window.location.href = 'list_users.php';
        }, 1000);
    } else {
        msgDiv.style.color = 'red';
        msgDiv.textContent = data.message;
    }
});
</script>

</body>
</html>
