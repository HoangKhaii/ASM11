<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fruit_shop";
$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($connect, $sql);
$currentUser = mysqli_fetch_assoc($result);
if ($currentUser['role'] !== 'admin') {
    header("Location: ../wed.php");
    exit();
}
// Xử lý cập nhật thông tin admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $sql_update = "UPDATE users SET firstname='$firstname', lastname='$lastname', phone='$phone', address='$address' WHERE user_id='$user_id'";
    mysqli_query($connect, $sql_update);
    header('Location: settings.php?success=1');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../combined-styles.css">
    <style>
        body { background: #e8f5e9; font-family: 'Poppins', Arial, sans-serif; }
.admin-sidebar { min-height: 100vh; background: linear-gradient(135deg, #388e3c 0%, #a5d6a7 100%); color: #fff; box-shadow: 2px 0 8px #0001; }
.admin-sidebar .nav-link { color: #fff; font-weight: 500; border-radius: 8px; margin-bottom: 6px; transition: background 0.2s, color 0.2s; }
.admin-sidebar .nav-link.active, .admin-sidebar .nav-link:hover { background: #fff; color: #388e3c; }
.admin-sidebar .logo-admin { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; margin-bottom: 10px; }
.admin-header { background: #fff; border-bottom: 1px solid #a5d6a7; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
.admin-header .admin-info { display: flex; align-items: center; gap: 12px; }
.admin-header .avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #388e3c; }
.card { border-radius: 16px; box-shadow: 0 2px 12px #388e3c11; }
.card-header { background: #e8f5e9; border-radius: 16px 16px 0 0; font-weight: 600; color: #388e3c; }
.btn, .btn:focus { border-radius: 8px; font-weight: 500; }
label.form-label { font-weight: 500; color: #388e3c; }
.form-control { border-radius: 8px; }
@media (max-width: 991.98px) { .admin-sidebar { min-height: auto; } }
@media (max-width: 767.98px) { .admin-header { flex-direction: column; gap: 8px; } }

    </style>
</head>
<body>
    <div class="admin-header">
        <div class="d-flex align-items-center gap-2">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Admin Avatar" class="avatar">
            <span class="fw-bold text-success">Xin chào, <?php echo htmlspecialchars($currentUser['firstname']); ?>!</span>
        </div>
        <div class="admin-info">
            <a href="../wed.php" class="btn btn-outline-success"><i class="bi bi-arrow-left"></i> Về trang shop</a>
            <a href="../wed.php?logout=1" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-0 d-flex flex-column align-items-center py-4">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Admin Logo" class="logo-admin">
                <h4 class="mb-4 fw-bold">Admin Panel</h4>
                <nav class="nav flex-column w-100 px-3">
                    <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Dashboard</a>
                    <a class="nav-link" href="users.php"><i class="bi bi-people"></i> Users Management</a>
                    <a class="nav-link" href="products.php"><i class="bi bi-box-seam"></i> Products Management</a>
                    <a class="nav-link" href="orders.php"><i class="bi bi-cart"></i> Orders Management</a>
                    <a class="nav-link" href="categories.php"><i class="bi bi-tags"></i> Categories</a>
                    <a class="nav-link active" href="settings.php"><i class="bi bi-gear"></i> Settings</a>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-success"><i class="bi bi-gear"></i> Admin Settings</h2>
                </div>
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Cập nhật thông tin thành công!</div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Update Your Information</h5></div>
                    <div class="card-body">
                        <form method="POST" action="settings.php">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($currentUser['firstname']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($currentUser['lastname']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($currentUser['phone']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($currentUser['address']); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Changes</button>
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