<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start(); // Tạo mới phiên làm việc 
ob_start(); // Tránh trường lỗi khi xử dụng các hàm như header, cookie

require_once 'config.php';
require_once './includes/connect.php';
require_once './includes/database.php';
require_once './includes/session.php';
//email
require_once './includes/mailer/Exception.php';
require_once './includes/mailer/PHPMailer.php';
require_once './includes/mailer/SMTP.php';

require_once './includes/function.php';





$module = _MODULES;
$action = _ACTION;

if (!empty($_GET['module'])) {
    $module = $_GET['module'];
}

if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}


$path = 'modules/' . $module . '/' . $action . '.php';

if (!empty($path)) { // Kiểm tra path có dữ liệu hay không
    if (file_exists($path)) { // Nếu path có dữ liệu rồi thì kiểm tra đường dẫn file có tồn tại hay không
        require_once $path;
    } else {
        require_once './modules/errors/400.php';
    }
} else {
    require_once './modules/errors/500.php';
}
