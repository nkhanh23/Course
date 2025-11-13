<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'tittle' => 'Đăng nhập hệ thống'
];

if (isLogin()) {
    $token = getSession('token_login');
    $remoToken = delete('token_login', "token = '$token'");

    if ($remoToken) {
        removeSession('token_login');
        redirect('?module=auth&action=login');
    } else {
        setSessionFlash('msg', 'Lỗi hệ thống, xin vui lòng thử lại sau.');
        setSessionFlash('msg_type', 'danger');
    }
} else {
    setSessionFlash('msg', 'Lỗi hệ thống, xin vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'danger');
}
