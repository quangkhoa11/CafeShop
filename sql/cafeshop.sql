-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 19, 2025 lúc 02:59 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `cafeshop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonban`
--

CREATE TABLE `chitietdonban` (
  `idchitietdb` int(11) NOT NULL,
  `iddonban` varchar(10) DEFAULT NULL,
  `idsp` int(11) DEFAULT NULL,
  `idgiatri` int(11) DEFAULT NULL,
  `duong` varchar(100) DEFAULT NULL,
  `da` varchar(100) DEFAULT NULL,
  `size` varchar(100) DEFAULT NULL,
  `soluong` int(11) NOT NULL,
  `dongia` int(11) NOT NULL,
  `thanhtien` int(11) NOT NULL,
  `ghichu` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donban`
--

CREATE TABLE `donban` (
  `iddonban` varchar(10) NOT NULL,
  `idkh` int(11) DEFAULT NULL,
  `ngayban` date NOT NULL,
  `tennguoinhan` varchar(100) NOT NULL,
  `sdtnguoinhan` varchar(18) NOT NULL,
  `diachinhan` varchar(100) NOT NULL,
  `tongtien` int(11) NOT NULL,
  `idshop` int(11) DEFAULT NULL,
  `trangthai` enum('Đã thanh toán','Chưa thanh toán','Đang xử lý','Đã giao cho đơn vị vận chuyển','Hoàn thành','Đã hủy','Chờ thanh toán','Chờ xác nhận') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `idkh` int(11) NOT NULL,
  `tenkh` varchar(40) NOT NULL,
  `sdt` varchar(18) NOT NULL,
  `diachi` varchar(40) NOT NULL,
  `email` varchar(40) NOT NULL,
  `matkhau` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuyenmai`
--

CREATE TABLE `khuyenmai` (
  `idkm` int(11) NOT NULL,
  `idshop` int(11) NOT NULL,
  `tenkm` varchar(255) NOT NULL,
  `mota` text DEFAULT NULL,
  `giamgia` int(11) NOT NULL CHECK (`giamgia` >= 1 and `giamgia` <= 100),
  `ngay_bd` date NOT NULL,
  `ngay_kt` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaisp`
--

CREATE TABLE `loaisp` (
  `idloai` int(11) NOT NULL,
  `tenloai` varchar(40) NOT NULL,
  `mota` varchar(40) NOT NULL,
  `hinhanh` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loaisp`
--

INSERT INTO `loaisp` (`idloai`, `tenloai`, `mota`, `hinhanh`) VALUES
(3, 'Cà phê/Trà', 'Cà phê', 'cafe1.jpg'),
(4, 'Đồ ăn', 'Thức ăn nhẹ kèm theo để ăn', 'doan.jpg'),
(5, 'Bánh ngọt', 'Bánh ngọt', 'banhngot1.jpg'),
(6, 'Nước ngọt', 'Nước ngọt', 'nuocngot.png'),
(7, 'Trà sữa', 'Trà sữa', 'trasua1.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rating_sanpham`
--

CREATE TABLE `rating_sanpham` (
  `idrating` int(11) NOT NULL,
  `idsp` int(11) NOT NULL,
  `idkh` int(11) NOT NULL,
  `diem` int(11) NOT NULL CHECK (`diem` between 1 and 5),
  `binhluan` text DEFAULT NULL,
  `ngaytao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `idsp` int(11) NOT NULL,
  `idloai` int(11) NOT NULL,
  `tensp` varchar(50) NOT NULL,
  `gia` int(30) NOT NULL,
  `mota` varchar(40) NOT NULL,
  `hinhanh` varchar(40) NOT NULL,
  `idshop` int(11) DEFAULT NULL,
  `trangthai` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`idsp`, `idloai`, `tensp`, `gia`, `mota`, `hinhanh`, `idshop`, `trangthai`) VALUES
(1, 3, 'Cà Phê Sữa', 25000, 'Cà phê (40%) + Sữa (60%)', 'cafesua.jpeg', 1, 1),
(2, 3, 'Cà Phê Đen', 30000, 'Cà phê đen + ít đường', 'cafeden.jpeg', 1, 1),
(3, 4, 'Bánh Mì Pate', 35000, 'Bánh mì thập cẩm có pate', 'banhmi.jpg', 1, 1),
(4, 5, 'Bánh Ngọt Vị Dâu', 45000, 'Bánh bông lan kem dâu', 'banhngot.jpg', 1, 1),
(5, 3, 'Cà Phê Muối', 23000, 'Cà phê đen + Muối', 'cafemuoi.jpg', 2, 1),
(6, 4, 'Bánh Croissant', 30000, 'Bánh ngọt', 'croissant.jpg', 1, 1),
(9, 3, 'Sinh Tố Xoài', 31000, 'Xoài', 'sinhto.jpg', 1, 1),
(10, 7, 'Trà Sữa Thái Truyền Thống', 45000, 'Trà sữa + Chân châu', 'trasua.jpg', 1, 1),
(11, 3, 'Trà Tắc Chanh', 32100, 'Trà tắc + Chanh', 'tratac.jpg', 1, 1),
(14, 6, 'Nước Pepsi 2', 50000, 'Nước pep', '1763086118_pepsi.jpg', 2, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shop`
--

CREATE TABLE `shop` (
  `idshop` int(11) NOT NULL,
  `tenshop` varchar(100) NOT NULL,
  `diachi` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `matkhau` varchar(300) NOT NULL,
  `anhbia` varchar(100) DEFAULT NULL,
  `logo` varchar(100) NOT NULL,
  `sdt` varchar(100) NOT NULL,
  `lat_shop` double DEFAULT NULL,
  `lng_shop` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shop`
--

INSERT INTO `shop` (`idshop`, `tenshop`, `diachi`, `email`, `matkhau`, `anhbia`, `logo`, `sdt`, `lat_shop`, `lng_shop`) VALUES
(1, 'Neties Cafe', 'Đường Nguyễn Văn Trỗi, Phường Phú Nhuận, Thành phố Thủ Đức, Thành phố Hồ Chí Minh, 72215, Việt Nam', 'nesticafe@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', 'bia_nc.jpg', 'logo_nestie.png', '0927382910', 10.795944250812571, 106.67518615722658),
(2, 'The Coffee House', 'Phan Văn Trị', 'coffeehouse@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', 'bia_ch.jpg', 'logo_ch.jpg\r\n', '0927361738', 10.830002, 106.6800339),
(3, 'Cà Phê Hương Việt', '82, Nguyễn Tất Thành, Phường Xóm Chiếu, Thành phố Thủ Đức, Thành phố Hồ Chí Minh, 72806, Việt Nam', 'huongvietcoffee@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', '1763093615_cover_bia_vh.jpg', 'logo_huongviet.jpg', '0909123456', 10.76550905599287, 106.70677185058595),
(5, 'Highlands Coffee', 'Deme Brewing, 7, Hẻm 393 Hai Bà Trưng, Phường Xuân Hòa, Thành phố Hồ Chí Minh, 72200, Việt Nam', 'highlandscoffee@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', 'bia_hl.jpg', 'logo_hl.png', '0933456789', 10.789648922954232, 106.68565750122072),
(6, 'Cộng Cà Phê', 'Hẻm 68 Út Tịch, Phường Tân Sơn Nhất, Thành phố Thủ Đức, Thành phố Hồ Chí Minh, 72106, Việt Nam', 'congcaphe@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', 'bia_cong.jpg', 'logo_cong.jpg', '0945678901', 10.795913335125556, 106.65853500366212),
(7, 'PhinDeli Coffee', '23 Nguyễn Trãi, Q.5, TP.HCM', 'phindeli@gmail.com', '123456', 'bia_phindeli.jpg', 'logo_phindeli.png', '0956789012', 10.7573427, 106.6798886),
(8, 'Urban Station', 'Ấp 4, Xã Bình Chánh, Thành phố Hồ Chí Minh, Việt Nam', 'urbanstation@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', 'bia_urban.jpg', 'logo_urban.jpg', '0967890123', 10.671123310713016, 106.57012939453126),
(12, 'Katinat', 'Ấp 9, Xã Tân Nhựt, Thành phố Hồ Chí Minh, Việt Nam', 'katinat@gmail.com', '$2y$10$jLPK3rSyk4m149xufavWpu4.5VLiH3U6W1riDkZmnyKYVvpM8TQtO', '1763093880_cover_ly-cafe-18.jpg', '1763021061_logo_katinat.jpg', '0928394929', 10.707952315309532, 106.56497955322266),
(13, 'Phúc Long', 'Xã An Thới Đông, Thành phố Hồ Chí Minh, Việt Nam', 'phuclong@gmail.com', '$2y$10$yC4i8WP.4ebSytxTeVoNpefT8Rr6sCgGe38gNmre1X.UqoPiYZGS6', '1763094703_cover_bia_25.jpg', '1763018962_logo_phuclong.png', '0928374893', 10.538580775606885, 106.77612304687501),
(16, 'Coffee Place', 'Hẻm 487/47/11 Huỳnh Tấn Phát, Phường Tân Thuận, Thành phố Hồ Chí Minh, 71800, Việt Nam', 'coffeeplace@gmail.com', '$2y$10$VU7.YYWrsvxAIYaJUw9O.eF3IzchE0ckUHrYwPCIldSLCrhaymbFu', 'cover_place.jpeg', 'logo_place.jpg', '0982536278', 10.746530825860322, 106.73183441162111);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdonban`
--
ALTER TABLE `chitietdonban`
  ADD PRIMARY KEY (`idchitietdb`),
  ADD KEY `iddonban` (`iddonban`),
  ADD KEY `idsp` (`idsp`),
  ADD KEY `idgiatri` (`idgiatri`);

--
-- Chỉ mục cho bảng `donban`
--
ALTER TABLE `donban`
  ADD PRIMARY KEY (`iddonban`),
  ADD KEY `idkh` (`idkh`),
  ADD KEY `idshop` (`idshop`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`idkh`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`idkm`),
  ADD KEY `fk_km_shop` (`idshop`);

--
-- Chỉ mục cho bảng `loaisp`
--
ALTER TABLE `loaisp`
  ADD PRIMARY KEY (`idloai`);

--
-- Chỉ mục cho bảng `rating_sanpham`
--
ALTER TABLE `rating_sanpham`
  ADD PRIMARY KEY (`idrating`),
  ADD KEY `idsp` (`idsp`),
  ADD KEY `idkh` (`idkh`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`idsp`),
  ADD KEY `idloai` (`idloai`),
  ADD KEY `idshop` (`idshop`);

--
-- Chỉ mục cho bảng `shop`
--
ALTER TABLE `shop`
  ADD PRIMARY KEY (`idshop`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chitietdonban`
--
ALTER TABLE `chitietdonban`
  MODIFY `idchitietdb` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `idkh` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  MODIFY `idkm` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loaisp`
--
ALTER TABLE `loaisp`
  MODIFY `idloai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `rating_sanpham`
--
ALTER TABLE `rating_sanpham`
  MODIFY `idrating` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `idsp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `shop`
--
ALTER TABLE `shop`
  MODIFY `idshop` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdonban`
--
ALTER TABLE `chitietdonban`
  ADD CONSTRAINT `chitietdonban_ibfk_1` FOREIGN KEY (`iddonban`) REFERENCES `donban` (`iddonban`),
  ADD CONSTRAINT `chitietdonban_ibfk_2` FOREIGN KEY (`idsp`) REFERENCES `sanpham` (`idsp`);

--
-- Các ràng buộc cho bảng `donban`
--
ALTER TABLE `donban`
  ADD CONSTRAINT `donban_ibfk_1` FOREIGN KEY (`idkh`) REFERENCES `khachhang` (`idkh`),
  ADD CONSTRAINT `donban_ibfk_2` FOREIGN KEY (`idshop`) REFERENCES `shop` (`idshop`);

--
-- Các ràng buộc cho bảng `khuyenmai`
--
ALTER TABLE `khuyenmai`
  ADD CONSTRAINT `fk_km_shop` FOREIGN KEY (`idshop`) REFERENCES `shop` (`idshop`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `rating_sanpham`
--
ALTER TABLE `rating_sanpham`
  ADD CONSTRAINT `rating_sanpham_ibfk_1` FOREIGN KEY (`idsp`) REFERENCES `sanpham` (`idsp`),
  ADD CONSTRAINT `rating_sanpham_ibfk_2` FOREIGN KEY (`idkh`) REFERENCES `khachhang` (`idkh`);

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`idloai`) REFERENCES `loaisp` (`idloai`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`idshop`) REFERENCES `shop` (`idshop`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
