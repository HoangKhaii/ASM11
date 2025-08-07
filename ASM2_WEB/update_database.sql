-- Cập nhật cấu trúc bảng users để sử dụng ID tự tăng
USE fruit_shopp;

-- Kiểm tra xem cột id đã tồn tại chưa
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'fruit_shopp' 
     AND TABLE_NAME = 'users' 
     AND COLUMN_NAME = 'id') > 0,
    'SELECT "Column id already exists" as message',
    'ALTER TABLE users ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cập nhật cấu trúc bảng users nếu cần
-- Thêm cột id nếu chưa có
ALTER TABLE users ADD COLUMN IF NOT EXISTS id INT AUTO_INCREMENT PRIMARY KEY FIRST;

-- Đảm bảo cột user_id vẫn tồn tại (nếu cần)
ALTER TABLE users ADD COLUMN IF NOT EXISTS user_id VARCHAR(20) UNIQUE;

-- Cập nhật các bảng liên quan để sử dụng id thay vì user_id
-- Bảng cart
ALTER TABLE cart MODIFY COLUMN user_id INT;

-- Bảng orders  
ALTER TABLE orders MODIFY COLUMN user_id INT;

-- Thêm foreign key constraints nếu cần
-- ALTER TABLE cart ADD CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id);
-- ALTER TABLE orders ADD CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id);

-- Hiển thị cấu trúc bảng sau khi cập nhật
DESCRIBE users;
DESCRIBE cart;
DESCRIBE orders; 