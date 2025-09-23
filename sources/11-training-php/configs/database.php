<?php
// Sử dụng biến môi trường từ .env
define('DB_HOST', getenv('DB_HOST') ?: 'web-mysql');
define('DB_USER', getenv('DB_USERNAME') ?: 'user');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'pass');
define('DB_NAME', getenv('DB_DATABASE') ?: 'database');
define('DB_PORT', getenv('DB_PORT') ?: 3306);
