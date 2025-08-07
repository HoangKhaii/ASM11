<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fruit_shop";
$connect = mysqli_connect($servername, $username, $password, $dbname);

$product_id = $_GET['id'] ?? '';
if (!$product_id) {
    echo '<div class="container mt-5 text-danger">Product not found.</div>';
    exit();
}
$sql = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.product_id = '".mysqli_real_escape_string($connect, $product_id)."' LIMIT 1";
$result = mysqli_query($connect, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    echo '<div class="container mt-5 text-danger">Product not found.</div>';
    exit();
}
$product = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wed.css">
</head>
<body>
    <div class="container py-4">
        <a href="wed.php" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Back to Shop</a>
        <div class="row g-4">
            <div class="col-md-5">
                <img src="<?php echo htmlspecialchars($product['product_img']); ?>" class="img-fluid rounded shadow-sm" alt="<?php echo htmlspecialchars($product['product_name']); ?>" onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
            </div>
            <div class="col-md-7">
                <h2 class="fw-bold mb-2"><?php echo htmlspecialchars($product['product_name']); ?></h2>
                <div class="mb-2">
                    <span class="badge bg-success">Category: <?php echo htmlspecialchars($product['category_name']); ?></span>
                    <?php if ($product['is_featured']): ?><span class="badge bg-warning text-dark ms-2">Featured</span><?php endif; ?>
                    <?php if ($product['is_on_sale']): ?><span class="badge bg-danger ms-2">On Sale</span><?php endif; ?>
                </div>
                <h4 class="text-success mb-3">
                    <?php echo number_format($product['product_price']); ?> đ
                    <?php if ($product['is_on_sale'] && $product['sale_price']): ?>
                        <span class="text-decoration-line-through text-secondary ms-2"><?php echo number_format($product['sale_price']); ?> đ</span>
                    <?php endif; ?>
                </h4>
                <p class="mb-3"><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                <form method="post" action="cart.php" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                    <input type="hidden" name="action" value="add">
                    <label for="quantity" class="form-label mb-0">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>" class="form-control" style="width:100px;">
                    <button type="submit" class="btn btn-success"><i class="bi bi-cart-plus"></i> Add to cart</button>
                </form>
                <div class="mt-3">
                    <span class="text-muted">Stock: <?php echo $product['quantity']; ?></span>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 