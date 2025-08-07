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
$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    echo '<div class="container mt-5 text-danger">Order not found.</div>';
    exit();
}
// Lấy thông tin đơn hàng
$sql = "SELECT o.*, u.firstname, u.lastname, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.user_id WHERE o.order_id = $order_id";
$result = mysqli_query($connect, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    echo '<div class="container mt-5 text-danger">Order not found.</div>';
    exit();
}
$order = mysqli_fetch_assoc($result);
// Lấy chi tiết đơn hàng
$details_sql = "SELECT od.*, p.product_name, p.product_img FROM order_details od JOIN products p ON od.product_id = p.product_id WHERE od.order_id = $order_id";
$details_result = mysqli_query($connect, $details_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order['order_id']; ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../combined-styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>body { background: #e8f5e9; font-family: 'Poppins', Arial, sans-serif; }</style>
</head>
<body>
    <div class="container py-4">
        <a href="orders.php" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Back to Orders</a>
        <div class="card shadow-sm mx-auto" style="max-width:800px;">
            <div class="card-body">
                <h2 class="fw-bold mb-3">Order #<?php echo $order['order_id']; ?></h2>
                <div class="mb-3">
                    <span class="badge bg-<?php
                        $status = $order['status'];
                        $badge = 'secondary';
                        if ($status == 'pending') $badge = 'warning';
                        if ($status == 'confirmed') $badge = 'info';
                        if ($status == 'shipping') $badge = 'primary';
                        if ($status == 'delivered') $badge = 'success';
                        if ($status == 'cancelled') $badge = 'danger';
                        echo $badge;
                    ?> text-uppercase"><?php echo ucfirst($order['status']); ?></span>
                    <span class="ms-3 text-muted">Date: <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="mb-2">
                    <strong>Customer:</strong> <?php echo htmlspecialchars(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? '')); ?>
                    <?php if ($order['email']): ?> (<?php echo htmlspecialchars($order['email']); ?>)<?php endif; ?><br>
                    <strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                    <strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?><br>
                    <?php if ($order['notes']): ?><strong>Notes:</strong> <?php echo htmlspecialchars($order['notes']); ?><br><?php endif; ?>
                </div>
                <h5 class="mb-3">Order Items</h5>
                <div class="table-responsive mb-2">
                    <table class="table align-middle table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $total = 0; while ($item = mysqli_fetch_assoc($details_result)): $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($item['product_img']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width:40px;height:40px;object-fit:cover;border-radius:8px;" onerror="this.src='https://via.placeholder.com/40x40?text=No+Image'">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                </td>
                                <td><?php echo number_format($item['price']); ?> đ</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($subtotal); ?> đ</td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-success"><?php echo number_format($total); ?> đ</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 