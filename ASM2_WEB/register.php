<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fruit_shop";
$connect = mysqli_connect($servername, $username, $password, $dbname);

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $phone = trim($_POST["phone"] ?? '');
    $address = trim($_POST["address"] ?? '');

    // Validation rules
    $name_pattern = '/^[a-zA-ZÀ-ỹ\s]{2,50}$/';
    $email_pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    $password_pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    $phone_pattern = '/^[0-9]{10,11}$/';

    // Validate First Name
    if (empty($firstname)) {
        $errors[] = "Họ không được để trống";
    } elseif (!preg_match($name_pattern, $firstname)) {
        $errors[] = "Họ phải có 2-50 ký tự, chỉ chứa chữ cái và dấu cách";
    }

    // Validate Last Name
    if (empty($lastname)) {
        $errors[] = "Tên không được để trống";
    } elseif (!preg_match($name_pattern, $lastname)) {
        $errors[] = "Tên phải có 2-50 ký tự, chỉ chứa chữ cái và dấu cách";
    }

    // Validate Email
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!preg_match($email_pattern, $email)) {
        $errors[] = "Email không đúng định dạng";
    } else {
        // Check if email already exists
        $check_sql = "SELECT email FROM users WHERE email = '" . mysqli_real_escape_string($connect, $email) . "'";
        $check_result = mysqli_query($connect, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email đã được sử dụng, vui lòng chọn email khác";
        }
    }

    // Validate Password
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống";
    } elseif (!preg_match($password_pattern, $password)) {
        $errors[] = "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt (@$!%*?&)";
    }

    // Validate Confirm Password
    if (empty($confirm_password)) {
        $errors[] = "Xác nhận mật khẩu không được để trống";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }

    // Validate Phone (optional but if provided, must be valid)
    if (!empty($phone) && !preg_match($phone_pattern, $phone)) {
        $errors[] = "Số điện thoại phải có 10-11 chữ số";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (firstname, lastname, email, password, phone, address) 
                VALUES ('" . mysqli_real_escape_string($connect, $firstname) . "', 
                        '" . mysqli_real_escape_string($connect, $lastname) . "', 
                        '" . mysqli_real_escape_string($connect, $email) . "', 
                        '$hashed_password', 
                        '" . mysqli_real_escape_string($connect, $phone) . "', 
                        '" . mysqli_real_escape_string($connect, $address) . "')";

        if (mysqli_query($connect, $sql)) {
            $success = true;
        } else {
            $errors[] = "Lỗi đăng ký: " . mysqli_error($connect);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Web Fruit Shop</title>
  <link rel="stylesheet" href="combined-styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
    <form class="form-register" action="register.php" method="POST">
      <p class="link-login">Have an account? <a href="login.php">Login</a></p>
      <h2>Sign Up</h2>
      
      <?php if ($success): ?>
        <div style="background: rgba(76, 175, 80, 0.9); color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
          Đăng ký thành công! Đang chuyển hướng...
        </div>
        <script>
          setTimeout(function() {
            window.location.href = 'login.php';
          }, 2000);
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

      <div class="row">
        <div class="input">
          <i class="fa fa-user"></i>
          <input type="text" placeholder="Firstname" name="firstname" value="<?php echo htmlspecialchars($_POST['firstname'] ?? ''); ?>" required>
        </div>
        <div class="input">
          <i class="fa fa-user"></i>
          <input type="text" placeholder="Lastname" name="lastname" value="<?php echo htmlspecialchars($_POST['lastname'] ?? ''); ?>" required>
        </div>
      </div>
      <div class="input">
        <i class="fa fa-envelope"></i>
        <input type="email" placeholder="Email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
      </div>
      <div class="input">
        <i class="fa fa-lock"></i>
        <input type="password" placeholder="Password (Min 8 ký tự)" name="password" required>
      </div>
      <div class="input">
        <i class="fa fa-lock"></i>
        <input type="password" placeholder="Confirm Password" name="confirm_password" required>
      </div>
      <div class="input">
        <i class="fa fa-phone"></i>
        <input type="tel" placeholder="Phone Number (Optional)" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
      </div>
      <div class="input">
        <i class="fa fa-map-marker"></i>
        <input type="text" placeholder="Address (Optional)" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
      </div>
      
      <button type="submit" class="button submit">Register</button>
      <div class="form-options">
        <label><input type="checkbox"> Remember Me</label>
        <a href="#">Terms & conditions</a>
      </div>
    </form>
  </section>

  <script>
    // Real-time password validation
    document.addEventListener('DOMContentLoaded', function() {
      const passwordInput = document.querySelector('input[name="password"]');
      const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
      
      function validatePassword(password) {
        const requirements = {
          length: password.length >= 8,
          lowercase: /[a-z]/.test(password),
          uppercase: /[A-Z]/.test(password),
          number: /\d/.test(password),
          special: /[@$!%*?&]/.test(password)
        };
        
        return requirements;
      }
      
      function updatePasswordStrength(password) {
        const requirements = validatePassword(password);
        let strength = 0;
        
        Object.values(requirements).forEach(met => {
          if (met) strength++;
        });
        
        return strength;
      }
      
      function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword && password !== confirmPassword) {
          confirmPasswordInput.style.borderColor = '#f44336';
          confirmPasswordInput.style.backgroundColor = 'rgba(244, 67, 54, 0.1)';
        } else {
          confirmPasswordInput.style.borderColor = '';
          confirmPasswordInput.style.backgroundColor = '';
        }
      }
      
      passwordInput.addEventListener('input', function() {
        const password = this.value;
        const requirements = validatePassword(password);
        const strength = updatePasswordStrength(password);
        
        // Update visual feedback
        if (password.length > 0) {
          if (strength >= 4) {
            this.style.borderColor = '#4caf50';
            this.style.backgroundColor = 'rgba(76, 175, 80, 0.1)';
          } else if (strength >= 2) {
            this.style.borderColor = '#ff9800';
            this.style.backgroundColor = 'rgba(255, 152, 0, 0.1)';
          } else {
            this.style.borderColor = '#f44336';
            this.style.backgroundColor = 'rgba(244, 67, 54, 0.1)';
          }
        } else {
          this.style.borderColor = '';
          this.style.backgroundColor = '';
        }
        
        checkPasswordMatch();
      });
      
      confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    });
  </script>
</body>

</html>