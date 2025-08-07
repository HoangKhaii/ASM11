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

// Xử lý thêm/xóa/cập nhật sản phẩm trong giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = $_POST['product_id'] ?? '';
    $quantity = max(1, intval($_POST['quantity'] ?? 1));
    if ($action === 'add' && $product_id) {
        // Kiểm tra đã có trong giỏ chưa
        $check = mysqli_query($connect, "SELECT * FROM cart WHERE user_id='$user_id' AND product_id='".mysqli_real_escape_string($connect, $product_id)."'");
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($connect, "UPDATE cart SET quantity = quantity + $quantity WHERE user_id='$user_id' AND product_id='".mysqli_real_escape_string($connect, $product_id)."'");
        } else {
            mysqli_query($connect, "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '".mysqli_real_escape_string($connect, $product_id)."', $quantity)");
        }
    }
    if ($action === 'update' && $product_id) {
        mysqli_query($connect, "UPDATE cart SET quantity = $quantity WHERE user_id='$user_id' AND product_id='".mysqli_real_escape_string($connect, $product_id)."'");
    }
    if ($action === 'delete' && $product_id) {
        mysqli_query($connect, "DELETE FROM cart WHERE user_id='$user_id' AND product_id='".mysqli_real_escape_string($connect, $product_id)."'");
    }
    header('Location: cart.php');
    exit();
}

// Lấy sản phẩm trong giỏ hàng
$sql = "SELECT c.*, p.product_name, p.product_img, p.product_price, p.sale_price, p.is_on_sale, p.quantity as stock FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.user_id = '$user_id'";
$result = mysqli_query($connect, $sql);
$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wed.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light wedding-page">
    <div class="container py-4">
        <a href="wed.php" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Back to Shop</a>
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="fw-bold mb-4">Your Shopping Cart</h2>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive mb-4">
                    <table class="table align-middle table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Image</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                                $price = ($row['is_on_sale'] && $row['sale_price']) ? $row['sale_price'] : $row['product_price'];
                                $subtotal = $price * $row['quantity'];
                                $total += $subtotal;
                            ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($row['product_img']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" style="width:60px;height:60px;object-fit:cover;border-radius:8px;" onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'"></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo number_format($price); ?> đ</td>
                                <td>
                                    <form method="post" action="cart.php" class="d-inline-flex align-items-center gap-2">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['product_id']); ?>">
                                        <input type="hidden" name="action" value="update">
                                        <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" max="<?php echo $row['stock']; ?>" class="form-control" style="width:70px;">
                                        <button type="submit" class="btn btn-outline-success btn-sm">Update</button>
                                    </form>
                                </td>
                                <td><?php echo number_format($subtotal); ?> đ</td>
                                <td>
                                    <form method="post" action="cart.php" onsubmit="return confirm('Remove this product?')">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['product_id']); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h4>Total: <span class="text-success"><?php echo number_format($total); ?> đ</span></h4>
                    <a href="checkout.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
                </div>
                <?php else: ?>
                    <div class="alert alert-info">Your cart is empty. <a href="wed.php">Go shopping!</a></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 