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

// Lấy category từ URL
$category = $_GET['category'] ?? 'all';
$search = trim($_GET['q'] ?? '');

// Xây dựng query dựa trên category
$where_conditions = ["status = 'active'"];

if ($category !== 'all') {
    $category_map = [
        'seedling' => 'Seedling',
        'seeds' => 'Seeds', 
        'clean_fruit' => 'Clean fruit',
        'clean_vegetables' => 'Clean vegetables',
        'advise' => 'Advise'
    ];
    
    if (isset($category_map[$category])) {
        $where_conditions[] = "category = '" . mysqli_real_escape_string($connect, $category_map[$category]) . "'";
    }
}

if ($search !== '') {
    $where_conditions[] = "product_name LIKE '%" . mysqli_real_escape_string($connect, $search) . "%'";
}

$where_clause = implode(' AND ', $where_conditions);
$product_sql = "SELECT * FROM products WHERE $where_clause ORDER BY created_at DESC";
$product_result = mysqli_query($connect, $product_sql);

// Lấy tên category hiển thị
$category_names = [
    'all' => 'Tất cả sản phẩm',
    'seedling' => 'Seedling',
    'seeds' => 'Seeds',
    'clean_fruit' => 'Clean fruit', 
    'clean_vegetables' => 'Clean vegetables',
    'advise' => 'Advise'
];

$current_category_name = $category_names[$category] ?? 'Tất cả sản phẩm';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="wed.css">
</head>

<body class="bg-light">
    <!-- Topbar -->
    <div class="topbar py-1 px-2 d-flex justify-content-between align-items-center bg-white border-bottom small">
        <div>
            <a href="wed.php" class="text-decoration-none text-success">Home</a> | 
            <a href="#" class="text-decoration-none text-success">Introduce</a> | 
            <a href="contact.php" class="text-decoration-none text-success">Contact</a>
        </div>
        <div>
            <?php if ($isLoggedIn): ?>
                <span class="text-success fw-bold me-2">
                    <i class="bi bi-person-circle"></i>
                    <?php echo htmlspecialchars($currentUser['firstname'] . ' ' . $currentUser['lastname']); ?>
                </span>
                <a href="products.php?logout=1" class="text-danger fw-bold me-2">
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

    <!-- Logo + Search -->
    <div class="container-lg py-2 d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <img src="https://th.bing.com/th/id/OIP.8yw8WMFVVGvFWhz1j5XPMQHaHa?rs=1&pid=ImgDetMain" alt="Logo"
                class="logo me-3" style="height:54px; border-radius: 50%; border: 2px solid #a5d6a7;">
            <h1 class="fs-4 fw-bold text-success">WEB FRUIT SHOP</h1>
        </div>
        <form class="d-flex search-bar" role="search" action="products.php" method="get" style="max-width:400px; flex:1;">
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
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
                <a class="nav-link text-white fw-bold text-uppercase" href="wed.php"><i class="bi bi-house-door"></i>Home</a>
                <a class="nav-link text-white fw-bold text-uppercase active" href="products.php"><i class="bi bi-box-seam"></i>Product</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-info-circle"></i>Introduce</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-chat-dots"></i>Advise</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-heart-pulse"></i>Health category</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="#"><i class="bi bi-newspaper"></i>News</a>
                <a class="nav-link text-white fw-bold text-uppercase" href="contact.php"><i class="bi bi-envelope"></i>Contacts</a>
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
                        <li class="list-group-item <?php echo $category === 'all' ? 'active' : ''; ?>">
                            <a href="products.php" class="text-decoration-none d-flex align-items-center">
                                <i class="bi bi-grid me-2"></i>Tất cả sản phẩm
                            </a>
                        </li>
                        <li class="list-group-item <?php echo $category === 'seedling' ? 'active' : ''; ?>">
                            <a href="products.php?category=seedling" class="text-decoration-none d-flex align-items-center">
                                <i class="bi bi-tree me-2"></i>Seedling
                            </a>
                        </li>
                        <li class="list-group-item <?php echo $category === 'seeds' ? 'active' : ''; ?>">
                            <a href="products.php?category=seeds" class="text-decoration-none d-flex align-items-center">
                                <i class="bi bi-droplet-half me-2"></i>Seeds
                            </a>
                        </li>
                        <li class="list-group-item <?php echo $category === 'clean_fruit' ? 'active' : ''; ?>">
                            <a href="products.php?category=clean_fruit" class="text-decoration-none d-flex align-items-center">
                                <i class="bi bi-emoji-smile me-2"></i>Clean fruit
                            </a>
                        </li>
                        <li class="list-group-item <?php echo $category === 'clean_vegetables' ? 'active' : ''; ?>">
                            <a href="products.php?category=clean_vegetables" class="text-decoration-none d-flex align-items-center">
                                <i class="bi bi-flower1 me-2"></i>Clean vegetables
                            </a>
                        </li>
                        <li class="list-group-item <?php echo $category === 'advise' ? 'active' : ''; ?>">
                            <a href="products.php?category=advise" class="text-decoration-none d-flex align-items-center">
                                <i class="bi bi-question-circle me-2"></i>Advise
                            </a>
                        </li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-success mb-0">
                        <i class="bi bi-box-seam"></i> <?php echo htmlspecialchars($current_category_name); ?>
                    </h4>
                    <div class="text-muted">
                        <?php echo mysqli_num_rows($product_result); ?> sản phẩm
                    </div>
                </div>

                <?php if ($search): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-search"></i> Kết quả tìm kiếm cho: "<strong><?php echo htmlspecialchars($search); ?></strong>"
                        <a href="products.php?category=<?php echo htmlspecialchars($category); ?>" class="btn btn-sm btn-outline-secondary ms-2">Xóa tìm kiếm</a>
                    </div>
                <?php endif; ?>

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
                        <div class="col-12 text-center text-muted py-5">
                            <i class="bi bi-box-seam fs-1"></i>
                            <h5 class="mt-3">Không tìm thấy sản phẩm</h5>
                            <p>Không có sản phẩm nào trong danh mục này.</p>
                            <a href="products.php" class="btn btn-success">Xem tất cả sản phẩm</a>
                        </div>
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
                    <h5 class="fw-bold mb-2 text-success"><i class="bi bi-shop-window me-2"></i>Fruit Shop by Hoang Khai</h5>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>