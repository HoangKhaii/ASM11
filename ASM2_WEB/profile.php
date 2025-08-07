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
    if ($firstname && $lastname) {
        $sql_update = "UPDATE users SET firstname='".mysqli_real_escape_string($connect, $firstname)."', lastname='".mysqli_real_escape_string($connect, $lastname)."', phone='".mysqli_real_escape_string($connect, $phone)."', address='".mysqli_real_escape_string($connect, $address)."' WHERE user_id='$user_id'";
        if (mysqli_query($connect, $sql_update)) {
            $success = true;
            $user['firstname'] = $firstname;
            $user['lastname'] = $lastname;
            $user['phone'] = $phone;
            $user['address'] = $address;
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
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            mysqli_query($connect, "UPDATE users SET password='$hash' WHERE user_id='$user_id'");
            $success = true;
        } else {
            $error = 'New passwords do not match.';
        }
    } else {
        $error = 'Old password is incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wed.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light wedding-page">
    <div class="container py-4">
        <a href="wed.php" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Back to Shop</a>
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="fw-bold mb-4">Profile</h2>
                        <?php if ($success): ?>
                            <div class="alert alert-success">Update successful!</div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post" class="row g-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>">
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" name="update_profile" class="btn btn-success btn-lg">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="fw-bold mb-3">Change Password</h4>
                        <form method="post" class="row g-3">
                            <div class="col-12">
                                <label for="old_password" class="form-label">Old Password</label>
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                            </div>
                            <div class="col-12">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="col-12">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" name="change_password" class="btn btn-success btn-lg">Change Password</button>
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