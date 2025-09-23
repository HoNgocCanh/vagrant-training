<?php
session_start();

// Set thử giá trị
if (empty($_SESSION['id'])) {
    $_SESSION['id'] = rand(1000, 9999);
    echo "Tạo session mới với ID: " . $_SESSION['id'] . "<br>";
} else {
    echo "Session đã tồn tại với ID: " . $_SESSION['id'] . "<br>";
}

// Kết nối Redis để xem có key lưu không
$r = new Redis();
$r->connect('web-redis', 6379);

// Liệt kê toàn bộ key trong Redis
$keys = $r->keys('*');
echo "<pre>";
print_r($keys);
echo "</pre>";
?>
