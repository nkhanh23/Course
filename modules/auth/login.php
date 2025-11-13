<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'tittle' => 'Đăng nhập hệ thống'
];


/*
- Validate dữ liệu đầu vào
- Check dữ liệu với csd (email và pass)
- Dữ liệu khớp -> token login -> insert vào bảng token_login để kiểm tra đăng nhập  

- Kiểm tra đăng nhập:
+ Gán token_login lên session
+ trong header -> lấy token từ session về và so khớp với token trong bảng token_login
+ nếu khớp thì điều hướng trang đích (không khớp điều hướng về login)
+ Điều hướng đến trang dashboard

- Đăng nhập tài khoản ở 1 nơi tại 1 thời điểm
*/



$shouldRedirect = false;

if (isPost()) {
    $filter = filterData();
    $errors = [];
    //validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email bắt buộc phải nhập';
    } else {
        if (!validateEmail($filter['email'])) {
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
        }
    }

    //validate mật khẩu
    if (empty(trim($filter['password']))) {
        $errors['password']['required'] = 'Mật khẩu bắt buộc nhập';
    } else {
        if (strlen(trim($filter['password'])) < 8) {
            $errors['password']['length'] = ' Mật khẩu phải trên 8 kí tự';
        }
    }

    if (empty($errors)) {
        //Kiem tra dữ liệu
        $email = $filter['email'];
        $password = $filter['password'];
        $checkEmail = getOne("SELECT * FROM users WHERE email ='$email'");

        if (!empty($checkEmail)) {
            if (!empty($password)) {
                $checkStatus = password_verify($password, $checkEmail['password']);
                if ($checkStatus) {
                    // TK chi login o 1 noi
                    $user_id = $checkEmail['id'];
                    $checkAlready = getRows("SELECT * FROM token_login WHERE user_id = $user_id");
                    if ($checkAlready > 0) {
                        setSessionFlash('msg', 'Tài khoản đang được đăng nhập ở 1 nơi khác, vui lòng thử lại sau.');
                        setSessionFlash('msg_type', 'danger');
                        redirect('?module=auth&action=login');
                    } else {
                        // Tạo token và insert vào bảng token_login
                        $token = sha1(uniqid() . time());
                        // gán token lên session
                        setSession('token_login', $token);
                        $data = [
                            'token' => $token,
                            'created_at' => date("Y:m:d H:i:s"),
                            'user_id' => $checkEmail['id'],
                        ];
                        $insertToken = insert('token_login', $data);
                        if ($insertToken) {
                            redirect('?module=dashboard&action=index');
                        } else {
                            setSessionFlash('msg', 'Đăng nhập không thành công.');
                            setSessionFlash('msg_type', 'danger');
                        }
                    }
                } else {
                    setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
                    setSessionFlash('msg_type', 'danger');
                }
            }
        } else {
            setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}
layout('header-auth', $data);
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorsArr  = getSessionFlash('errors');
?>



<div class="container">

    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <?php
                    if (!empty($msg) && !empty($msg_type)) {
                        getMsg($msg, $msg_type);
                    }
                    ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0">Đăng nhập</p>
                        </div>

                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="text" name="email" value="<?php
                                                                    if (!empty($oldData)) {
                                                                        echo oldata($oldData, 'email');
                                                                    }
                                                                    ?>" class="form-control form-control-lg"
                                placeholder="Nhập địa chỉ email" />
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'email');
                            }
                            ?>
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-3">
                            <input type="password" name="password" class="form-control form-control-lg"
                                placeholder="Nhập mật khẩu" />
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'password');
                            }
                            ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Checkbox -->
                            <div class="form-check mb-0">
                                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                                <label class="form-check-label" for="form2Example3">
                                    Remember me
                                </label>
                            </div>
                            <a href="<?php echo _HOST_URL; ?>?module=auth&action=forget" class="text-body">Quên mật
                                khẩu?</a>
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                            <p class="small fw-bold mt-2 pt-1 mb-0">Chưa có tài khoản? <a
                                    href="<?php echo _HOST_URL; ?>?module=auth&action=register" class="link-danger">Đăng
                                    ký</a></p>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

</div>

<?php
layout('footer');
