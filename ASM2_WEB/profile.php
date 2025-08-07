<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fruit_shop";
$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// Lấy thông tin user
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($connect, $sql);
$user = mysqli_fetch_assoc($result);

$success = false;
$error = '';
// Cập nhật thông tin cá nhân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    // Validate phone
    if ($phone && !preg_match('/^[0-9]{10,11}$/', $phone)) {
        $error = 'Số điện thoại phải có 10-11 chữ số.';
    } elseif ($firstname && $lastname) {
        $sql_update = "UPDATE users SET firstname='".mysqli_real_escape_string($connect, $firstname)."', lastname='".mysqli_real_escape_string($connect, $lastname)."', phone='".mysqli_real_escape_string($connect, $phone)."', address='".mysqli_real_escape_string($connect, $address)."' WHERE user_id='$user_id'";
        if (mysqli_query($connect, $sql_update)) {
            $success = 'Cập nhật thông tin thành công!';
            $user['firstname'] = $firstname;
            $user['lastname'] = $lastname;
            $user['phone'] = $phone;
            $user['address'] = $address;
        } else {
            $error = 'Cập nhật thất bại. Vui lòng thử lại.';
        }
    }
}
// Đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if (password_verify($old_password, $user['password'])) {
        if ($new_password && $new_password === $confirm_password) {
            if (strlen($new_password) < 8) {
                $error = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
            } else {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                mysqli_query($connect, "UPDATE users SET password='$hash' WHERE user_id='$user_id'");
                $success = 'Đổi mật khẩu thành công!';
            }
        } else {
            $error = 'Mật khẩu mới không khớp.';
        }
    } else {
        $error = 'Mật khẩu cũ không đúng.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wed.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .profile-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #a5d6a7;
            background: #e8f5e9;
            margin-bottom: 10px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .profile-header h2 {
            font-weight: 700;
            color: #388e3c;
        }
        .btn-back-shop {
            position: absolute;
            left: 24px;
            top: 24px;
            z-index: 10;
        }
        @media (max-width: 767.98px) {
            .btn-back-shop {
                position: static;
                margin-bottom: 16px;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-light wedding-page">
    <div class="container py-4 position-relative">
        <a href="wed.php" class="btn btn-outline-success btn-lg fw-bold btn-back-shop"><i class="bi bi-arrow-left"></i> Quay lại trang chủ</a>
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="profile-header">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['firstname'].' '.$user['lastname']); ?>&background=a5d6a7&color=205723&size=128" alt="Avatar" class="profile-avatar">
                            <h2 class="mb-1"><?php echo htmlspecialchars($user['firstname'].' '.$user['lastname']); ?></h2>
                            <div class="text-muted mb-2"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <?php if ($success): ?>
                            <div class="alert alert-success text-center"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post" class="row g-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">Họ</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Tên</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" pattern="[0-9]{10,11}" title="Số điện thoại phải có 10-11 chữ số">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" name="update_profile" class="btn btn-success btn-lg">Cập nhật thông tin</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3 text-success"><i class="bi bi-key"></i> Đổi mật khẩu</h4>
                        <form method="post" class="row g-3">
                            <div class="col-12">
                                <label for="old_password" class="form-label">Mật khẩu cũ</label>
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                            </div>
                            <div class="col-12">
                                <label for="new_password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required>
                            </div>
                            <div class="col-12">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" name="change_password" class="btn btn-outline-success btn-lg">Đổi mật khẩu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>