<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Danh sách khóa học'
];
layout('header', $data);
layout('sidebar');


if (isPost()) {
    $filter = filterData();
    $errors = [];

    // validate fullname
    if (empty(trim($filter['name']))) {
        $errors['name']['required'] = 'Tên bắt buộc phải nhập';
    }

    // Validate slug
    if (empty(trim($filter['name']))) {
        $errors['name']['required'] = 'Đường dẫn bắt buộc phải nhập';
    }

    // Validate price
    if (empty($filter['phone'])) {
        $errors['phone']['required'] = 'Giá bắt buộc phải nhập';
    }

    // Validate price
    if (empty($filter['description'])) {
        $errors['description']['required'] = 'Mô tả bắt buộc phải nhập';
    }

    if (empty($errors)) {
        // Xử lý thumbnail upload lên
        $uploadDir = './templates/uploads';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // tạo mới thư mục upload nếu chưa có
        }

        $fileName = basename($_FILES['thumbnail']['name']);

        $targetFile = $uploadDir . time() . '-' . $fileName;

        $thumb = '';
        $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        if ($checkMove) {
            $thumb = $targetFile;
        }
        $dataInsert = [
            'name' => $filter['name'],
            'slug' => $filter['slug'],
            'price' => $filter['price'],
            'description' => $filter['description'],
            'thumbnail' => $thumb,
            'category_id' => $filter['category_id'],
            'created_at' => date('Y:m:d H:i:s')
        ];
        $insertStatus = insert('course', $dataInsert);
        if ($insertStatus) {
            setSessionFlash('msg', 'Thêm khóa học thành công.');
            setSessionFlash('msg_type', 'success');
            redirect('?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Thêm khóa học thất bại.');
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
<div class="container add-user">
    <?php
    if (!empty($msg) && !empty($msg_type)) {
        getMsg($msg, $msg_type);
    }
    ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 pb-3">
                <label for="name">Tên</label>
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
                <label for="slug">Đường dẫn</label>
                <input id="slug" name="slug" type="text" value="<?php
                                                                if (!empty($oldData)) {
                                                                    echo oldata($oldData, 'slug');
                                                                } ?>" class="form-control" placeholder="slug">
                <?php
                if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'slug');
                }
                ?>
            </div>
            <div class="col-6 pb-3">
                <label for="description">Mô tả khóa học</label>
                <input id="description" name="description" type="text" value="<?php
                                                                                if (!empty($oldData)) {
                                                                                    echo oldata($oldData, 'description');
                                                                                } ?>" class="form-control"
                    placeholder="Mô tả khóa học">
                <?php
                if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'description');
                }

                ?>
            </div>
            <div class="col-6 pb-3">
                <label for="price">Giá</label>
                <input id="price" name="price" type="text" class="form-control" placeholder="Mật khẩu">
                <?php
                if (!empty($errorsArr)) {
                    echo formError($errorsArr, 'price');
                }
                ?>
            </div>
            <div class="col-6 pb-3">
                <label for="address">Thumbnail</label>
                <input id="address" name="address" type="file" class="form-control" placeholder="Thumbnail">
                <img width="200px" id="previewImage" class="preview-image p-3" src="#" alt="">
            </div>
            <div class="col-3 pb-3">
                <label for="catefory_id">Lĩnh vực</label>
                <select name="catefory_id" id="catefory_id" class="form-select form-control">
                    <?php
                    $getGroup = getAll("SELECT * FROM course_category");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>

                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
    <button type="submit" class="btn btn-success">Xác nhận</button>

</div>

<script>
    // Hàm giúp chuyển text thành slug
    function createSlug(string) {
        return strig.toLowerCase()
            .normalize('NFD') // chuyển ký tự có dấu thành tổ hợp: é -> e + '
            .replace(/[\u0300-\u036f]/g, '') // xoá dấu
            .replace(/đ/g, 'd') // thay đ -> d
            .replace(/[^a-z0-9\s-]/g, '') // xoá ký tự đặc biệt
            .trim() // bỏ khoảng trắng đầu/cuối
            .replace(/\s+/g, '-') // thay khoảng trắng -> -
            .replace(/-+/g, '-'); // bỏ trùng dấu -
    }

    document.getElementById('name').addEventListener('input', function() {
        const getValue = this.value;
        document.getElementById('slug').value = createSlug(getValue);
    });
</script>

<script>
    const thumbInput = document.getElementById('thumbnail');
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

<?php
layout('footer');
