drop database db_winmart;
CREATE DATABASE IF NOT EXISTS db_winmart;
USE db_winmart;

-- Tạo bảng độc lập trước (không có khóa ngoại)
CREATE TABLE `category`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATE NOT NULL
);

CREATE TABLE `role`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATE NOT NULL
);

-- Tạo bảng product (phụ thuộc category)
CREATE TABLE `product`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NULL,
    `quantity` BIGINT NOT NULL,
    `price` FLOAT(53) NOT NULL,
    `description` TEXT NOT NULL,
    `category_id` BIGINT NOT NULL,
    `expiry` DATE NOT NULL,
    `year` DATE NOT NULL,
    `created_at` DATE NOT NULL,
    `discount` VARCHAR(45) NOT NULL
);

-- Tạo bảng user (phụ thuộc role)
CREATE TABLE `user`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `fullname` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `role_id` BIGINT NOT NULL,
    `created_at` DATE NOT NULL
);

-- Tạo các bảng còn lại
CREATE TABLE `orders`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(255) NOT NULL,
    `user_id` BIGINT NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `note` TEXT NOT NULL,
    `status` VARCHAR(255) NOT NULL,
    `total_price` FLOAT(53) NOT NULL,
    `created_at` BIGINT NOT NULL
);

CREATE TABLE `cart`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `product_id` BIGINT NOT NULL,
    `quantity` BIGINT NOT NULL,
    `price` FLOAT(53) NOT NULL,
    `total_price` FLOAT(53) NOT NULL,
    `created_at` DATE NOT NULL
);

CREATE TABLE `order_details`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `orders_id` BIGINT NOT NULL,
    `product_id` BIGINT NOT NULL,
    `quantity` BIGINT NOT NULL,
    `price` FLOAT(53) NOT NULL,
    `created_at` VARCHAR(255) NOT NULL
);

CREATE TABLE `feedback`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT NOT NULL,
    `content` TEXT NOT NULL,
    `star` BIGINT NOT NULL,
    `product_id` BIGINT NOT NULL,
    `created_at` DATE NOT NULL
);

CREATE TABLE `image`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `url` VARCHAR(255) NOT NULL,
    `user_id` BIGINT NOT NULL,
    `product_id` BIGINT NOT NULL,
    `created_at` DATE NOT NULL
);

