<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}
$getData = filterData('get');

if (!empty($getData['id'])) {
    $user_id = $getData['id'];
    $checkUser = getRows("SELECT * FROM users WHERE id = $user_id");
    if ($checkUser > 0) {
        // xoá tài khoản
        $checkToken = getRows("SELECT * FROM token_login WHERE user_id = $user_id");
        if ($checkToken > 0) {
            delete('token_login', "user_id = $user_id");
        }

        $checkDelete = delete('users', "id = $user_id");
        if ($checkDelete) {
            setSessionFlash('msg', 'Xóa người dùng thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Xóa người dùng thất bại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Người dùng không tồn tại.');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=users&action=list');
    }
}
