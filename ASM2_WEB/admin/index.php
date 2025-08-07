<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fruit_shop";
$connect = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($connect, $sql);
$currentUser = mysqli_fetch_assoc($result);

// Thống kê
$sql_users = "SELECT COUNT(*) as total_users FROM users";
$result_users = mysqli_query($connect, $sql_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'];

$sql_products = "SELECT COUNT(*) as total_products FROM products";
$result_products = mysqli_query($connect, $sql_products);
$total_products = mysqli_fetch_assoc($result_products)['total_products'];

$sql_orders = "SELECT COUNT(*) as total_orders FROM orders";
$result_orders = mysqli_query($connect, $sql_orders);
$total_orders = mysqli_fetch_assoc($result_orders)['total_orders'];

// Kiểm tra role admin
$user_role = $currentUser['role'];
if ($user_role !== 'admin') {
    header("Location: ../wed.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fruit Shop</title>
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
.stat-card {
    background: linear-gradient(135deg, #fff 60%, #e8f5e9 100%);
    color: #388e3c;
    border-radius: 18px;
    padding: 24px 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px #388e3c22;
    transition: transform 0.2s;
}
.stat-card:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 8px 32px #388e3c33;
}
.stat-card .fs-1 {
    opacity: 0.7;
}
.card {
    border-radius: 16px;
    box-shadow: 0 2px 12px #388e3c11;
}
.card-header {
    background: #e8f5e9;
    border-radius: 16px 16px 0 0;
    font-weight: 600;
    color:#388e3c;
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
.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-info:hover {
    color: #fff !important;
    background-color: #388e3c !important;
    border-color: #388e3c !important;
}
.badge {
    font-size: 0.95em;
    border-radius: 8px;
    padding: 0.5em 0.8em;
    background-color: #c8e6c9;
    color: #388e3c;
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
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people"></i> Users Management
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="bi bi-box-seam"></i> Products Management
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="bi bi-cart"></i> Orders Management
                    </a>
                    <a class="nav-link" href="categories.php">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                    <a class="nav-link" href="settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-success">Dashboard</h2>
                </div>
                <!-- Statistics Cards -->
                <div class="d-flex justify-content-end align-items-start mb-4" style="gap: 24px;">
                    <div style="min-width: 260px;">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-bold mb-1"><?php echo $total_users; ?></h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-people fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="min-width: 260px;">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-bold mb-1"><?php echo $total_products; ?></h3>
                                    <p class="mb-0">Total Products</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-box-seam fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="min-width: 260px;">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="fw-bold mb-1"><?php echo $total_orders; ?></h3>
                                    <p class="mb-0">Total Orders</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="bi bi-cart fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Users</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $sql_recent_users = "SELECT * FROM users ORDER BY created_at DESC LIMIT 5";
                                $result_recent_users = mysqli_query($connect, $sql_recent_users);
                                ?>
                                <div class="list-group list-group-flush">
                                    <?php while ($user = mysqli_fetch_assoc($result_recent_users)): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                        </div>
                                        <small class="text-muted"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></small>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="users.php" class="btn btn-outline-primary">
                                        <i class="bi bi-person-plus"></i> Add New User
                                    </a>
                                    <a href="products.php" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Add New Product
                                    </a>
                                    <a href="orders.php" class="btn btn-outline-warning">
                                        <i class="bi bi-eye"></i> View Orders
                                    </a>
                                    <a href="settings.php" class="btn btn-outline-info">
                                        <i class="bi bi-gear"></i> System Settings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 