<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fruit_shop";
$connect = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_id']);
$currentUser = null;
if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($connect, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $currentUser = mysqli_fetch_assoc($result);
    }
}

// Xử lý đăng xuất
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: wed.php");
    exit();
}

// Lấy danh sách sản phẩm
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $product_sql = "SELECT * FROM products WHERE status = 'active' AND product_name LIKE '%".mysqli_real_escape_string($connect, $search)."%' ORDER BY created_at DESC";
} else {
    $product_sql = "SELECT * FROM products WHERE status = 'active' ORDER BY created_at DESC LIMIT 9";
}
$product_result = mysqli_query($connect, $product_sql);

// Lấy sản phẩm khuyến mãi (on sale)
$on_sale_sql = "SELECT * FROM products WHERE status = 'active' AND is_on_sale = 1 ORDER BY created_at DESC LIMIT 6";
$on_sale_result = mysqli_query($connect, $on_sale_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fruit Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="wed.css">
</head>

<body class="bg-light wedding-page">
    <!-- Topbar -->
    <div class="topbar py-1 px-2 d-flex justify-content-between align-items-center bg-white border-bottom small">
        <div>
            Home | Introduce | Contact
        </div>
        <div>
            <div>
                <?php if ($isLoggedIn): ?>
                    <span class="text-success fw-bold me-2">
                        <i class="bi bi-person-circle"></i>
                        <?php echo htmlspecialchars($currentUser['firstname'] . ' ' . $currentUser['lastname']); ?>
                    </span>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <a href="admin/index.php" class="text-success fw-bold me-2">
                            <i class="bi bi-gear"></i> Admin
                        </a>
                    <?php endif; ?>
                    <a href="wed.php?logout=1" class="text-danger fw-bold me-2">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="text-success fw-bold me-2">
                        <i class="bi bi-person-circle"></i> Login
                    </a>
                    <a href="register.php" class="text-success fw-bold me-2">
                        <i class="bi bi-person-plus"></i> Register
                    </a>
                <?php endif; ?>
                <a href="cart.php" class="text-success fw-bold">
                    <i class="bi bi-cart"></i> Shopping cart
                </a>
            </div>
        </div>
    </div>

    <!-- Logo + Search -->
    <div class="container-lg py-2 d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <img src="https://th.bing.com/th/id/OIP.8yw8WMFVVGvFWhz1j5XPMQHaHa?rs=1&pid=ImgDetMain" alt="Logo"
                class="logo me-3" style="height:54px; border-radius: 50%; border: 2px solid #a5d6a7;">
            <h1 class="fs-4 fw-bold text-success">WEB FRUIT SHOP</h1>
        </div>
        <form class="d-flex search-bar" role="search" action="wed.php" method="get" style="max-width:400px; flex:1;"
            aria-label="Fruit Shop Search Form">
            <input class="form-control me-2" type="search" name="q" placeholder="Search fruits, vegetables..."
                aria-label="Search for products" value="<?php echo htmlspecialchars($search); ?>">
            <button class="btn btn-success" type="submit">
                <i class="bi bi-search"></i> Search
            </button>
        </form>

    </div>
    <!-- Main Menu -->
    <nav class="mainmenu navbar navbar-expand-lg" style="background:#388e3c;">
        <div class="container-lg">
            <div class="navbar-nav w-100 justify-content-between">
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-house-door"></i>Home</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-box-seam"></i>
                    Product</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-info-circle"></i>
                    Introduce</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-chat-dots"></i>Advise</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-heart-pulse">Health
                        category</i></a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-newspaper"></i>News</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-envelope"></i>
                    Contacts</a>
            </div>
        </div>
    </nav>
    <!-- Content -->
    <div class="container-lg my-3">
        <div class="row g-4">
            <!-- Sidebar -->
            <aside class="col-lg-3">
                <div class="bg-white rounded shadow-sm p-3 mb-4">
                    <h6 class="sidebar-title"><i class="bi bi-list-ul me-2"></i>PRODUCT LIST</h6>
                    <ul class="list-group mb-3">
                        <li class="list-group-item"><i class="bi bi-tree me-2"></i>Seedling</li>
                        <li class="list-group-item"><i class="bi bi-droplet-half me-2"></i>Seeds</li>
                        <li class="list-group-item"><i class="bi bi-emoji-smile me-2"></i>Clean fruit</li>
                        <li class="list-group-item"><i class="bi bi-flower1 me-2"></i>Clean vegetables</li>
                        <li class="list-group-item"><i class="bi bi-question-circle me-2"></i>Advise</li>
                    </ul>
                    <h6 class="sidebar-title"><i class="bi bi-lightbulb me-2"></i>ADVISE</h6>
                    <ul class="list-group mb-3">
                        <li class="list-group-item"><i class="bi bi-heart-pulse me-2"></i>Health category</li>
                        <li class="list-group-item"><i class="bi bi-check2-circle me-2"></i>Tree selection advice</li>
                        <li class="list-group-item"><i class="bi bi-plant me-2"></i>Tree planting advice</li>
                        <li class="list-group-item"><i class="bi bi-emoji-heart-eyes me-2"></i>Care advice</li>
                    </ul>
                    <h6 class="sidebar-title"><i class="bi bi-star-fill me-2"></i>FEATURED PRODUCTS</h6>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-cup-straw me-2"></i>Papaya</span>
                            <span class="text-danger fw-bold">200.000 đ</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-egg-fried me-2"></i>Kiwi</span>
                            <span class="text-danger fw-bold">130.000 đ</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-bag-heart me-2"></i>Potato</span>
                            <span class="text-danger fw-bold">43.000 đ</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-cup-hot me-2"></i>Orange</span>
                            <span class="text-danger fw-bold">85.000 đ</span>
                        </li>
                    </ul>
                </div>
            </aside>
            <!-- Main Content -->
            <main class="col-lg-9">
                <!-- Banner -->
                <div class="banner mb-4 rounded shadow-sm overflow-hidden position-relative">
                    <img src="https://img5.thuthuatphanmem.vn/uploads/2021/12/27/background-cac-loai-trai-cay_044210936.jpg"
                        class="w-100" style="max-height:260px;object-fit:cover;"
                        alt="Banner showing fresh fruits and vegetables">
                    <div class="banner-caption text-center">
                        <h2>CLEAN VEGETABLES, CLEAN FRUITS</h2>
                        <p>Providing clean vegetables and safe fruits</p>
                        <a href="#" class="btn btn-success px-4"><i class="bi bi-telephone"></i>Contact now!</a>
                    </div>
                </div>
                <!-- Section: Sản phẩm -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="section-title mb-0">PRODUCT</div>
                    <a href="#" class="btn btn-outline-success btn-sm">See more<i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-4">
<?php if ($product_result && mysqli_num_rows($product_result) > 0): ?>
    <?php while ($product = mysqli_fetch_assoc($product_result)): ?>
    <div class="col">
        <div class="card h-100 text-center item">
            <img src="<?php echo htmlspecialchars($product['product_img']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="height:180px;object-fit:cover;width:100%;max-width:220px;margin:auto;" onerror="this.src='https://via.placeholder.com/220x180?text=No+Image'">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                <p class="card-text fw-bold"><?php echo number_format($product['product_price']); ?> đ
                <?php if ($product['is_on_sale'] && $product['sale_price']): ?>
                    <span class="text-decoration-line-through text-secondary ms-2"><?php echo number_format($product['sale_price']); ?> đ</span>
                <?php endif; ?></p>
                <a href="product_detail.php?id=<?php echo urlencode($product['product_id']); ?>" class="btn btn-link">Detail</a>
                <form method="post" action="cart.php" style="display:inline-block">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="btn btn-success ms-2"><i class="bi bi-cart-plus"></i> Add to cart</button>
                </form>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-12 text-center text-muted">No products found.</div>
<?php endif; ?>
</div>
                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled"><a class="page-link" href="#">First</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        <li class="page-item"><a class="page-link" href="#">End</a></li>
                    </ul>
                </nav>
                <!-- Section: Khuyến mãi -->
                <div class="d-flex justify-content-between align-items-center mt-5 mb-2">
                    <div class="section-title mb-0">ON SALE</div>
                    <a href="#" class="btn btn-outline-success btn-sm">See more <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="row row-cols-1 row-cols-md-3 g-4">
<?php if ($on_sale_result && mysqli_num_rows($on_sale_result) > 0): ?>
    <?php while ($product = mysqli_fetch_assoc($on_sale_result)): ?>
    <div class="col">
        <div class="card h-100 text-center item position-relative">
            <span class="badge bg-success position-absolute top-0 end-0 m-2">Sale</span>
            <img src="<?php echo htmlspecialchars($product['product_img']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="height:180px;object-fit:cover;width:100%;max-width:220px;margin:auto;" onerror="this.src='https://via.placeholder.com/220x180?text=No+Image'">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                <p class="card-text fw-bold"><?php echo number_format($product['product_price']); ?> đ
                <?php if ($product['sale_price']): ?>
                    <span class="text-decoration-line-through text-secondary ms-2"><?php echo number_format($product['sale_price']); ?> đ</span>
                <?php endif; ?></p>
                <a href="product_detail.php?id=<?php echo urlencode($product['product_id']); ?>" class="btn btn-link">Detail</a>
                <form method="post" action="cart.php" style="display:inline-block">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                    <input type="hidden" name="action" value="add">
                    <button type="submit" class="btn btn-success ms-2"><i class="bi bi-cart-plus"></i> Add to cart</button>
                </form>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="col-12 text-center text-muted">No sale products found.</div>
<?php endif; ?>
</div>
            </main>
        </div>
    </div>
    <!-- Footer -->
    <footer class="bg-light border-top mt-4 pt-4 pb-2">
        <div class="container-lg">
            <div class="row gy-3">
                <div class="col-md-4 text-center text-md-start">
                    <h5 class="fw-bold mb-2 text-success"><i class="bi bi-shop-window me-2"></i>Fruit Shop by Hoang Khai
                    </h5>
                    <p class="mb-1">Specializing in providing clean vegetables and fruits, safe and quality.</p>
                    <p class="mb-0 small text-muted">© 2020 - 2026 Fruit Shop. All rights reserved.</p>
                </div>
                <div class="col-md-4 text-center">
                    <h6 class="fw-bold mb-2 text-success"><i class="bi bi-geo-alt me-2"></i>Contacts</h6>
                    <p class="mb-1"><i class="bi bi-telephone me-2"></i>0878 92 2005</p>
                    <p class="mb-1"><i class="bi bi-envelope me-2"></i>hoangkhai0512205@gmail.com</p>
                    <p class="mb-0"><i class="bi bi-geo me-2"></i>123 Fruit Street, Hoan Kiem District, Hanoi City</p>
                </div>
                <div class="col-md-4 text-center text-md-end">
                    <h6 class="fw-bold mb-2 text-success"><i class="bi bi-share me-2"></i>Connect with me</h6>
                    <a href="#" class="text-success fs-4 me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-success fs-4 me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-success fs-4 me-2"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="text-success fs-4"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
