<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}
require_once './templates/layouts/header-auth.php';

if (isPost()) {
    $filter = filterData();
    $errors = [];

    // Validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email bắt buộc phải nhập';
    } else {
        // Đúng định dạng email, email này đã tồn tại trong CSDL chưa
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
        }
    }
    if (empty($errors)) {
        //xử lý và gửi mail
        if (!empty($filter['email'])) {
            $email = $filter['email'];

            $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'");
            if (!empty($checkEmail)) {
                // Update forgot_token vào bảng users.
                $forgot_token = sha1(uniqid() . time());
                $data = [
                    'forget_token' => $forgot_token
                ];
                $condition = "id=" . $checkEmail['id'];
                $updateStatus = update('users', $data, $condition);
                if ($updateStatus) {
                    $emailTo = $filter['email'];
                    $subject = 'Yêu cầu đặt lại mật khẩu - nkhanh';

                    $content = '<div style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 30px;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
    
    <div style="background-color: #007bff; color: #ffffff; padding: 20px; text-align: center;">
      <h2 style="margin: 0;">Đặt lại mật khẩu của bạn</h2>
    </div>
    
    <div style="padding: 30px; color: #333333; line-height: 1.6;">
      <p>Xin chào</p>
      <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn trên hệ thống <strong>nkhanh</strong>.</p>
      <p>Để tạo mật khẩu mới, vui lòng nhấn vào nút bên dưới:</p>

      <div style="text-align: center; margin: 30px 0;">
        <a href="' . _HOST_URL . '/?module=auth&action=reset&token=' . $forgot_token . '" 
           style="background-color: #007bff; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; display: inline-block;">
          Đặt lại mật khẩu
        </a>
      </div>

      <p>Nếu nút trên không hoạt động, bạn có thể truy cập đường link sau:</p>
      <p style="word-break: break-all; color: #007bff;">' . _HOST_URL . '/?module=auth&action=reset&token=' . $forgot_token . '</p>
      
      <p style="font-size: 14px; color: #888; margin-top: 20px;">Lưu ý: Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email. Tài khoản của bạn vẫn an toàn.</p>

      <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
      
      <p style="text-align: center; font-size: 14px; color: #888;">Cảm ơn bạn đã tin tưởng và ủng hộ <strong>nkhanh</strong> ❤️</p>
    </div>
  </div>
</div>';

                    // Gửi email
                    sendMail($emailTo, $subject, $content);

                    setSessionFlash('msg', 'Reset mật khẩu thành công.');
                    setSessionFlash('msg_type', 'success');
                } else {
                    setSessionFlash('msg', 'Đã có lỗi. Vui lòng thử lại sau');
                    setSessionFlash('msg_type', 'danger');
                }
            }
        }
    } else {
        setSessionFlash('msg', 'Vui lòng kiểm tra lại dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
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
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0">Quên mật khẩu</p>
                        </div>

                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="email" name="email" value="<?php
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

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

</div>

<?php
require_once './templates/layouts/footer.php';
