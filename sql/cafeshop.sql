-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 03, 2025 lúc 03:47 AM
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
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `idkh` int(11) NOT NULL,
  `tenkh` varchar(40) NOT NULL,
  `sdt` varchar(13) NOT NULL,
  `diachi` varchar(40) NOT NULL,
  `email` varchar(40) NOT NULL,
  `matkhau` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`idkh`, `tenkh`, `sdt`, `diachi`, `email`, `matkhau`) VALUES
(2, 'Văn A', '0932424327', 'Việt Nam', 'dshdhsds@gmail.com', '12345');

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
  `hinhanh` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`idsp`, `idloai`, `tensp`, `gia`, `mota`, `hinhanh`) VALUES
(1, 3, 'Cà Phê Sữa', 25000, 'Cà phê (40%) + Sữa (60%)', 'cafesua.jpeg'),
(2, 3, 'Cà Phê Đen', 30000, 'Cà phê đen + ít đường', 'cafeden.jpeg'),
(3, 4, 'Bánh Mì Pate', 35000, 'Bánh mì thập cẩm có pate', 'banhmi.jpg'),
(4, 4, 'Bánh Ngọt Vị Dâu', 45000, 'Bánh bông lan kem dâu', 'banhngot.jpg'),
(5, 3, 'Cà Phê Muối', 23000, 'Cà phê đen + Muối', 'cafemuoi.jpg'),
(6, 4, 'Bánh Croissant', 30000, 'Bánh ngọt', 'croissant.jpg'),
(9, 3, 'Sinh Tố Xoài', 31000, 'Xoài', 'sinhto.jpg'),
(10, 3, 'Trà Sữa Thái Truyền Thống', 45000, 'Trà sữa + Chân châu', 'trasua.jpg'),
(11, 3, 'Trà Tắc Chanh', 32000, 'Trà tắc + Chanh', 'tratac.jpg');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`idkh`),
  ADD UNIQUE KEY `sdt` (`sdt`),
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
  ADD KEY `idloai` (`idloai`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `idkh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `loaisp`
--
ALTER TABLE `loaisp`
  MODIFY `idloai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  MODIFY `idsp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`idloai`) REFERENCES `loaisp` (`idloai`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
