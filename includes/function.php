<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

//Hàm thay đổi tiêu đề tùy thuộc vào trang đang mở
function layout($layoutName, $data = [])
{

    if (file_exists(_PATH_URL_TEMPLATES . '/layouts/' . $layoutName . '.php')) {
        require_once _PATH_URL_TEMPLATES . '/layouts/' . $layoutName . '.php';
    }
}

//Hàm gửi mail
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($emailTo, $subject, $content)
{

    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function


    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'nkhanh2305@gmail.com';                     //SMTP username
        $mail->Password   = 'cbxv eatt imhj bsks';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('nkhanh2305@gmail.com', 'Nkhanh Course');
        $mail->addAddress($emailTo,);     //Add a recipient

        //Content
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;

        //Custom connection options
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'  => true,
                'verify_depth' => 3,
                'allow_self_signed' => true,
            )
        );

        return $mail->send();
    } catch (Exception $e) {
        echo "Gửi thất bại. Mailer Error: {$mail->ErrorInfo}";
    }
}


//Kiểm tra phương thức post
function isPost()
{

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

//Kiểm tra phương thức GET
function isGet()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return  true;
    }
    return false;
}


//Lọc dữ liệu người dùng nhập vào
/*

array = [
    'key1' => '1' // giá trị
    ''key2' => [1,2,3,4,5] //mảng
]

*/
function filterData($method = '')
{
    $filterArray = [];
    if (empty($method)) {
        if (isGet()) {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key); //loai bo cac the html
                    //Kiểm tra người dùng nhập vào giá trị hay là mảng
                    if (is_array($value)) {
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY); //FILTER_REQUIRE_ARRAY là yêu cầu đầu vào phải là mảng
                    } else {
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
        if (isPost()) {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
    } else {
        if ($method  == 'get') {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        } else if ($method == 'post') {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
    }
    return $filterArray;
}

//validate email
function validateEmail($email)
{
    if (!empty($email)) {
        $checkEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    return $checkEmail;
}

//validate int
function validateInt($number)
{
    if (!empty($number)) {
        $checkNumber = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
    }
    return $checkNumber;
}

//validate phone 0123456789
function isPhone($number)
{
    //Kiểm tra số đầu tiên phải số 0 không
    $phoneFirst = false;
    if ($number[0] == '0') {
        $phoneFirst = true;
        $number = substr($number, 1);
    }
    //Kiểm tra 9 số còn lại có phải số nguyên không
    $phoneCheck = false;
    if (validateInt($number)) {
        $phoneCheck = true;
    }

    if ($phoneFirst && $phoneCheck) {
        return true;
    }
    return false;
}

// thông báo lỗi
function getMsg($msg, $type = 'success')
{
    echo '<div class="annouce-message alert alert-' . $type . '"> ';
    echo $msg;
    echo ' </div>';
}

// hiển thị lỗi
function formError($errors, $fieldName)
{
    return (!empty($errors[$fieldName])) ? '<div class="error">' . reset($errors[$fieldName]) . '  </div>' : false;
}

// Hàm hiển thị lại giá trị cũ
function oldata($oldData, $filedName)
{
    return !empty($oldData[$filedName]) ? $oldData[$filedName] : NULL;
}


// Hàm chuyển hướng
function redirect($path, $pathFull = false)
{
    if ($pathFull) {
        header("Location: $path");
        exit();
    } else {
        $url = _HOST_URL . $path;
        header("Location: $url");
        exit();
    }
}




// Hàm checkLogin
function isLogin()
{
    $checkLogin = false;
    $tokenLogin = getSession('token_login');
    $checkToken = getOne("SELECT * FROM token_login WHERE token = '$tokenLogin'");
    if (!empty($checkToken)) {
        $checkLogin = true;
    } else {
        removeSession('token_login');
    }

    return $checkLogin;
}
