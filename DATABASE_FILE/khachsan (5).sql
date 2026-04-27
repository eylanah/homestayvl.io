-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 02:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `khachsan`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_cred`
--

CREATE TABLE `admin_cred` (
  `sr_no` int(11) NOT NULL,
  `admin_name` varchar(150) NOT NULL,
  `admin_pass` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_cred`
--

INSERT INTO `admin_cred` (`sr_no`, `admin_name`, `admin_pass`) VALUES
(2, 'admin', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `total_pay` int(11) NOT NULL,
  `room_no` varchar(100) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `address` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`sr_no`, `booking_id`, `room_name`, `price`, `total_pay`, `room_no`, `user_name`, `phonenum`, `address`) VALUES
(96, 97, 'Phòng Bình Dân', 800000, 4800000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(97, 98, 'Phòng Bình Dân', 800000, 4000000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(98, 99, 'Phòng Bình Dân', 800000, 4000000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(99, 100, 'Phòng Bình Dân', 800000, 4800000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(100, 101, 'Phòng Bình Dân', 800000, 5600000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(101, 102, 'Phòng Vip 3', 1500000, 1500000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(102, 103, 'Phòng Bình Dân', 800000, 4800000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(103, 104, 'Phòng Vip 3', 1500000, 3000000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(104, 105, 'Phòng Vip 3', 1500000, 4500000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(105, 106, 'Phòng Đơn', 500000, 2500000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(106, 107, 'Phòng Đơn', 500000, 2500000, NULL, 'Quang Huy', '0888131067', 'Bac Lieu - Tp Vinh - Nghe An'),
(108, 110, 'Phòng Bình Dân', 800000, 2400000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long'),
(109, 111, 'Phòng Bình Dân', 800000, 1600000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long'),
(110, 112, 'Phòng cổ điển đơn giản', 1000000, 2000000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long'),
(111, 113, 'Phòng Bình Dân', 800000, 1600000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long'),
(112, 114, 'Phòng Bình Dân', 800000, 1600000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long'),
(113, 116, 'Phòng sang trọng', 1500000, 3000000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long'),
(114, 117, 'Phòng sang trọng', 1500000, 4500000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long'),
(115, 118, 'Phòng Đơn', 500000, 6000000, NULL, 'Nguyễn Thành Đạt', '0123456789', 'Vinh Long');

-- --------------------------------------------------------

--
-- Table structure for table `booking_order`
--

CREATE TABLE `booking_order` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `arrival` int(11) NOT NULL DEFAULT 0,
  `refund` int(11) DEFAULT NULL,
  `booking_status` varchar(100) NOT NULL DEFAULT 'Đã Đặt',
  `order_id` varchar(150) NOT NULL,
  `trans_id` varchar(200) DEFAULT NULL,
  `trans_amt` int(11) NOT NULL,
  `trans_status` varchar(100) NOT NULL DEFAULT 'Đã Đặt',
  `payment_status` varchar(100) NOT NULL DEFAULT 'Chưa Thanh Toán',
  `trans_resp_msg` varchar(200) DEFAULT NULL,
  `rate_review` int(11) DEFAULT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_order`
--

INSERT INTO `booking_order` (`booking_id`, `user_id`, `room_id`, `check_in`, `check_out`, `arrival`, `refund`, `booking_status`, `order_id`, `trans_id`, `trans_amt`, `trans_status`, `payment_status`, `trans_resp_msg`, `rate_review`, `datentime`) VALUES
(96, 9, 3, '2023-05-13', '2023-05-18', 0, NULL, 'Đã Đặt', 'ORD_97949965', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-13 15:18:38'),
(97, 9, 3, '2023-05-13', '2023-05-19', 0, 0, 'Đã Huỷ', 'ORD_98661978', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-13 15:23:42'),
(98, 9, 3, '2023-05-13', '2023-05-18', 0, 0, 'Đã Huỷ', 'ORD_96810887', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-13 15:34:08'),
(99, 9, 3, '2023-05-13', '2023-05-18', 0, 0, 'Đã Huỷ', 'ORD_9394029', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-13 15:34:19'),
(100, 9, 3, '2023-05-13', '2023-05-19', 1, NULL, 'Đã Thanh Toán', 'ORD_9554729', NULL, 4800000, 'TXN_SUCCESS', 'Chưa Thanh Toán', NULL, NULL, '2023-05-13 15:39:59'),
(101, 9, 3, '2023-05-13', '2023-05-20', 0, 0, 'Đã Huỷ', 'ORD_91676809', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-13 15:40:38'),
(102, 9, 6, '2023-05-10', '2023-05-14', 0, NULL, 'Đã Xác Nhận Đặt Phòng', 'ORD_95687526', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-14 05:33:26'),
(103, 9, 3, '2023-05-14', '2023-05-20', 0, 0, 'Đã Huỷ', 'ORD_96612782', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-14 06:59:30'),
(104, 9, 6, '2023-05-14', '2023-05-16', 0, 0, 'Đã Huỷ', 'ORD_91367634', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-14 09:54:35'),
(105, 9, 6, '2023-05-14', '2023-05-17', 1, NULL, 'Đã Thanh Toán', 'ORD_91493479', NULL, 4500000, 'TXN_SUCCESS', 'Chưa Thanh Toán', NULL, NULL, '2023-05-14 10:43:24'),
(106, 9, 8, '2023-05-15', '2023-05-20', 0, NULL, 'Đã Đặt', 'ORD_91032898', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-15 19:12:10'),
(107, 9, 8, '2023-05-15', '2023-05-20', 0, NULL, 'Đã Đặt', 'ORD_92583238', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2023-05-15 19:12:24'),
(108, 11, 3, '2026-03-14', '2026-03-16', 1, NULL, 'Đã Thanh Toán', 'ORD_116017568', NULL, 1600000, 'TXN_SUCCESS', 'Chưa Thanh Toán', NULL, 1, '2026-03-14 22:11:39'),
(109, 13, 3, '2026-04-25', '2026-04-30', 0, NULL, 'pending', 'ORD_133463217', NULL, 0, 'Đã Đặt', 'Chưa Thanh Toán', NULL, NULL, '2026-04-25 22:19:42'),
(110, 13, 3, '2026-04-25', '2026-04-28', 0, NULL, 'Đã Thanh Toán 50%', 'ORD_132848892', NULL, 1200000, 'Đã nhận 50%', 'Chưa Thanh Toán', NULL, NULL, '2026-04-25 22:21:21'),
(111, 13, 3, '2026-04-25', '2026-04-27', 0, NULL, 'Đã Thanh Toán 50%', 'ORD_137463176', NULL, 800000, 'Đã nhận 50%', 'Chưa Thanh Toán', NULL, NULL, '2026-04-25 22:30:13'),
(112, 13, 4, '2026-04-25', '2026-04-27', 0, NULL, 'Đã Thanh Toán 50%', 'ORD_135950060', NULL, 1000000, 'Đã nhận 50%', 'Chưa Thanh Toán', NULL, NULL, '2026-04-25 22:30:40'),
(113, 13, 3, '2026-04-27', '2026-04-29', 1, NULL, 'Đã Thanh Toán', 'ORD_131777291102', NULL, 1600000, 'Thành Công', 'Đã Thanh Toán', NULL, NULL, '2026-04-27 19:02:02'),
(114, 13, 3, '2026-04-28', '2026-04-30', 1, NULL, 'Đã Huỷ', 'ORD_131777291342', NULL, 0, 'Đã Huỷ', 'Đã Huỷ', NULL, NULL, '2026-04-27 19:02:24'),
(115, 13, 6, '2026-04-27', '2026-05-01', 0, NULL, 'pending', 'ORD_131777292210', NULL, 0, 'Đã Đặt', 'Chờ Thanh Toán', NULL, NULL, '2026-04-27 19:16:50'),
(116, 13, 6, '2026-04-27', '2026-04-29', 1, NULL, 'Đã Thanh Toán', 'ORD_131777292210', NULL, 3000000, 'Thành Công', 'Đã Thanh Toán', NULL, NULL, '2026-04-27 19:17:16'),
(117, 13, 6, '2026-04-27', '2026-04-30', 1, NULL, 'Đã Huỷ', 'ORD_131777292240', NULL, 0, 'Đã Huỷ', 'Đã Huỷ', NULL, NULL, '2026-04-27 19:18:10'),
(118, 13, 8, '2026-04-27', '2026-05-09', 1, NULL, 'Đã Thanh Toán', 'ORD_131777292330', NULL, 6000000, 'Thành Công', 'Đã Thanh Toán', NULL, NULL, '2026-04-27 19:18:51');

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `sr_no` int(11) NOT NULL,
  `image` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carousel`
--

INSERT INTO `carousel` (`sr_no`, `image`) VALUES
(4, 'IMG_97676.jpg'),
(5, 'IMG_93127.jpg'),
(6, 'IMG_87422.jpg'),
(8, 'IMG_55677.png');

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

CREATE TABLE `contact_details` (
  `sr_no` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` bigint(20) NOT NULL,
  `pn2` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `insta` varchar(100) NOT NULL,
  `tw` varchar(100) NOT NULL,
  `iframe` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_details`
--

INSERT INTO `contact_details` (`sr_no`, `address`, `gmap`, `pn1`, `pn2`, `email`, `fb`, `insta`, `tw`, `iframe`) VALUES
(1, 'Số 73, Nguyễn Huệ, Phường Long Châu, tỉnh Vĩnh Long', 'https://goo.gl/maps/RvFRc2kyeJ3Hsq3VA', 723004127, 23004122, '23004122@gmail.com', 'https://www.facebook.com/', 'https://www.facebook.com/', '', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.110348170116!2d105.69317477506567!3d18.65904358246151!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3139cddf0bf20f23:0x86154b56a284fa6d!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBWaW5o!5e0!3m2!1svi!2s!4v1682140114536!5m2!1svi!2s');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int(11) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `icon`, `name`, `description`) VALUES
(13, 'IMG_43553.svg', 'Wifi', 'Wifi trong khách sạn cho phép khách hàng kết nối internet không dây tốc độ cao, có thể truy cập vào các trang web yêu thích của họ, xem phim, nghe nhạc, tải xuống các tài liệu, và liên lạc với người thân và bạn bè. '),
(14, 'IMG_49949.svg', 'Điều Hoà', ' Khách hàng có thể dễ dàng điều chỉnh nhiệt độ và các tính năng khác trên điều hòa để đáp ứng các nhu cầu của họ và tạo ra một không gian nghỉ ngơi lý tưởng.'),
(15, 'IMG_41622.svg', 'Tivi', 'TV cung cấp cho khách hàng nhiều kênh truyền hình đa dạng và phong phú, bao gồm cả các kênh quốc tế và địa phương, các kênh phim, chương trình giải trí, tin tức, thể thao và giáo dục'),
(17, 'IMG_47816.svg', 'Spa', 'Tại spa, khách hàng có thể trải nghiệm các liệu pháp chăm sóc da, tắm thủy lực, massage và nhiều dịch vụ khác.'),
(18, 'IMG_96423.svg', 'Máy Sưởi', 'Máy sưởi phòng được thiết kế để giữ cho phòng ấm áp và thoải mái trong suốt mùa đông.'),
(19, 'IMG_27079.svg', 'Nóng Lạnh', 'Được trang bị các tính năng và thiết bị hiện đại như màn hình LCD hiển thị nhiệt độ, điều khiển từ xa, cảm biến nhiệt độ và chức năng tự động điều chỉnh nhiệt độ nước.');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `name`) VALUES
(18, 'Phòng Ngủ'),
(19, 'Ban Công'),
(20, 'Phòng Bếp'),
(21, 'Ghế sofa');

-- --------------------------------------------------------

--
-- Table structure for table `rating_review`
--

CREATE TABLE `rating_review` (
  `sr_no` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` varchar(200) NOT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating_review`
--

INSERT INTO `rating_review` (`sr_no`, `booking_id`, `room_id`, `user_id`, `rating`, `review`, `seen`, `datentime`) VALUES
(15, 108, 3, 11, 5, 'Phòng đẹp tốt', 1, '2026-03-14 22:13:08');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `area` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` varchar(350) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `removed` int(11) NOT NULL DEFAULT 0,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`, `removed`, `location`) VALUES
(3, 'Phòng Bình Dân', 30, 800000, 1, 5, 3, 'Phòng được trang bị các tiện nghi cơ bản như giường ngủ êm ái, bàn ghế nhỏ, tivi, quạt hoặc điều hòa, tủ quần áo và hệ thống chiếu sáng đầy đủ, mang lại không gian nghỉ ngơi thoải mái và tiện lợi cho du khách.', 1, 0, 'TP. Hồ Chí Minh'),
(4, 'Phòng cổ điển đơn giản', 40, 1000000, 2, 5, 5, 'Phòng được thiết kế theo phong cách cổ điển đơn giản, với tông màu ấm áp và nội thất gọn gàng. Không gian phòng thoáng mát, tạo cảm giác ấm cúng và dễ chịu cho du khách khi nghỉ ngơi. Các vật dụng được bố trí hài hòa, mang lại sự tiện lợi và thoải mái trong suốt thời gian lưu trú.', 1, 0, 'Đà Lạt'),
(5, 'Phòng bố trí đẹp', 40, 1200000, 3, 8, 6, 'Các tiện nghi trong phòng khách sạn bao gồm các ghế sofa và bàn, tivi màn hình phẳng, minibar, két sắt, điều hòa, máy sưởi, hệ thống chiếu sáng, tủ quần áo và giường ngủ thoải mái.', 1, 0, 'TP. Hồ Chí Minh'),
(6, 'Phòng sang trọng', 50, 1500000, 7, 9, 10, 'Các tiện nghi trong phòng khách sạn bao gồm các ghế sofa và bàn, tivi màn hình phẳng, minibar, két sắt, điều hòa, máy sưởi, hệ thống chiếu sáng, tủ quần áo và giường ngủ thoải mái.', 1, 0, 'Vĩnh Long'),
(7, 'Phòng VIP', 20, 800000, 2, 2, 2, 'Phòng VIP được thiết kế sang trọng và rộng rãi, với nội thất hiện đại và đầy đủ tiện nghi. Không gian phòng thoáng đãng, giường ngủ êm ái cùng khu vực nghỉ ngơi riêng, mang lại sự thoải mái và trải nghiệm nghỉ dưỡng cao cấp cho du khách.', 1, 1, 'Vĩnh Long'),
(8, 'Phòng Đơn', 20, 500000, 2, 2, 2, 'rộng rãi thoáng mát', 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `room_facilities`
--

CREATE TABLE `room_facilities` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `facilities_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_facilities`
--

INSERT INTO `room_facilities` (`sr_no`, `room_id`, `facilities_id`) VALUES
(52, 4, 13),
(53, 4, 14),
(54, 4, 19),
(65, 3, 13),
(66, 3, 14),
(67, 3, 15),
(68, 5, 13),
(69, 5, 14),
(70, 5, 19),
(71, 6, 13),
(72, 6, 14),
(73, 6, 15),
(74, 6, 18),
(75, 6, 19),
(77, 8, 13);

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `features_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_features`
--

INSERT INTO `room_features` (`sr_no`, `room_id`, `features_id`) VALUES
(36, 4, 18),
(37, 4, 19),
(38, 4, 20),
(46, 3, 18),
(47, 3, 20),
(48, 5, 18),
(49, 5, 19),
(50, 5, 21),
(51, 6, 18),
(52, 6, 19),
(53, 6, 20),
(54, 6, 21),
(56, 8, 18);

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `sr_no` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image` varchar(150) NOT NULL,
  `thumb` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`sr_no`, `room_id`, `image`, `thumb`) VALUES
(26, 3, 'phong1.jpg', 1),
(27, 3, 'phong2.jpg', 0),
(28, 4, 'phong3.jpg', 0),
(29, 4, 'phong4.jpg', 1),
(30, 5, 'phong5.jpg', 1),
(31, 5, 'phong6.jpg', 0),
(32, 6, 'phong7.jpg', 0),
(33, 6, 'phong8.jpg', 1),
(34, 8, 'phong9.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL,
  `site_title` varchar(255) NOT NULL,
  `site_about` varchar(250) NOT NULL,
  `shutdown` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`sr_no`, `site_title`, `site_about`, `shutdown`) VALUES
(1, 'Homstay Vĩnh Long', 'Homestay Vĩnh Long là một điểm lưu trú thân thiện và gần gũi với thiên nhiên, được thiết kế theo phong cách đơn giản nhưng đầy đủ tiện nghi. Homestay cung cấp các phòng nghỉ thoải mái, không gian sinh hoạt chung, khu vực ăn uống và khu vườn xanh mát,', 0);

-- --------------------------------------------------------

--
-- Table structure for table `team_details`
--

CREATE TABLE `team_details` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `picture` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_cred`
--

CREATE TABLE `user_cred` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `address` varchar(120) NOT NULL,
  `phonenum` varchar(100) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `profile` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `is_verified` int(11) NOT NULL DEFAULT 0,
  `token` varchar(10) DEFAULT NULL COMMENT 'Lưu mã PIN 6 số',
  `pin_expire` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `datentime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cred`
--

INSERT INTO `user_cred` (`id`, `name`, `email`, `address`, `phonenum`, `gender`, `dob`, `profile`, `password`, `is_verified`, `token`, `pin_expire`, `status`, `datentime`) VALUES
(9, 'Dat', 'nguyendat@gmail.com', 'Cai Nhum - Vinh Long', '0779880482', 'Nam', '2005-04-07', 'IMG_26096.jpg', '$2y$10$jJ1ecibawp8lN2t7MOIe5.R2yz49m0hwfUW2KPz3XF/DmwDphCAOC', 1, NULL, NULL, 1, '2023-04-22 14:32:35'),
(10, 'Nguyen Dat', 'ntdata2105@gmail.com', 'Bac Lieu - Tp Vinh - Nghe An', '0365417401', 'Nam', '2004-05-13', 'IMG_1.jpeg', '$2y$10$VKXJmosMmMcIs4xd1vnEIOd1dc8vpdfP3DjJOcPFKfLoqiEZwT.om', 1, NULL, NULL, 1, '2023-05-13 14:58:38'),
(11, 'Minh Em', 'nguyenhoangminhanh1511@gmail.com', 'An Nhon - Chau Thanh - Dong Thap', '0869034523', 'Nam', '2005-03-15', 'mecure.jpg', '$2y$10$IfgADiQxyVuqwjUBIu92r.OunWntY7La/k2P6/7oQs/v0m88j85aa', 123, NULL, NULL, 1, '2026-03-14 21:34:44'),
(13, 'Nguyễn Thành Đạt', 'ntdata9205@gmail.com', 'Vinh Long', '0123456789', 'Nam', '2026-04-01', 'USER_97208.png', '$2y$10$LHrI2lw0CVyaw87uWGQNueVo6NQLCSv8m0Ki16qXfvplPmlvtS0He', 1, '933372', '2026-04-26 07:41:50', 1, '2026-04-25 22:01:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_queries`
--

CREATE TABLE `user_queries` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_queries`
--

INSERT INTO `user_queries` (`sr_no`, `name`, `email`, `subject`, `message`, `datentime`, `seen`) VALUES
(16, 'Minh Em', 'nguyenhoangminhanh1511@gmail.com', 'emminh', 'phòng đẹp qué', '2026-03-14 22:13:36', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_cred`
--
ALTER TABLE `admin_cred`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `booking_order`
--
ALTER TABLE `booking_order`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `contact_details`
--
ALTER TABLE `contact_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rating_review`
--
ALTER TABLE `rating_review`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `facilities id` (`facilities_id`),
  ADD KEY `room id` (`room_id`);

--
-- Indexes for table `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `features id` (`features_id`),
  ADD KEY `rm id` (`room_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `team_details`
--
ALTER TABLE `team_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Indexes for table `user_cred`
--
ALTER TABLE `user_cred`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_queries`
--
ALTER TABLE `user_queries`
  ADD PRIMARY KEY (`sr_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_cred`
--
ALTER TABLE `admin_cred`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `booking_order`
--
ALTER TABLE `booking_order`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_details`
--
ALTER TABLE `contact_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `rating_review`
--
ALTER TABLE `rating_review`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_facilities`
--
ALTER TABLE `room_facilities`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `team_details`
--
ALTER TABLE `team_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_queries`
--
ALTER TABLE `user_queries`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`);

--
-- Constraints for table `booking_order`
--
ALTER TABLE `booking_order`
  ADD CONSTRAINT `booking_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`),
  ADD CONSTRAINT `booking_order_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `rating_review`
--
ALTER TABLE `rating_review`
  ADD CONSTRAINT `rating_review_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking_order` (`booking_id`),
  ADD CONSTRAINT `rating_review_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `rating_review_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user_cred` (`id`);

--
-- Constraints for table `room_facilities`
--
ALTER TABLE `room_facilities`
  ADD CONSTRAINT `facilities id` FOREIGN KEY (`facilities_id`) REFERENCES `facilities` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `room id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `room_features`
--
ALTER TABLE `room_features`
  ADD CONSTRAINT `features id` FOREIGN KEY (`features_id`) REFERENCES `features` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `rm id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
