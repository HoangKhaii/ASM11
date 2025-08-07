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

// Lấy danh sách đơn hàng của user
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($connect, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wed.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light wedding-page">
    <div class="container py-4">
        <a href="wed.php" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Back to Shop</a>
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="fw-bold mb-4">Order History</h2>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table align-middle table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($order = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $order['order_id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
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
                                </td>
                                <td><?php echo number_format($order['total_amount']); ?> đ</td>
                                <td><a href="order_view.php?id=<?php echo $order['order_id']; ?>" class="btn btn-outline-success btn-sm">View</a></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info">You have no orders yet. <a href="wed.php">Go shopping!</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 