<?php
// test_session.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Bật cookie cho session
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);

// Include middleware nếu cần
require_once 'session_middleware.php';
require_once 'middleware/csrf.php';

// Khởi session **phải trước bất cứ output nào**
start_session_from_request();

// Nếu chưa có CSRF token, tạo một token mới
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Hiển thị thông tin session + cookie
echo "<h2>Session Test</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>PHPSESSID cookie:</strong> " . ($_COOKIE['PHPSESSID'] ?? 'NULL') . "</p>";
echo "<p><strong>CSRF token:</strong> " . $_SESSION['csrf_token'] . "</p>";

echo "<h3>Session Array:</h3>";
echo "<pre>" . print_r($_SESSION, true) . "</pre>";

// Form test POST
?>
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="text" name="dummy" placeholder="Type something">
    <button type="submit">Submit</button>
</form>

<?php
// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Received</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Kiểm tra CSRF
    $csrfPost = $_POST['csrf_token'] ?? '';
    if (validate_csrf_token($csrfPost)) {
        echo "<p style='color:green;'>✅ CSRF token valid</p>";
    } else {
        echo "<p style='color:red;'>❌ CSRF token INVALID</p>";
    }

    // Hiển thị session lại sau POST
    echo "<h3>Session Array after POST:</h3>";
    echo "<pre>" . print_r($_SESSION, true) . "</pre>";
}
?>
