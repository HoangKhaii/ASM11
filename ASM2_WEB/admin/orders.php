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
// Xử lý thay đổi trạng thái đơn hàng
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $update_sql = "UPDATE orders SET status = '".mysqli_real_escape_string($connect, $new_status)."' WHERE order_id = '".mysqli_real_escape_string($connect, $order_id)."'";
    if (mysqli_query($connect, $update_sql)) {
        $success_message = "Order status updated successfully!";
    } else {
        $error_message = "Failed to update order status.";
    }
}

// Xử lý xóa đơn hàng
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $sql_delete = "DELETE FROM orders WHERE order_id = '".mysqli_real_escape_string($connect, $delete_id)."'";
    mysqli_query($connect, $sql_delete);
    header("Location: orders.php");
    exit();
}

// Lọc theo trạng thái
$status_filter = $_GET['status'] ?? '';
if ($status_filter) {
    $sql = "SELECT o.*, u.firstname, u.lastname FROM orders o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.status = '".mysqli_real_escape_string($connect, $status_filter)."' ORDER BY o.created_at DESC";
} else {
    $sql = "SELECT o.*, u.firstname, u.lastname FROM orders o LEFT JOIN users u ON o.user_id = u.user_id ORDER BY o.created_at DESC";
}
$result = mysqli_query($connect, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Admin</title>
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
.card { border-radius: 16px; box-shadow: 0 2px 12px #388e3c22; }
.card-header { background: #e8f5e9; border-radius: 16px 16px 0 0; font-weight: 600; color: #388e3c; }
.btn, .btn:focus { border-radius: 8px; font-weight: 500; }
.badge { font-size: 0.95em; border-radius: 8px; padding: 0.5em 0.8em; }
.table thead th { background: #e8f5e9; color: #388e3c; font-weight: 600; }
.table-striped>tbody>tr:nth-of-type(odd) { background-color: #e8f5e9; }
.table-striped>tbody>tr:hover { background: #c8e6c9; }
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
                    <a class="nav-link active" href="orders.php"><i class="bi bi-cart"></i> Orders Management</a>
                    <a class="nav-link" href="categories.php"><i class="bi bi-tags"></i> Categories</a>
                    <a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-success"><i class="bi bi-cart"></i> Orders Management</h2>
                    <div class="d-flex gap-2">
                        <select class="form-select" onchange="window.location.href='orders.php?status=' + this.value">
                            <option value="">All Orders</option>
                            <option value="pending" <?php if ($status_filter == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="confirmed" <?php if ($status_filter == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                            <option value="shipping" <?php if ($status_filter == 'shipping') echo 'selected'; ?>>Shipping</option>
                            <option value="delivered" <?php if ($status_filter == 'delivered') echo 'selected'; ?>>Delivered</option>
                            <option value="cancelled" <?php if ($status_filter == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Order Status Statistics -->
                <?php
                $stats_sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
                $stats_result = mysqli_query($connect, $stats_sql);
                $status_stats = [];
                while ($stat = mysqli_fetch_assoc($stats_result)) {
                    $status_stats[$stat['status']] = $stat['count'];
                }
                ?>
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="text-warning"><?php echo $status_stats['pending'] ?? 0; ?></h5>
                                <small class="text-muted">Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="text-info"><?php echo $status_stats['confirmed'] ?? 0; ?></h5>
                                <small class="text-muted">Confirmed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="text-primary"><?php echo $status_stats['shipping'] ?? 0; ?></h5>
                                <small class="text-muted">Shipping</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="text-success"><?php echo $status_stats['delivered'] ?? 0; ?></h5>
                                <small class="text-muted">Delivered</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="text-danger"><?php echo $status_stats['cancelled'] ?? 0; ?></h5>
                                <small class="text-muted">Cancelled</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="text-secondary"><?php echo array_sum($status_stats); ?></h5>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Orders Table -->
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">All Orders</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? '')); ?></td>
                                        <td><?php echo number_format($order['total_amount']); ?> đ</td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <select name="new_status" class="form-select form-select-sm" style="width: auto; min-width: 120px;" onchange="this.form.submit()">
                                                    <option value="pending" <?php if ($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                                    <option value="confirmed" <?php if ($order['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                                    <option value="shipping" <?php if ($order['status'] == 'shipping') echo 'selected'; ?>>Shipping</option>
                                                    <option value="delivered" <?php if ($order['status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                                                    <option value="cancelled" <?php if ($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                                </select>
                                                <input type="hidden" name="update_status" value="1">
                                            </form>
                                            <div class="mt-1">
                                                <?php
                                                $status = $order['status'];
                                                $badge = 'secondary';
                                                if ($status == 'pending') $badge = 'warning';
                                                if ($status == 'confirmed') $badge = 'info';
                                                if ($status == 'shipping') $badge = 'primary';
                                                if ($status == 'delivered') $badge = 'success';
                                                if ($status == 'cancelled') $badge = 'danger';
                                                ?>
                                                <span class="badge bg-<?php echo $badge; ?> text-uppercase"><?php echo ucfirst($status); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <a href="view_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                            <a href="orders.php?delete=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this order?')"><i class="bi bi-trash"></i></a>
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