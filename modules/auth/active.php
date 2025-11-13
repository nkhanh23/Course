<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}
require_once './templates/layouts/header-auth.php';
//Kiểm tra xem active_token ở url có giống với active_token trong database không
// Update trường status trong bảng users -> 1 (đã kích hoạt) + xóa active_token đi để lần sau không truy cập được trang active nữa

$filter = filterData('get');
if (!empty($filter['token'])) :
    $token = $filter['token'];
    $checkToken = getOne("SELECT * FROM users WHERE active_token = '$token'");
?>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <?php if (!empty($checkToken)):

                    $data = [
                        'status' => 1,
                        'active_token' => NULL,
                        'updated_at' => date('Y:m:d H:i:s')
                    ];
                    $condition = "id=" . $checkToken['id'];
                    update('users', $data, $condition);
                ?>

                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <form>
                            <div class="divider d-flex align-items-center my-4">
                                <h3 class="text-center fw-bold mx-3 mb-0">Kích hoạt tài khoản thành công</h3>
                            </div>

                            <div class="text-center text-lg-start mt-4 pt-2">
                                <p class="small fw-bold mt-2 pt-1 mb-0"><a
                                        href="<?php echo _HOST_URL; ?>?module=auth&action=login" class="link-danger">Đăng
                                        nhập</a></p>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <form>
                            <div class="divider d-flex align-items-center my-4">
                                <h3 class="text-center fw-bold mx-3 mb-0">Kích hoạt tài khoản không thành công. Đường link đã
                                    hết hạn</h3>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php else: ?>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <form>
                        <div class="divider d-flex align-items-center my-4">
                            <h3 class="text-center fw-bold mx-3 mb-0">Trang kích hoạt đã hết hạn hoặc không tồn tại</h3>
                        </div>
                        <div class="text-center text-lg-start mt-4 pt-2">
                            <p class="small fw-bold mt-2 pt-1 mb-0"><a
                                    href="<?php echo _HOST_URL; ?>?module=auth&action=login" class="link-danger">Đăng
                                    nhập</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
<?php
endif;
?>


<?php
require_once './templates/layouts/footer.php';
