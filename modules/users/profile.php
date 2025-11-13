<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Chi tiết người dùng'
];
//LƯU Ý: ĐOẠN CODE DƯỚI ĐÂY CHỈ DÙNG ĐỂ TEST KHI KHÔNG CÓ CHỨC NĂNG ĐĂNG NHẬP, KHÔNG NHẬP VÀO TRANG THỰC TẾ
setSession('token_login', '51dbd984e5aca24d9388e2fb140e4d3134adfb1a');
layout('header', $data);
layout('side_bar');

$getData = filterData('get');
// Lấy thông tin user
$token = getSession('token_login');
if (!empty($token)) {
    $checkTokenLogin = getOne("SELECT * FROM token_login WHERE token='$token'");
    if (!empty($checkTokenLogin)) {
        $user_id = $checkTokenLogin['user_id'];
        $detailUser = getOne("SELECT * FROM users WHERE id=$user_id");
    }
}

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

        $dataUpdate = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'group_id' => $filter['group'],
            'address' => (!empty($filter['address']) ? $filter['address'] : null),
            'updated_at' => date('Y:m:d H:i:S')
        ];

        if (!empty($_FILES['avatar']['name'])) {
            // Xử lý avatar upload lên
            $uploadDir = './templates/uploads/';

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); // tạo mới thư mục upload nếu chưa có
            }

            $fileName = basename($_FILES['avatar']['name']);

            $targetFile = $uploadDir . time() . '-' . $fileName;

            $thumb = '';
            $checkMove = move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile);

            if ($checkMove) {
                $thumb = $targetFile;
            }

            $dataUpdate['avatar'] = $thumb;
        }

        if (!empty($filter['password'])) {
            $dataUpdate['password'] = password_hash($filter['password'], PASSWORD_DEFAULT);
        }
        $condition = "id=" . $user_id;
        $updateStatus = update('users', $dataUpdate, $condition);
        if ($updateStatus) {
            setSessionFlash('msg', 'Cập nhật thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=profile');
        } else {
            setSessionFlash('msg', 'Cập nhật thất bại.');
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

if (empty($oldData) && !empty($detailUser)) {
    $oldData = $detailUser;
}
$errorsArr  = getSessionFlash('errors');


?>

<div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <section class="vh-100" style="background-color: #f4f5f7;">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col col-lg-6 mb-4 mb-lg-0">
                        <div class="card mb-3" style="border-radius: .5rem;">
                            <div class="row g-0">
                                <div class="col-md-4 gradient-custom text-center text-white"
                                    style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                                    <img id="previewImage" class="preview-image"
                                        src="<?php echo _HOST_URL; ?><?php echo !empty($oldData['avatar']) ? $oldData['avatar'] : false; ?>"
                                        alt="Avatar" class="img-fluid my-5" style="width: 80px;" />
                                    <input id="avatar" name="avatar" type="file" class="form-control"
                                        placeholder="Thumbnail">
                                    <h5>Marie Horwitz</h5>
                                    <p>Web Designer</p>
                                    <i class="far fa-edit mb-5"></i>
                                    <button type="submit" class="btn btn-success">Xác nhận</button>

                                </div>
                                <div class="col-md-8">
                                    <div class="card-body p-4">
                                        <h6>Information</h6>
                                        <hr class="mt-0 mb-4">
                                        <div class="row pt-1">
                                            <div class="col-6 mb-3">
                                                <h6>Họ và tên</h6>
                                                <input id="fullname" name="fullname" type="text" value="<?php
                                                                                                        if (!empty($oldData)) {
                                                                                                            echo oldata($oldData, 'fullname');
                                                                                                        }
                                                                                                        ?>"
                                                    class="form-control" placeholder="Họ tên">
                                                <?php
                                                if (!empty($errorsArr)) {
                                                    echo formError($errorsArr, 'fullname');
                                                }
                                                ?>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <h6>Email</h6>
                                                <input id="email" name="email" type="text" value="<?php
                                                                                                    if (!empty($oldData)) {
                                                                                                        echo oldata($oldData, 'email');
                                                                                                    } ?>"
                                                    class="form-control" placeholder="Email">
                                                <?php
                                                if (!empty($errorsArr)) {
                                                    echo formError($errorsArr, 'email');
                                                }
                                                ?>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <h6>Số điện thoại</h6>
                                                <input id="phone" name="phone" type="text" value="<?php
                                                                                                    if (!empty($oldData)) {
                                                                                                        echo oldata($oldData, 'phone');
                                                                                                    } ?>"
                                                    class="form-control" placeholder="Số điện thoại">
                                                <?php
                                                if (!empty($errorsArr)) {
                                                    echo formError($errorsArr, 'phone');
                                                }
                                                ?>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <h6>Mật khẩu</h6>
                                                <input id="password" name="password" type="text" class="form-control"
                                                    placeholder="Mật khẩu">
                                                <?php
                                                if (!empty($errorsArr)) {
                                                    echo formError($errorsArr, 'password');
                                                }
                                                ?>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <h6>Địa chỉ</h6>
                                                <input id="password" name="address" type="text" class="form-control"
                                                    placeholder="Địa chỉ">
                                                <?php
                                                if (!empty($errorsArr)) {
                                                    echo formError($errorsArr, 'address');
                                                }
                                                ?>
                                            </div>

                                        </div>
                                        <div class="d-flex justify-content-start">
                                            <a href="#!"><i class="fab fa-facebook-f fa-lg me-3"></i></a>
                                            <a href="#!"><i class="fab fa-twitter fa-lg me-3"></i></a>
                                            <a href="#!"><i class="fab fa-instagram fa-lg"></i></a>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </section>

    </form>
</div>



<?php
layout('footer');
?>
<script>
    const thumbInput = document.getElementById('avatar');
    const previewImg = document.getElementById('previewImage');

    thumbInput.addEventListener('change', function() {
        const $file = this.file[0];
        if (file) {

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.setAttribute('src', e.target.result);
                previewImg.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.style.display = 'none';
        }
    });
</script>