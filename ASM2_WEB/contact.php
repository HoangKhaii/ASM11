<?php
session_start();
$errors = [];
$success = false;

// Xử lý form liên hệ
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"] ?? '');
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Validation
    if (empty($name)) {
        $errors[] = "Tên không được để trống";
    }
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không đúng định dạng";
    }
    if (empty($subject)) {
        $errors[] = "Tiêu đề không được để trống";
    }
    if (empty($message)) {
        $errors[] = "Nội dung không được để trống";
    }

    // Nếu không có lỗi, gửi email
    if (empty($errors)) {
        $to = "hoangkhai0512205@gmail.com"; // Địa chỉ nhận liên hệ
        $email_subject = "Liên hệ mới từ Fruit Shop: " . $subject;
        $email_body = "Thông tin liên hệ:\n\n";
        $email_body .= "Tên: " . $name . "\n";
        $email_body .= "Email: " . $email . "\n";
        $email_body .= "Điện thoại: " . ($phone ?: "Không có") . "\n";
        $email_body .= "Tiêu đề: " . $subject . "\n\n";
        $email_body .= "Nội dung:\n" . $message . "\n\n";
        $email_body .= "Thời gian: " . date('Y-m-d H:i:s') . "\n";
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        if (mail($to, $email_subject, $email_body, $headers)) {
            $success = true;
        } else {
            $errors[] = "Gửi email thất bại. Vui lòng thử lại sau.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Fruit Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="wed.css">
</head>
<body class="bg-light">
    <div class="container-lg my-4">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4 text-success">
                    <i class="bi bi-envelope-heart"></i> Liên hệ với chúng tôi
                </h2>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="bg-white rounded shadow-sm p-4">
                    <h4 class="mb-4 text-success">
                        <i class="bi bi-chat-dots"></i> Gửi tin nhắn cho chúng tôi
                    </h4>
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            <strong>Cảm ơn bạn!</strong> Tin nhắn của bạn đã được gửi thành công.
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Lỗi:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="contact.php">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label text-success fw-bold">
                                    <i class="bi bi-person"></i> Họ và tên *
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label text-success fw-bold">
                                    <i class="bi bi-envelope"></i> Email *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label text-success fw-bold">
                                    <i class="bi bi-telephone"></i> Số điện thoại
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="subject" class="form-label text-success fw-bold">
                                    <i class="bi bi-tag"></i> Tiêu đề *
                                </label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label text-success fw-bold">
                                <i class="bi bi-chat-text"></i> Nội dung tin nhắn *
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="6" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success px-4 py-2">
                                <i class="bi bi-send"></i> Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bg-success text-white rounded p-4 h-100">
                    <h4 class="mb-4">
                        <i class="bi bi-info-circle"></i> Thông tin liên hệ
                    </h4>
                    <div class="mb-4">
                        <h6><i class="bi bi-geo-alt"></i> Địa chỉ</h6>
                        <p class="mb-0">123 Fruit Street, Hoan Kiem District, Hanoi City</p>
                    </div>
                    <div class="mb-4">
                        <h6><i class="bi bi-telephone"></i> Điện thoại</h6>
                        <p class="mb-0">0878 92 2005</p>
                    </div>
                    <div class="mb-4">
                        <h6><i class="bi bi-envelope"></i> Email</h6>
                        <p class="mb-0">hoangkhai0512205@gmail.com</p>
                    </div>
                    <div class="mb-4">
                        <h6><i class="bi bi-clock"></i> Giờ làm việc</h6>
                        <p class="mb-1">Thứ 2 - Thứ 6: 8:00 - 18:00</p>
                        <p class="mb-0">Thứ 7 - Chủ nhật: 9:00 - 16:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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