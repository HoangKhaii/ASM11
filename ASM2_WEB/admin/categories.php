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
$sql = "SELECT * FROM categories ORDER BY created_at DESC";
$result = mysqli_query($connect, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories Management - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../combined-styles.css">
    <style>
        body {
    background: #e8f5e9;
    font-family: 'Poppins', Arial, sans-serif;
}

.admin-sidebar {
    min-height: 100vh;
    background: linear-gradient(135deg,#388e3c 0%,#66bb6a 100%);
    color: #fff;
    box-shadow: 2px 0 8px #0001;
}

.admin-sidebar .nav-link {
    color: #fff;
    font-weight: 500;
    border-radius: 8px;
    margin-bottom: 6px;
    transition: background 0.2s, color 0.2s;
}

.admin-sidebar .nav-link.active,
.admin-sidebar .nav-link:hover {
    background: #fff;
    color: #388e3c;
}

.admin-sidebar .logo-admin {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    margin-bottom: 10px;
}

.admin-header {
    background: #fff;
    border-bottom: 1px solid #a5d6a7;
    padding: 12px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 100;
}

.admin-header .admin-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.admin-header .avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #388e3c;
}

.card {
    border-radius: 16px;
    box-shadow: 0 2px 12px #388e3c;
}

.card-header {
    background: #e8f5e9;
    border-radius: 16px 16px 0 0;
    font-weight: 600;
    color: #388e3c;
}

.btn,
.btn:focus {
    border-radius: 8px;
    font-weight: 500;
    background-color: #388e3c;
    color: #fff;
    border: none;
}

.btn:hover {
    background-color: #66bb6a;
    color: #fff;
}

.badge {
    font-size: 0.95em;
    border-radius: 8px;
    padding: 0.5em 0.8em;
    background-color: #388e3c;
    color: #fff;
}

.table thead th {
    background: #e8f5e9;
    color: #388e3c;
    font-weight: 600;
}

.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #e8f5e9;
}

.table-striped>tbody>tr:hover {
    background: #c8e6c9;
}

@media (max-width: 991.98px) {
    .admin-sidebar {
        min-height: auto;
    }
}

@media (max-width: 767.98px) {
    .admin-header {
        flex-direction: column;
        gap: 8px;
    }
}

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
                    <a class="nav-link active" href="categories.php"><i class="bi bi-tags"></i> Categories</a>
                    <a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-success"><i class="bi bi-tags"></i> Categories Management</h2>
                </div>
                <!-- Categories Table -->
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">All Categories</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($cat = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $cat['category_id']; ?></td>
                                        <td><?php echo htmlspecialchars($cat['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($cat['description']); ?></td>
                                        <td>
                                            <?php if ($cat['status'] == 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($cat['created_at'])); ?></td>
                                        <td>
                                            <a href="edit_category.php?id=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                            <a href="categories.php?delete=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this category?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 