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

$product_id = $_GET['id'] ?? '';
if (!$product_id) {
    echo '<div class="container mt-5 text-danger">Product not found.</div>';
    exit();
}
// Lấy thông tin sản phẩm
$sql = "SELECT * FROM products WHERE product_id = '".mysqli_real_escape_string($connect, $product_id)."'";
$result = mysqli_query($connect, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    echo '<div class="container mt-5 text-danger">Product not found.</div>';
    exit();
}
$product = mysqli_fetch_assoc($result);

// Lấy danh mục
$cat_sql = "SELECT * FROM categories WHERE status = 'active'";
$cat_result = mysqli_query($connect, $cat_sql);

$success = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST["product_name"];
    $category_id = $_POST["category_id"];
    $product_price = $_POST["product_price"];
    $quantity = $_POST["quantity"];
    $product_description = $_POST["product_description"];
    $status = $_POST["status"];
    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;
    $is_on_sale = isset($_POST["is_on_sale"]) ? 1 : 0;
    $sale_price = $_POST["sale_price"] ?: NULL;
    // Xử lý ảnh
    $img_path = $product['product_img'];
    if (isset($_FILES['product_img']) && $_FILES['product_img']['size'] > 0) {
        if (!is_dir('../uploads')) { mkdir('../uploads', 0777, true); }
        $img_name = $_FILES['product_img']['name'];
        $img_tmp = $_FILES['product_img']['tmp_name'];
        $img_path = 'uploads/' . basename($img_name); // Lưu vào DB chỉ là uploads/ten_anh.jpg
        move_uploaded_file($img_tmp, '../' . $img_path); // Di chuyển file vào đúng thư mục uploads
    }
    $sql_update = "UPDATE products SET product_name='".mysqli_real_escape_string($connect, $product_name)."', category_id='$category_id', product_price='$product_price', quantity='$quantity', product_description='".mysqli_real_escape_string($connect, $product_description)."', product_img='".mysqli_real_escape_string($connect, $img_path)."', status='$status', is_featured=$is_featured, is_on_sale=$is_on_sale, sale_price=".($sale_price ? "'$sale_price'" : "NULL")." WHERE product_id = '".mysqli_real_escape_string($connect, $product_id)."'";
    if (mysqli_query($connect, $sql_update)) {
        $success = true;
        // Reload lại dữ liệu mới
        $result = mysqli_query($connect, $sql);
        $product = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../combined-styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #e8f5e9; font-family: 'Poppins', Arial, sans-serif; }
    </style>
</head>
<body>
    <div class="container py-4">
        <a href="../wed.php" class="btn btn-outline-success mb-3"><i class="bi bi-arrow-left"></i> Về trang shop</a>
        <div class="card shadow-sm mx-auto" style="max-width:700px;">
            <div class="card-body">
                <h2 class="fw-bold mb-4">Edit Product</h2>
                <?php if ($success): ?>
                    <div class="alert alert-success">Product updated successfully!</div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = mysqli_fetch_assoc($cat_result)): ?>
                                <option value="<?php echo $cat['category_id']; ?>" <?php if ($cat['category_id'] == $product['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['category_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="product_price" class="form-label">Price (VND)</label>
                        <input type="number" class="form-control" id="product_price" name="product_price" value="<?php echo htmlspecialchars($product['product_price']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="quantity" class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required>
                    </div>
                    <div class="col-12">
                        <label for="product_description" class="form-label">Description</label>
                        <textarea class="form-control" id="product_description" name="product_description" rows="3"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="product_img" class="form-label">Image</label>
                        <input type="file" class="form-control" id="product_img" name="product_img" accept="image/*">
                        <?php if ($product['product_img']): ?>
                            <img src="../<?php echo htmlspecialchars($product['product_img']); ?>" alt="Current Image" style="width:80px;height:80px;object-fit:cover;border-radius:8px;margin-top:8px;" onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                        <?php endif; ?>
                        <div class="form-text">Leave blank to keep current image.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" <?php if ($product['status'] == 'active') echo 'selected'; ?>>Active</option>
                            <option value="inactive" <?php if ($product['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?php if ($product['is_featured']) echo 'checked'; ?>>
                            <label class="form-check-label" for="is_featured">Featured Product</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_on_sale" name="is_on_sale" <?php if ($product['is_on_sale']) echo 'checked'; ?>>
                            <label class="form-check-label" for="is_on_sale">On Sale</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="sale_price" class="form-label">Sale Price (VND)</label>
                        <input type="number" class="form-control" id="sale_price" name="sale_price" value="<?php echo htmlspecialchars($product['sale_price']); ?>">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-lg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 