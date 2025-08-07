<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fruit_shop";
$connect = mysqli_connect($servername, $username, $password, $dbname);

/*// Tạo tài khoản admin mặc định nếu chưa tồn tại (chạy 1 lần)
$default_admin_email = 'admin2@fruitshop.com'; // pass: admin123
$check_admin = mysqli_query($connect, "SELECT * FROM users WHERE email='$default_admin_email'");
if ($check_admin && mysqli_num_rows($check_admin) == 0) {
    $admin_id = 'ADMIN0003';
    $firstname = 'Admin';
    $lastname = 'Pro';
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $role = 'admin';
    $phone = '0999999999';
    $address = 'Hanoi, Vietnam';
    mysqli_query($connect, "INSERT INTO users (user_id, firstname, lastname, email, password, role, phone, address) VALUES ('$admin_id', '$firstname', '$lastname', '$default_admin_email', '$password_hash', '$role', '$phone', '$address')");
    // Chỉ thông báo khi tạo mới
    echo '<div style="position:fixed;top:0;left:0;z-index:9999;background:#d4edda;color:#a6785c;padding:10px;">Tài khoản admin mặc định đã được tạo: '.$default_admin_email.' / admin123</div>';
}*/

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validation
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không đúng định dạng";
    }

    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống";
    }

    // If no validation errors, proceed with login
    if (empty($errors)) {
        $sql = "SELECT user_id, password FROM users WHERE email='" . mysqli_real_escape_string($connect, $email) . "'";
        $result = mysqli_query($connect, $sql);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                // Lấy role
                $role_sql = "SELECT role FROM users WHERE user_id = '" . mysqli_real_escape_string($connect, $row['user_id']) . "'";
                $role_result = mysqli_query($connect, $role_sql);
                $role_row = mysqli_fetch_assoc($role_result);
                $role = $role_row['role'] ?? 'user';
                
                $success = true;
                if ($role === 'admin') {
                    $redirect_url = 'admin/index.php';
                    $message = 'Đăng nhập thành công với quyền admin!';
                } else {
                    $redirect_url = 'wed.php';
                    $message = 'Đăng nhập thành công!';
                }
            } else {
                $errors[] = "Sai mật khẩu!";
            }
        } else {
            $errors[] = "Không tìm thấy tài khoản với email này!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Web Fruit Shop</title>
  <link rel="stylesheet" href="combined-styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>
  <div class="background"></div>
  <header class="header">
    <div class="brand"> WEB FRUIT SHOP</div>
    <nav class="menu">
      <a href="#">Home</a>
      <a href="#">Blog</a>
      <a href="#">Services</a>
      <a href="#">About</a>
    </nav>
    <div class="button-group">
      <a href="login.php" class="button outline">Sign In</a>
      <a href="register.php" class="button solid">Sign Up</a>
    </div>
  </header>

  <section class="form-area">
    <form class="form-login" action="login.php" method="post">
      <p class="link-signup">Don't have an account? <a href="register.php">Sign Up</a></p>
      <h2>Sign In</h2>
      
      <?php if ($success): ?>
        <div style="background: rgba(76, 175, 80, 0.9); color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
          <?php echo htmlspecialchars($message); ?>
        </div>
        <script>
          setTimeout(function() {
            window.location.href = '<?php echo $redirect_url; ?>';
          }, 1500);
        </script>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div style="background: rgba(244, 67, 54, 0.9); color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
          <strong>Lỗi:</strong>
          <ul style="margin: 5px 0 0 20px; padding: 0;">
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="input">
        <i class="fa fa-envelope"></i>
        <input type="email" placeholder="Email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
      </div>
      <div class="input">
        <i class="fa fa-lock"></i>
        <input type="password" placeholder="Password" name="password" required>
      </div>
      <button type="submit" class="button submit">Sign In</button>
      <div class="form-options">
        <label><input type="checkbox"> Remember Me</label>
        <a href="#">Terms & conditions</a>
      </div>
    </form>
  </section>
</body>
</html>
