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

// Kiểm tra role admin
if ($currentUser['role'] !== 'admin') {
    header("Location:wed.php");
    exit();
}

// Xử lý thêm sản phẩm
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST["product_name"];
    $category_id = $_POST["category_id"];
    $product_price = $_POST["product_price"];
    $quantity = $_POST["quantity"];
    $product_description = $_POST["product_description"];
    // Tạo thư mục uploads nếu chưa có
    if (!is_dir('../uploads')) { mkdir('../uploads', 0777, true); }
    // Xử lý upload ảnh
    $img_name = $_FILES['product_img']['name'];
    $img_tmp = $_FILES['product_img']['tmp_name'];
    $img_path = 'uploads/' . basename($img_name); // Lưu vào DB chỉ là uploads/ten_anh.jpg
    move_uploaded_file($img_tmp, '../' . $img_path); // Di chuyển file vào đúng thư mục uploads
    $status = $_POST["status"];
    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;
    $is_on_sale = isset($_POST["is_on_sale"]) ? 1 : 0;
    $sale_price = $_POST["sale_price"] ?: NULL;

    $sql = "INSERT INTO products (product_name, category_id, product_price, quantity, product_description, product_img, status, is_featured, is_on_sale, sale_price) 
            VALUES ('" . mysqli_real_escape_string($connect, $product_name) . "', '$category_id', '$product_price', '$quantity', '" . mysqli_real_escape_string($connect, $product_description) . "', '$img_path', '$status', $is_featured, $is_on_sale, " . ($sale_price ? "'$sale_price'" : "NULL") . ")";

    if (mysqli_query($connect, $sql)) {
        echo "<script>
            alert('Product added successfully!');
            window.location.href = 'products.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . mysqli_error($connect) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../combined-styles.css">
    <style>
        body { background: #e8f5e9; font-family: 'Poppins', Arial, sans-serif; }
.admin-sidebar { min-height: 100vh; background: linear-gradient(135deg,#388e3c 0%, #a5d6a7 100%); color: #fff; box-shadow: 2px 0 8px #0001; }
.admin-sidebar .nav-link { color: #fff; font-weight: 500; border-radius: 8px; margin-bottom: 6px; transition: background 0.2s, color 0.2s; }
.admin-sidebar .nav-link.active, .admin-sidebar .nav-link:hover { background: #fff; color: #388e3c; }
.admin-sidebar .logo-admin { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; margin-bottom: 10px; }
.admin-header { background: #fff; border-bottom: 1px solid #a5d6a7; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
.admin-header .admin-info { display: flex; align-items: center; gap: 12px; }
.admin-header .avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #388e3c; }
.card { border-radius: 16px; box-shadow: 0 2px 12px #388e3c15; }
.card-header { background: #e8f5e9; border-radius: 16px 16px 0 0; font-weight: 600; color: #388e3c; }
.btn, .btn:focus { border-radius: 8px; font-weight: 500; }
.btn-outline-primary:hover, .btn-outline-success:hover, .btn-outline-danger:hover { color: #fff !important; }
label.form-label { font-weight: 500; color: #388e3c; }
.form-control, .form-select { border-radius: 8px; }
.form-check-label { color: #388e3c; }
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
            <a href="wed.php?logout=1" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a>
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
                    <a class="nav-link active" href="products.php"><i class="bi bi-box-seam"></i> Products Management</a>
                    <a class="nav-link" href="orders.php"><i class="bi bi-cart"></i> Orders Management</a>
                    <a class="nav-link" href="categories.php"><i class="bi bi-tags"></i> Categories</a>
                    <a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a>
                </nav>
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 admin-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-success"><i class="bi bi-plus-circle"></i> Add New Product</h2>
                    <a href="products.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Products</a>
                </div>
                <!-- Add Product Form -->
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Product Information</h5></div>
                    <div class="card-body">
                        <form method="POST" action="add_product.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_name" class="form-label">Product Name</label>
                                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            <?php
                                            $sql_categories = "SELECT * FROM categories WHERE status = 'active'";
                                            $result_categories = mysqli_query($connect, $sql_categories);
                                            while ($category = mysqli_fetch_assoc($result_categories)):
                                            ?>
                                            <option value="<?php echo $category['category_id']; ?>">
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_price" class="form-label">Price (VND)</label>
                                        <input type="number" class="form-control" id="product_price" name="product_price" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="quantity" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_img" class="form-label">Image</label>
                                        <input type="file" class="form-control" id="product_img" name="product_img" accept="image/*" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                            <label class="form-check-label" for="is_featured">Featured Product</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_on_sale" name="is_on_sale">
                                            <label class="form-check-label" for="is_on_sale">On Sale</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sale_price" class="form-label">Sale Price (VND)</label>
                                        <input type="number" class="form-control" id="sale_price" name="sale_price">
                                    </div>
                                    <div class="mb-3">
                                        <label for="product_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="product_description" name="product_description" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="products.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Product</button>
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