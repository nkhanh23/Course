<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Đăng ký tài khoản'
];
layout('header-auth', $data);

$msg = $msg_type = null;
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
        $errors['password']['required'] = 'Mật khẩu bắt buộc phải nhập';
    } else {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = 'Mật khẩu phải lớn hơn 6 ký tự';
        }
    }

    // Validate confirm password
    if (empty(trim($filter['password']))) {
        $errors['confirm_pass']['required'] = 'Vui lòng nhập lại mật khẩu';
    } else {
        if (trim($filter['password']) !== trim($filter['confirm_pass'])) {
            $errors['confirm_pass']['like'] = 'Mật khẩu nhập lại không khớp';
        }
    }

    if (empty($errors)) {
        $activeToken = sha1(uniqid() . time());
        $data = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT),
            'active_token' => $activeToken,
            'group_id' => 1,
            'created_at' => date('Y:m:d H:i:s'),

        ];
        $insertStatus = insert('users', $data);

        if ($insertStatus) {
            $emailTo = $filter['email'];
            $subject = 'Kích hoạt tài khoản';
            $content = '<div style="font-family: Arial, sans-serif; background-color: #f5f7fa; padding: 30px;">
  <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
    
    <div style="background-color: #007bff; color: #ffffff; padding: 20px; text-align: center;">
      <h2 style="margin: 0;">Kích hoạt tài khoản của bạn</h2>
    </div>
    
    <div style="padding: 30px; color: #333333; line-height: 1.6;">
      <p>Xin chào <strong>' . htmlspecialchars($filter["fullname"]) . '</strong>,</p>
      <p>Chúc mừng bạn đã đăng ký tài khoản thành công trên hệ thống <strong>nkhanh</strong>!</p>
      <p>Để kích hoạt tài khoản, vui lòng nhấn vào nút bên dưới:</p>

      <div style="text-align: center; margin: 30px 0;">
        <a href="' . _HOST_URL . '/?module=auth&action=active&token=' . $activeToken . '" 
           style="background-color: #007bff; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; display: inline-block;">
          Kích hoạt tài khoản
        </a>
      </div>

      <p>Nếu nút trên không hoạt động, bạn có thể truy cập đường link sau:</p>
      <p style="word-break: break-all; color: #007bff;">' . _HOST_URL . '/?module=auth&action=active&token=' . $activeToken . '</p>
      
      <hr style="border: none; border-top: 1px solid #eee; margin: 25px 0;">
      
      <p style="text-align: center; font-size: 14px; color: #888;">Cảm ơn bạn đã tin tưởng và ủng hộ <strong>nkhanh</strong> ❤️</p>
    </div>
  </div>
</div>';
            sendMail($emailTo, $subject, $content);

            setSessionFlash('msg', 'Đăng kí thành công, vui lòng kích hoạt tài khoản');
            setSessionFlash('msg_type', 'success');
        } else {
            setSessionFlash('msg', 'Đăng kí không thành công, vui lòng thử lại sau');
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
$errorsArr  = getSessionFlash('errors');


?>
<div class="container">
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/image/draw2.webp" class="img-fluid"
                        alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <?php
                    if (!empty($msg) && !empty($msg_type)) {
                        getMsg($msg, $msg_type);
                    }
                    ?>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                            <h2 class=" fw-normal mb-5 me-3">Đăng ký tài khoản</h2>
                        </div>
                        <!--   Name, email, sdt, mật khẩu, nhập lại mk -->

                        <div data-mdb-input-init class="form-outline mb-4">
                            <input name="fullname" type="text" value="<?php
                                                                        if (!empty($oldData)) {
                                                                            echo oldata($oldData, 'fullname');
                                                                        }
                                                                        ?>" class="form-control form-control-lg"
                                placeholder="Họ tên" />
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'fullname');
                            }
                            ?>
                        </div>

                        <!-- Nhập email -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input name="email" type="text" value="<?php
                                                                    if (!empty($oldData)) {
                                                                        echo oldata($oldData, 'email');
                                                                    } ?>" class="form-control form-control-lg"
                                placeholder="Địa chỉ email" />
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'email');
                            }
                            ?>
                        </div>

                        <!-- Nhập số điện thoại -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input name="phone" type="text" value="<?php
                                                                    if (!empty($oldData)) {
                                                                        echo oldata($oldData, 'phone');
                                                                    } ?>" class="form-control form-control-lg"
                                placeholder="Nhập số điện thoại" />
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'phone');
                            }

                            ?>
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-3">
                            <input name="password" type="password" id="form3Example4"
                                class="form-control form-control-lg" placeholder="Nhập mật khẩu" />
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'password');
                            }
                            ?>
                        </div>

                        <!-- Nhập lại mật khẩu -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input name="confirm_pass" type="password" class="form-control form-control-lg"
                                placeholder="Nhập lại mật khẩu" />
                            <?php
                            if (!empty($errorsArr)) {
                                echo formError($errorsArr, 'confirm_pass');
                            }
                            ?>
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" data-mdb-button-init data-mdb-ripple-init
                                class="btn btn-primary btn-lg" style="padding-left: 2.5rem; padding-right: 2.5rem;">Đăng
                                ký</button>
                            <p class="small fw-bold mt-2 pt-1 mb-0">Bạn đã có tài khoản? <a
                                    href="<?php echo _HOST_URL; ?>?module=auth&action=login" class="link-danger">Đăng
                                    nhập
                                    ngay</a></p>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </section>
</div>

<?php
require_once './templates/layouts/footer.php';