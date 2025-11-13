<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}


// Hàm lưu một giá trị vào session nếu session đã được khởi tạo
function setSession($key, $value)
{
    // Nếu hàm session_start() đã hoạt động thì sẽ trả về sessionID
    if (!empty(session_id())) {
        $_SESSION[$key] = $value;
        return true;
    }
    return false;
}

// Hàm lấy ra dữ liệu từ session
function getSession($key = '')
{
    // Nếu không truyền key vào thì sẽ trả về tất cả dữ liệu trong session
    if (empty($key)) {
        return $_SESSION;
    } else {
        // Nếu có key truyền vào thì kiểm tra có session nào có key đó không
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    return  false;
}

function removeSession($key = '')
{
    if (empty($key)) {
        session_destroy();
        return true;
    } else {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return true;
    }
    return false;
}

// Tạo sesion lấy dữ liệu ra sử dụng, sau khi sử dụng xong tự động xóa
function setSessionFlash($key, $value)
{
    $key = $key . 'Flash';

    $result = setSession($key, $value);
    return $result;
}

// Lấy session  flash
function getSessionFlash($key)
{
    $key = $key . 'Flash';

    $result = getSession($key);
    removeSession(($key));
    return $result;
}
