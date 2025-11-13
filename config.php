<?php
const _nkhanh = true; // Dùng để kiểm tra truy cập có hợp lệ không

// Mặc định
const _MODULES = 'dashboard';
const _ACTION = 'index';

// Khai báo database
const _HOST = 'localhost';
const _DB = 'manager_course';
const _USER = 'root';
const _PASS = '';
const _DRIVER = 'mysql';

// debug error
const _DEBUG = true; //Khi chạy chương trình và gặp lỗi thì bật true lên thì sẽ hiển thị lỗi cho mình xem, false sẽ tắt đi

// thiết lập đường dẫn host
define('_HOST_URL', 'http://' . ($_SERVER['HTTP_HOST']) . '/course');
define('_HOST_URL_TEMPLATES', _HOST_URL . '/templates');

//thiết lập path
define('_PATH_URL', __DIR__);
define('_PATH_URL_TEMPLATES', _PATH_URL . '/templates');
