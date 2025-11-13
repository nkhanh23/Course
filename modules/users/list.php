<?php
if (!defined('_nkhanh')) {
    die('Truy cập không hợp lệ');
}

$data = [
    'title' => 'Danh sách người dùng'
];
layout('header', $data);
layout('side_bar');


$filter = filterData();
$chuoiWhere = '';
$group = '0';
$keyword = '';

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }
    if (isset($filter['group'])) {
        $group = $filter['group'];
    }

    if (!empty($keyword)) {
        if (strpos($chuoiWhere, 'WHERE') == false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "a.fullname LIKE '%$keyword%' OR a.email LIKE '%$keyword%' ";
    }

    if (!empty($group)) {
        if (strpos($chuoiWhere, 'WHERE') == false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= " a.group_id = $group ";
    }
}

//Xu ly phan trang
$maxData = getRows("SELECT id FROM users"); // tổng dữ liệu
$perPage = 1; //số dòng dữ liệu 1 trang
$maxPage = ceil($maxData / $perPage); //tính max page
$offset = 0;
$page = 1;
//get page
if (isset($filter['page'])) {
    $page = $filter['page'];
}

if ($page > $maxPage || $page < 1) {
    $page = 1;
}

if (isset($page)) {
    $offset = ($page - 1) * $perPage;
}

$getDetailUser = getAll("SELECT a.id, a.fullname, a.email, a.created_at, b.name
FROM users a INNER JOIN `groups` b
ON a.group_id = b.id $chuoiWhere
ORDER BY created_at DESC
LIMIT $offset, $perPage
");
$getGroup = getAll("SELECT * FROM `groups`");

// Xử lý query
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    //Xu ly &page= dồn trên url
    $queryString = str_replace('&page=' . $page, '', $queryString);
}

if ($group > 0 || !empty($keyword)) {
    $maxData2 = getRows("SELECT id FROM users a $chuoiWhere");
    $maxPage = ceil($maxData2 / $perPage);
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');


?>

<div class="container grip-user">
    <div class="container-fluid">
        <a href="?module=users&action=add" class="btn btn-success mb-3"><i class="fa-solid fa-plus"></i>Thêm mới người
            dùng</a>
        <?php
        if (!empty($msg) && !empty($msg_type)) {
            getMsg($msg, $msg_type);
        }
        ?>
        <form class="mb-3" action="" method="get">
            <input type="hidden" name="module" value="users">
            <input type="hidden" name="action" value="list">
            <div class="row">
                <div class="col-3">
                    <select class="form-select form-control" name="group" id="">
                        <option value="">Nhóm người dùng</option>
                        <?php
                        foreach ($getGroup as $item):
                        ?>
                            <option value="<?php echo $item['id']; ?>"
                                <?php echo ($group == $item['id']) ? 'selected' : false; ?>><?php echo $item['name']; ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="1">1</option>
                    </select>
                </div>
                <div class="col-7">
                    <input type="text" class="form-control" value="<?php echo (!empty($keyword)) ? $keyword : false; ?>"
                        name="keyword" placeholder="Nhập thông tin tìm kiếm...">
                </div>
                <div class="col-2"><button class="btn btn-primary" type="submit">Tìm kiếm</button></div>
            </div>
        </form>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Họ tên</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ngày đăng ký</th>
                    <th scope="col">Nhóm</th>
                    <th scope="col">Phân quyền</th>
                    <th scope="col">Sửa</th>
                    <th scope="col">Xoá</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($getDetailUser as $key => $item): ?>
                    <tr>
                        <th scope="row"><?php echo $key + 1; ?></th>
                        <td><?php echo $item['fullname']; ?></td>
                        <td><?php echo $item['email']; ?></td>
                        <td><?php echo $item['created_at']; ?></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><a href="?module=users&action=permission&id=<?php echo $item['id']; ?>"
                                class="btn btn-primary">Phân quyền</a></td>
                        <td><a href="?module=users&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-warning"><i
                                    class="fa-solid fa-pencil"></i></a></td>
                        <td><a href="?module=users&action=delete&id=<?php echo $item['id']; ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xoá không?')" class="btn btn-danger"><i
                                    class="fa-solid fa-trash"></i></a></td>
                    </tr>
                <?php
                endforeach;
                ?>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <!-- Xu ly nut truoc -->
                <?php
                if ($page > 1):
                ?>
                    <li class="page-item"><a class="page-link"
                            href="?module=users&action=list&page=<?php echo $page - 1; ?>">Trước</a></li>
                <?php endif;
                ?>
                <!-- Tinh vi tri bat dau -->
                <?php
                $start = $page - 1;
                if ($start < 1) {
                    $start = 1;
                }
                ?>
                <?php
                if ($start > 1):
                ?>
                    <li class="page-item"><a class="page-link"
                            href="?module=users&action=list&page=<?php echo $page - 1; ?>">...</a></li>
                <?php endif;
                $end = $page + 1;
                if ($end > $maxPage) {
                    $end = $maxPage;
                }
                ?>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : false; ?>"><a class="page-link"
                            href="?module=users&action=list&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>

                <?php
                endfor;
                if ($end < $maxPage):
                ?>
                    <li class="page-item"><a class="page-link"
                            href="?<?php echo $queryString; ?>&page=<?php echo $page + 1; ?>">...</a></li>
                <?php endif;
                $end = $page + 2;
                if ($end > $maxPage) {
                    $end = $maxPage;
                }
                ?>
                <!-- Xu ly nut sau -->
                <?php
                if ($page < $maxPage):
                ?>
                    <li class="page-item"><a class="page-link"
                            href="?<?php echo $queryString; ?>&page=<?php echo $page + 1; ?>">Sau</a></li>

                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<?php
layout('footer');
