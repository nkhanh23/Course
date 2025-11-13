<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Sửangười dùng'
];
layout('header', $data);
layout('side_bar');
// Lấy id từ URL
$filter = filterData('get');
$user_id = !empty($filter['id']) ? $filter['id'] : 0;

// Lấy thông tin user từ database
$detailUser = getOne("SELECT * FROM users WHERE id = $user_id");



if (isPost()) {
    $filter = filterData();
    $errors = [];

    // validate fullname
    if (empty(trim($filter['fullname']))) {
        $errors['fullname']['required'] = 'Họ tên bắt buộc phải nhập';
    } else {
        if (strlen(trim($filter['fullname'])) < 5) {
            $errors['fullname']['length'] = 'Họ tên phải lớn hơn 5 ký tự';
        }
    }


    if ($filter['email'] != $detailUser['email']) {
        // Validate email
        if (empty(trim($filter['email']))) {
            $errors['email']['required'] = 'Email bắt buộc phải nhập';
        } else {
            // Đúng định dạng email, email này đã tồn tại trong CSDL chưa
            if (!validateEmail(trim($filter['email']))) {
                $errors['email']['isEmail'] = 'Email không đúng định dạng';
            } else {
                $email = $filter['email'];

                $checkEamil = getRows("SELECT * FROM users WHERE email = '$email' ");
                if ($checkEamil > 0) {
                    $errors['email']['check'] = 'Email đã tồn tại';
                }
            }
        }
    }

    // Validate phone
    if (empty($filter['phone'])) {
        $errors['phone']['required'] = 'Số điện thoại bắt buộc phải nhập';
    } else {
        if (!isPhone($filter['phone'])) {
            $errors['phone']['isPhone'] = 'Số điện thoại ko đúng định dạng';
        }
    }

    // Validate Password MK > 6 ký tự
    if (empty(trim($filter['password']))) {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 ký tự';
        }
    }
    if (empty($errors)) {
        $data = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'group_id' => $filter['group'],
            'status' => $filter['status'],
            'address' => (!empty($filter['address']) ? $filter['address'] : null),
            'updated_at' => date('Y:m:d H:i:S')
        ];

        if (!empty($filter['password'])) {
            $dataUpdate['password'] = password_hash($filter['password'], PASSWORD_DEFAULT);
        }
        $condition = "id=" . $user_id;
        $updateStatus = update('users', $data, $condition);
        if ($updateStatus) {
            setSessionFlash('msg', 'Cập nhật người dùng thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Cập nhật người dùng thất bại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
// Nếu không có oldData từ session (lần đầu vào trang hoặc không có lỗi), dùng dữ liệu từ database
if (empty($oldData) && !empty($detailUser)) {
    $oldData = $detailUser;
}
$errorsArr  = getSessionFlash('errors');
?>
<div class="container add-user">
    <?php
    if (!empty($msg) && !empty($msg_type)) {
        getMsg($msg, $msg_type);
    }
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 pb-3">
                <label for="fullname">Họ và tên</label>
                <input id="fullname" name="fullname" type="text" value="<?php
                                                                        if (!empty($oldData)) {
                                                                            echo oldata($oldData, 'fullname');
                                                                        }
                                                                        ?>" class="form-control" placeholder="Họ tên">
                <?php
                if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'fullname');
                }
                ?>
            </div>
            <div class="col-6 pb-3">
                <label for="email">Email</label>
                <input id="email" name="email" type="text" value="<?php
                                                                    if (!empty($oldData)) {
                                                                        echo oldata($oldData, 'email');
                                                                    } ?>" class="form-control" placeholder="Email">
                <?php
                if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'email');
                }
                ?>
            </div>
            <div class="col-6 pb-3">
                <label for="phone">Số điện thoại</label>
                <input id="phone" name="phone" type="text" value="<?php
                                                                    if (!empty($oldData)) {
                                                                        echo oldata($oldData, 'phone');
                                                                    } ?>" class="form-control"
                    placeholder="Số điện thoại">
                <?php
                if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'phone');
                }
                ?>
            </div>
            <div class="col-6 pb-3">
                <label for="password">Mật khẩu</label>
                <input id="password" name="password" type="text" class="form-control" placeholder="Mật khẩu">
                <?php
                if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'password');
                }
                ?>
            </div>
            <div class="col-6 pb-3">
                <label for="address">Địa chỉ</label>
                <input id="address" name="address" type="text" value="<?php
                                                                        if (!empty($oldData)) {
                                                                            echo oldata($oldData, 'address');
                                                                        } ?>" class="form-control"
                    placeholder="Địa chỉ">
            </div>
            <div class="col-3 pb-3">
                <label for="group">Phân cấp người dùng</label>
                <select name="group" id="group" class="form-select form-control">
                    <?php
                    $getGroup = getAll("SELECT * FROM `groups`");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id']; ?>"
                            <?php echo ($oldData['group_id'] == $item['id']) ? 'selected' : false; ?>>
                            <?php echo $item['name']; ?></option>

                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-3 pb-3">
                <label for="status">Trạng thái TK</label>
                <select name="status" id="status" class="form-select form-control">
                    <option value="0" <?php echo ($oldData['status'] == 0) ? 'selected' : false; ?>>Chưa
                        kích hoạt</option>
                    <option value="1" <?php echo ($oldData['status'] == 1) ? 'selected' : false; ?>>Đã kích
                        hoạt</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Xác nhận</button>
        <a type="submit" href="?module=users&action=list" class="btn btn-primary btn-success">Quay lại</a>
    </form>


</div>


<?php
layout('footer');
