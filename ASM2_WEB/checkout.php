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
$user_sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$user_result = mysqli_query($connect, $user_sql);
$user = mysqli_fetch_assoc($user_result);

// Lấy sản phẩm trong giỏ hàng
$sql = "SELECT c.*, p.product_name, p.product_img, p.product_price, p.sale_price, p.is_on_sale FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = '$user_id'";
$result = mysqli_query($connect, $sql);
$total = 0;
$cart_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $price = ($row['is_on_sale'] && $row['sale_price']) ? $row['sale_price'] : $row['product_price'];
    $row['final_price'] = $price;
    $row['subtotal'] = $price * $row['quantity'];
    $total += $row['subtotal'];
    $cart_items[] = $row;
}

// Xử lý đặt hàng
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($cart_items) > 0) {
    $address = trim($_POST['shipping_address'] ?? $user['address']);
    $phone = trim($_POST['phone'] ?? $user['phone']);
    $notes = trim($_POST['notes'] ?? '');
    if ($address && $phone) {
        $order_sql = "INSERT INTO orders (user_id, total_amount, status, shipping_address, phone, notes) VALUES ('$user_id', $total, 'pending', '".mysqli_real_escape_string($connect, $address)."', '".mysqli_real_escape_string($connect, $phone)."', '".mysqli_real_escape_string($connect, $notes)."')";
        if (mysqli_query($connect, $order_sql)) {
            $order_id = mysqli_insert_id($connect);
            foreach ($cart_items as $item) {
                $pid = mysqli_real_escape_string($connect, $item['product_id']);
                $qty = intval($item['quantity']);
                $price = floatval($item['final_price']);
                mysqli_query($connect, "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES ($order_id, '$pid', $qty, $price)");
                // Trừ tồn kho
                mysqli_query($connect, "UPDATE products SET quantity = quantity - $qty WHERE product_id = '$pid'");
            }
            // Xóa giỏ hàng
            mysqli_query($connect, "DELETE FROM cart WHERE user_id = '$user_id'");
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wed.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light wedding-page">
    <div class="container py-4">
        <a href="cart.php" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Back to Cart</a>
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="fw-bold mb-4">Checkout</h2>
                <?php if ($success): ?>
                    <div class="alert alert-success">Order placed successfully! <a href="order_history.php">View your orders</a></div>
                <?php elseif (count($cart_items) === 0): ?>
                    <div class="alert alert-info">Your cart is empty. <a href="wed.php">Go shopping!</a></div>
                <?php else: ?>
                <form method="post" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="shipping_address" class="form-label">Shipping Address</label>
                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-lg">Place Order</button>
                    </div>
                </form>
                <h5 class="mb-3">Order Summary</h5>
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
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($item['product_img']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width:40px;height:40px;object-fit:cover;border-radius:8px;" onerror="this.src='https://via.placeholder.com/40x40?text=No+Image'">
                                    <?php echo htmlspecialchars($item['product_name']); ?>
                                </td>
                                <td><?php echo number_format($item['final_price']); ?> đ</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($item['subtotal']); ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-success"><?php echo number_format($total); ?> đ</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 