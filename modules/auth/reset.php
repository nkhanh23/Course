<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}
$data = [
    'tittle' => 'Reset mật khẩu'
];
layout('header-auth', $data);

$filterGet = filterData('get');

if (!empty($filterGet['token'])) {
    $tokenReset = $filterGet['token'];
}

if (!empty($tokenReset)) {
    // Check token có chính xác hay không
    $checkToken = getOne("SELECT * FROM users WHERE forget_token = '$tokenReset'");
    if (!empty($checkToken)) {
        if (isPost()) {
            $filter = filterData();
            $errors = [];

            // Validate Password MK > 6 ký tự
            if (empty(trim($filter['password']))) {
                $errors['password']['required'] = 'Mật khẩu bắt buộc phải nhập';
            } else {
                if (strlen(trim($filter['password'])) < 6) {
                    $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 ký tự';
                }
            }

            // Validate confirm password
            if (empty(trim($filter['confirm_pass']))) {
                $errors['confirm_pass']['required'] = 'Vui lòng nhập lại mật khẩu';
            } else {
                if (trim($filter['password']) !== trim($filter['confirm_pass'])) {
                    $errors['confirm_pass']['like'] = 'Mật khẩu nhập lại không khớp';
                }
            }

            if (empty($errors)) {
                $password = password_hash($filter['password'], PASSWORD_DEFAULT);
                $data = [
                    'password' => $password,
                    'forget_token' => null,
                    'updated_at' => date('Y:m:d H:i:s')
                ];

                $condition = "id=" . $checkToken['id'];
                $updateStatus = update('users', $data, $condition);

                if ($updateStatus) {
                    $emailTo = $checkToken['email'];
                    // Subject từ nội dung trong ảnh
                    $subject = 'Đổi mật khẩu thành công!!';

                    // Content được cập nhật nội dung từ ảnh, giữ nguyên định dạng HTML
                    $content = '<div style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 30px;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
    
    <div style="background-color: #007bff; color: #ffffff; padding: 20px; text-align: center;">
      <h2 style="margin: 0;">Đổi mật khẩu thành công!</h2>
    </div>
    
    <div style="padding: 30px; color: #333333; line-height: 1.6;">
      
      <p>Chúc mừng bạn đã đổi mật khẩu thành công trên nkhanh.</p>
      
      <p>Nếu không phải bạn thao tác đổi mật khẩu thì hãy liên hệ ngay với admin.</p>

      <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
      
      <p style="text-align: center; font-size: 14px; color: #888;">Cảm ơn các bạn đã ủng hộ nkhanh!!!</p>
    </div>
  </div>
</div>';

                    // Gửi email
                    sendMail($emailTo, $subject, $content);
                    setSessionFlash('msg', 'Đổi mật khẩu thành công.');
                    setSessionFlash('msg_type', 'success');
                } else {
                    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại sau.');
                    setSessionFlash('msg_type', 'danger');
                }
            } else {
                setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu vào.');
                setSessionFlash('msg_type', 'danger');
                setSessionFlash('oldData', $filter);
                setSessionFlash('errors', $errors);
            }
        }
    } else {
        getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
    }
} else {
    getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
}


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
                    <form style="width: 23rem;" method="POST" action="" enctype="multipart/form-data">

                        <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Đặt lại mật khẩu</h3>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="password" name="password" class="form-control form-control-lg" />
                            <label class="form-label" for="form2Example28">Mật khẩu mới</label>
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'password');
                            }
                            ?>
                        </div>


                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="password" name="confirm_pass" class="form-control form-control-lg" />
                            <label class="form-label" for="form2Example28">Nhập lại mật khẩu</label>
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'confirm_pass');
                            }
                            ?>
                        </div>
                        <div class="pt-1 mb-4">
                            <button data-mdb-button-init data-mdb-ripple-init class="btn btn-info btn-lg btn-block"
                                type="submit">Gửi</button>
                        </div>
                        <p style="margin-top: 15px;"><a href="<?php echo _HOST_URL; ?>?module=auth&action=login"
                                class="link-danger">Đăng nhập</a></p>

                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
layout('footer');