CREATE TABLE `payment`(
    `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `orders_id` BIGINT NOT NULL,
    `amount` FLOAT(53) NOT NULL,
    `payment_date` DATE NOT NULL,
    `payment_method` VARCHAR(255) NOT NULL
);

-- Thêm các ràng buộc khóa ngoại
ALTER TABLE `product` 
    ADD CONSTRAINT `product_category_id_foreign` 
    FOREIGN KEY(`category_id`) REFERENCES `category`(`id`);

ALTER TABLE `user` 
    ADD CONSTRAINT `user_role_id_foreign` 
    FOREIGN KEY(`role_id`) REFERENCES `role`(`id`);

ALTER TABLE `orders` 
    ADD CONSTRAINT `orders_user_id_foreign` 
    FOREIGN KEY(`user_id`) REFERENCES `user`(`id`);

ALTER TABLE `cart` 
    ADD CONSTRAINT `cart_user_id_foreign` 
    FOREIGN KEY(`user_id`) REFERENCES `user`(`id`),
    ADD CONSTRAINT `cart_product_id_foreign` 
    FOREIGN KEY(`product_id`) REFERENCES `product`(`id`);

ALTER TABLE `order_details` 
    ADD CONSTRAINT `order_details_orders_id_foreign` 
    FOREIGN KEY(`orders_id`) REFERENCES `orders`(`id`),
    ADD CONSTRAINT `order_details_product_id_foreign` 
    FOREIGN KEY(`product_id`) REFERENCES `product`(`id`);

ALTER TABLE `feedback` 
    ADD CONSTRAINT `feedback_user_id_foreign` 
    FOREIGN KEY(`user_id`) REFERENCES `user`(`id`),
    ADD CONSTRAINT `feedback_product_id_foreign` 
    FOREIGN KEY(`product_id`) REFERENCES `product`(`id`);

ALTER TABLE `image` 
    ADD CONSTRAINT `image_user_id_foreign` 
    FOREIGN KEY(`user_id`) REFERENCES `user`(`id`),
    ADD CONSTRAINT `image_product_id_foreign` 
    FOREIGN KEY(`product_id`) REFERENCES `product`(`id`);

ALTER TABLE `payment` 
    ADD CONSTRAINT `payment_orders_id_foreign` 
    FOREIGN KEY(`orders_id`) REFERENCES `orders`(`id`);




USE db_winmart;

-- 1. Thêm role (bỏ id vì auto increment)
INSERT INTO `role` (`name`, `created_at`) VALUES
('Admin', '2024-11-05'),
('User', '2024-11-05'),
('Editor', '2024-11-05');

-- 2. Thêm category (bỏ id vì auto increment)
INSERT INTO `category` (`name`, `created_at`) VALUES
('Thực phẩm tươi sống', '2024-11-08'),
('Đồ uống', '2024-11-08'),
('Bánh kẹo', '2024-11-08'),
('Gia vị & Đồ khô', '2024-11-08'),
('Sữa & Sản phẩm từ sữa', '2024-11-08');

-- 3. Thêm user (bỏ id vì auto increment) - Thêm user thứ 4
-- $2y$10$eaImdxl6Tlp/Lk4mJG9QpeIG6c/a4fc.VR1cFzVpofMGqCJptAgUS = admin (password)
INSERT INTO `user` (`username`, `fullname`, `password`, `email`, `phone`, `address`, `role_id`, `created_at`) VALUES
('admin', 'Nguyen Van A', '$2y$10$eaImdxl6Tlp/Lk4mJG9QpeIG6c/a4fc.VR1cFzVpofMGqCJptAgUS', 'user1@example.com', '0123456789', '123 ABC Street', 1, '2024-11-07'),
('user2', 'Tran Thi B', 'password2', 'user2@example.com', '0987654321', '456 DEF Street', 2, '2024-11-07'),
('user3', 'Le Van C', 'password3', 'user3@example.com', '0912345678', '789 GHI Street', 3, '2024-11-07'),
('user4', 'Pham Van D', 'password4', 'user4@example.com', '0977888999', '789 GHI Street', 2, '2024-11-07');

-- 4. Thêm products 
INSERT INTO `product` (`code`, `name`, `quantity`, `price`, `description`, `category_id`, `expiry`, `year`, `created_at`, `discount`) VALUES
('P001', 'Thịt heo ba rọi', 50, 189000, 'Thịt heo ba rọi tươi, đảm bảo vệ sinh an toàn thực phẩm', 1, '2024-11-15', '2024-11-08', '2024-11-08', '0'),
('P002', 'Coca Cola 330ml', 200, 12000, 'Nước giải khát Coca Cola lon 330ml', 2, '2025-06-30', '2024-11-08', '2024-11-08', '5'),
('P003', 'Bánh Oreo Original', 100, 55000, 'Bánh Oreo Original gói 300g', 3, '2025-01-01', '2024-11-08', '2024-11-08', '0'),
('P004', 'Mì Hảo Hảo', 500, 4000, 'Mì gói Hảo Hảo vị tôm chua cay 75g', 4, '2025-03-15', '2024-11-08', '2024-11-08', '0'),
('P005', 'Sữa tươi Vinamilk', 150, 30000, 'Sữa tươi Vinamilk 100% hộp 1 lít', 5, '2024-12-31', '2024-11-08', '2024-11-08', '10'),
('P006', 'Cá thu tươi', 30, 250000, 'Cá thu tươi nguyên con, 1kg', 1, '2024-11-12', '2024-11-08', '2024-11-08', '0'),
('P007', 'Sting đỏ', 300, 10000, 'Nước tăng lực Sting đỏ 330ml', 2, '2025-05-20', '2024-11-08', '2024-11-08', '0'),
('P008', 'Kẹo dẻo Haribo', 80, 45000, 'Kẹo dẻo Haribo gói 200g', 3, '2025-02-28', '2024-11-08', '2024-11-08', '15'),
('P009', 'Nước mắm Nam Ngư', 120, 35000, 'Nước mắm Nam Ngư 900ml', 4, '2025-12-31', '2024-11-08', '2024-11-08', '0'),
('P010', 'Phô mai Cheese Master', 60, 65000, 'Phô mai lát Cheese Master 200g', 5, '2024-12-15', '2024-11-08', '2024-11-08', '5');

-- 5. Thêm orders
INSERT INTO `orders` (`code`, `user_id`, `address`, `note`, `status`, `total_price`, `created_at`) VALUES
-- Tháng 11/2024 (Hiện tại)
('ORD_VS001', 2, '123 ABC Street', 'Giao giờ hành chính', 'pending', 189000, UNIX_TIMESTAMP('2024-11-09')),
('ORD_VS002', 3, '456 DEF Street', 'Giao buổi sáng', 'processing', 120000, UNIX_TIMESTAMP('2024-11-09')),
('ORD_VS003', 4, '789 GHI Street', 'Gọi trước khi giao', 'completed', 350000, UNIX_TIMESTAMP('2024-11-09')),
('ORD_VS004', 2, '123 ABC Street', '', 'cancelled', 89000, UNIX_TIMESTAMP('2024-11-08')),
('ORD_VS005', 3, '456 DEF Street', 'Giao giờ hành chính', 'completed', 450000, UNIX_TIMESTAMP('2024-11-08')),

-- Tháng 10/2024
('ORD_VS006', 2, '123 ABC Street', '', 'completed', 780000, UNIX_TIMESTAMP('2024-10-15')),
('ORD_VS007', 3, '456 DEF Street', '', 'completed', 560000, UNIX_TIMESTAMP('2024-10-20')),
('ORD_VS008', 4, '789 GHI Street', '', 'completed', 890000, UNIX_TIMESTAMP('2024-10-25')),

-- Tháng 9/2024
('ORD_VS009', 2, '123 ABC Street', '', 'completed', 670000, UNIX_TIMESTAMP('2024-09-10')),
('ORD_VS010', 3, '456 DEF Street', '', 'completed', 450000, UNIX_TIMESTAMP('2024-09-15')),
('ORD_VS011', 4, '789 GHI Street', '', 'completed', 890000, UNIX_TIMESTAMP('2024-09-20')),

-- Tháng 8/2024
('ORD_VS012', 2, '123 ABC Street', '', 'completed', 560000, UNIX_TIMESTAMP('2024-08-05')),
('ORD_VS013', 3, '456 DEF Street', '', 'completed', 780000, UNIX_TIMESTAMP('2024-08-15')),
('ORD_VS014', 4, '789 GHI Street', '', 'completed', 340000, UNIX_TIMESTAMP('2024-08-25')),

-- Tháng 7/2024
('ORD_VS015', 2, '123 ABC Street', '', 'completed', 890000, UNIX_TIMESTAMP('2024-07-10')),
('ORD_VS016', 3, '456 DEF Street', '', 'completed', 670000, UNIX_TIMESTAMP('2024-07-20')),

-- Tháng 6/2024
('ORD_VS017', 2, '123 ABC Street', '', 'completed', 450000, UNIX_TIMESTAMP('2024-06-15')),
('ORD_VS018', 3, '456 DEF Street', '', 'completed', 780000, UNIX_TIMESTAMP('2024-06-25')),

-- Các tháng trước đó
('ORD_VS019', 4, '789 GHI Street', '', 'completed', 560000, UNIX_TIMESTAMP('2024-05-15')),
('ORD_VS020', 2, '123 ABC Street', '', 'completed', 890000, UNIX_TIMESTAMP('2024-04-20')),
('ORD_VS021', 3, '456 DEF Street', '', 'completed', 670000, UNIX_TIMESTAMP('2024-03-10')),
('ORD_VS022', 4, '789 GHI Street', '', 'completed', 450000, UNIX_TIMESTAMP('2024-02-15')),
('ORD_VS023', 2, '123 ABC Street', '', 'completed', 780000, UNIX_TIMESTAMP('2024-01-20')),
('ORD_VS024', 3, '456 DEF Street', '', 'completed', 560000, UNIX_TIMESTAMP('2023-12-25'));

-- 6. Thêm chi tiết đơn hàng
INSERT INTO `order_details` (`orders_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
-- Đơn hàng ngày hiện tại
(1, 1, 1, 189000, '2024-11-09'),
(2, 2, 10, 12000, '2024-11-09'),
(3, 3, 5, 55000, '2024-11-09'),
(3, 4, 25, 4000, '2024-11-09'),

-- Đơn hàng ngày hôm qua
(4, 5, 1, 89000, '2024-11-08'),
(5, 6, 1, 250000, '2024-11-08'),
(5, 7, 20, 10000, '2024-11-08'),

-- Đơn hàng tháng 10
(6, 1, 2, 390000, '2024-10-15'),
(7, 2, 15, 37333, '2024-10-20'),
(8, 3, 8, 111250, '2024-10-25'),

-- Đơn hàng tháng 9
(9, 4, 30, 22333, '2024-09-10'),
(10, 5, 2, 225000, '2024-09-15'),
(11, 6, 2, 445000, '2024-09-20'),

-- Đơn hàng các tháng trước
(12, 7, 25, 22400, '2024-08-05'),
(13, 1, 2, 390000, '2024-08-15'),
(14, 2, 17, 20000, '2024-08-25'),
(15, 3, 10, 89000, '2024-07-10'),
(16, 4, 35, 19142, '2024-07-20'),
(17, 5, 3, 150000, '2024-06-15'),
(18, 6, 2, 390000, '2024-06-25'),
(19, 7, 28, 20000, '2024-05-15'),
(20, 1, 3, 296666, '2024-04-20'),
(21, 2, 20, 33500, '2024-03-10'),
(22, 3, 5, 90000, '2024-02-15'),
(23, 4, 40, 19500, '2024-01-20'),
(24, 5, 4, 140000, '2023-12-25');

-- 7. Thêm feedback
INSERT INTO `feedback` (`user_id`, `content`, `star`, `product_id`, `created_at`) VALUES
(1, 'Thịt tươi ngon, đóng gói sạch sẽ', 5, 1, '2024-11-08'),
(2, 'Thịt tươi nhưng hơi mỡ', 4, 1, '2024-11-07'),
(1, 'Nước ngon, giá tốt', 5, 2, '2024-11-08'),
(3, 'Đóng gói cẩn thận, giao hàng nhanh', 5, 2, '2024-11-06'),
(2, 'Bánh giòn, vị ngon như mọi khi', 5, 3, '2024-11-08'),
(1, 'Bánh hơi ngọt, nên giảm đường', 3, 3, '2024-11-05'),
(3, 'Mì dai ngon, vị tương đối ổn', 4, 4, '2024-11-07'),
(2, 'Giá rẻ, ăn được', 4, 4, '2024-11-06');

-- 8. Thêm payment cho các đơn hàng
INSERT INTO `payment` (`orders_id`, `amount`, `payment_date`, `payment_method`) VALUES
-- Thanh toán cho đơn hàng tháng 11/2024
(1, 189000, '2024-11-09', 'COD'),       -- Cho ORD_VS001
(2, 120000, '2024-11-09', 'Banking'),    -- Cho ORD_VS002
(3, 350000, '2024-11-09', 'COD'),       -- Cho ORD_VS003
(4, 89000, '2024-11-08', 'COD'),        -- Cho ORD_VS004
(5, 450000, '2024-11-08', 'Banking'),    -- Cho ORD_VS005

-- Thanh toán cho đơn hàng tháng 10/2024
(6, 780000, '2024-10-15', 'Banking'),    -- Cho ORD_VS006
(7, 560000, '2024-10-20', 'COD'),       -- Cho ORD_VS007
(8, 890000, '2024-10-25', 'Banking'),    -- Cho ORD_VS008

-- Thanh toán cho đơn hàng tháng 9/2024
(9, 670000, '2024-09-10', 'COD'),       -- Cho ORD_VS009
(10, 450000, '2024-09-15', 'Banking'),   -- Cho ORD_VS010
(11, 890000, '2024-09-20', 'COD'),      -- Cho ORD_VS011

-- Thanh toán cho các đơn hàng còn lại
(12, 560000, '2024-08-05', 'Banking'),   -- Cho ORD_VS012 
(13, 780000, '2024-08-15', 'COD'),      -- Cho ORD_VS013
(14, 340000, '2024-08-25', 'Banking'),   -- Cho ORD_VS014
(15, 890000, '2024-07-10', 'COD'),      -- Cho ORD_VS015
(16, 670000, '2024-07-20', 'Banking'),   -- Cho ORD_VS016
(17, 450000, '2024-06-15', 'COD'),      -- Cho ORD_VS017
(18, 780000, '2024-06-25', 'Banking'),   -- Cho ORD_VS018
(19, 560000, '2024-05-15', 'COD'),      -- Cho ORD_VS019
(20, 890000, '2024-04-20', 'Banking'),   -- Cho ORD_VS020
(21, 670000, '2024-03-10', 'COD'),      -- Cho ORD_VS021
(22, 450000, '2024-02-15', 'Banking'),   -- Cho ORD_VS022
(23, 780000, '2024-01-20', 'COD'),      -- Cho ORD_VS023
(24, 560000, '2023-12-25', 'Banking');   -- Cho ORD_VS024

-- 9. Thêm image
INSERT INTO `image` (`url`, `user_id`, `product_id`, `created_at`) VALUES
('products/p001.jpg', 1, 1, '2024-11-08'),
('products/p002.jpg', 1, 2, '2024-11-08'),
('products/p003.jpg', 1, 3, '2024-11-08'),
('products/p004.jpg', 1, 4, '2024-11-08'),
('products/p005.jpg', 1, 5, '2024-11-08'),
('products/p006.jpg', 1, 6, '2024-11-08'),
('products/p007.jpg', 1, 7, '2024-11-08'),
('products/p008.jpg', 1, 8, '2024-11-08'),
('products/p009.jpg', 1, 9, '2024-11-08'),
('products/p010.jpg', 1, 10, '2024-11-08');

-- 10. Thêm cart
INSERT INTO `cart` (`user_id`, `product_id`, `quantity`, `price`, `total_price`, `created_at`) VALUES
(1, 1, 2, 189000, 378000, '2024-11-08'),
(2, 3, 1, 55000, 55000, '2024-11-08'),
(3, 5, 3, 30000, 90000, '2024-11-08');





