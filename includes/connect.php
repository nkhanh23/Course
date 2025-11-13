<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}
try {
    $OPTION = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", //Hỗ trợ tiếng việt
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Đẩy lỗi vào ngoại lệ
    );
    $dsn = _DRIVER . ':host=' . _HOST . "; dbname=" . _DB;
    $connect = new PDO($dsn, _USER, _PASS, $OPTION);
} catch (Exception $ex) {
    require_once './modules/errors/404.php';
    die();
}
