<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}


// Hàm lấy tất cả dữ liệu của 1 bảng
function getALL($sql)
{
    global $connect;

    $stm = $connect->prepare($sql);

    $stm->execute();

    $result = $stm->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}
// Hàm đếm số lương hàng của bảng
function getRows($sql)
{
    global $connect;

    $stm = $connect->prepare($sql);

    $stm->execute();

    $result = $stm->rowCount();

    return $result;
}



// Truy vấn 1 dòng dữ liệu của 1 bảng
function getOne($sql)
{
    global $connect;
    $stm = $connect->prepare($sql);
    $stm->execute();
    $result = $stm->fetch(PDO::FETCH_ASSOC);
    return $result;
}

// Insert dữ liệu

function insert($table, $data)
{
    // 
    //     $data = [
    //     'name' => 'Khanh',
    //     'email' => 'nkhanh23005@gmail.com',
    //     'phone' => '0987654321'
    //     ];

    global $connect;

    $keys = array_keys(($data));
    $cot = implode(',', $keys);
    $placeholder = ':' . implode(',:', $keys);

    $sql = "INSERT INTO $table ({$cot}) VALUES ({$placeholder})";
    $stm = $connect->prepare($sql);

    $result =  $stm->execute($data);
    return $result;
}

function update($table, $data, $condition = '')
{
    global $connect;

    $update = '';

    foreach ($data as $key => $value) {
        $update .= $key . '=:' . $key . ',';
    }

    $update = trim($update, ',');

    if (!empty($condition)) {
        $sql = "UPDATE $table SET $update WHERE $condition";
    } else {
        $sql = "UPDATE $table SET $update";
    }
    $stm = $connect->prepare($sql);

    $result = $stm->execute($data);
    return $result;
}


function delete($table, $condition = '')
{
    global  $connect;
    if (!empty($condition)) {
        $sql = "DELETE FROM $table WHERE $condition";
    } else {
        $sql = "DELETE FROM $table";
    }
    $stm = $connect->prepare(($sql));

    $result = $stm->execute();
    return $result;
}

function getLastID()
{
    global $connect;
    return $connect->lastInsertId();
}