-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 04, 2025 lúc 09:19 AM
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
  `trangthai` enum('Đã thanh toán','Chưa thanh toán','Đang xử lý','Đã giao cho đơn vị vận chuyển','Hoàn thành','Đã hủy','Chờ thanh toán') NOT NULL
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
-- Cấu trúc bảng cho bảng `loaisp`
--

CREATE TABLE `loaisp` (
  `idloai` int(11) NOT NULL,
  `tenloai` varchar(40) NOT NULL,
  `mota` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loaisp`
--

INSERT INTO `loaisp` (`idloai`, `tenloai`, `mota`) VALUES
(3, 'Đồ uống', 'Các món nước'),
(4, 'Thức ăn nhẹ', 'Thức ăn nhẹ kèm theo để ăn');

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
(4, 4, 'Bánh Ngọt Vị Dâu', 45000, 'Bánh bông lan kem dâu', 'banhngot.jpg', 1, 1),
(5, 3, 'Cà Phê Muối', 23000, 'Cà phê đen + Muối', 'cafemuoi.jpg', 2, 1),
(6, 4, 'Bánh Croissant', 30000, 'Bánh ngọt', 'croissant.jpg', 1, 1),
(9, 3, 'Sinh Tố Xoài', 31000, 'Xoài', 'sinhto.jpg', 1, 1),
(10, 3, 'Trà Sữa Thái Truyền Thống', 45000, 'Trà sữa + Chân châu', 'trasua.jpg', 1, 1),
(11, 3, 'Trà Tắc Chanh', 32100, 'Trà tắc + Chanh', 'tratac.jpg', 1, 0),
(14, 3, 'Nước Pepsi 2', 50000, 'Nước pep', 'pepsi.jpg', 2, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shop`
--

CREATE TABLE `shop` (
  `idshop` int(11) NOT NULL,
  `tenshop` varchar(100) NOT NULL,
  `diachi` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `matkhau` varchar(300) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `sdt` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `shop`
--

INSERT INTO `shop` (`idshop`, `tenshop`, `diachi`, `email`, `matkhau`, `logo`, `sdt`) VALUES
(1, 'Neties Cafe', 'Phan Văn Trị j', 'nesticafe@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', 'assets/images/1761644163_logocafe.png', '0927382910'),
(2, 'Coffee House', 'Phan Văn Trị', 'coffeehouse@gmail.com', '$2y$10$FrRkTpNV/9AhxCSYKNfjs.RwaYquAcUtQGGNPMugqicD2SqIEnVFq', 'assets/images/logopep.png', '0927361738');

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
-- Chỉ mục cho bảng `loaisp`
--
ALTER TABLE `loaisp`
  ADD PRIMARY KEY (`idloai`);

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
-- AUTO_INCREMENT cho bảng `loaisp`
--
ALTER TABLE `loaisp`
  MODIFY `idloai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `idsp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `shop`
--
ALTER TABLE `shop`
  MODIFY `idshop` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`idloai`) REFERENCES `loaisp` (`idloai`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`idshop`) REFERENCES `shop` (`idshop`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
