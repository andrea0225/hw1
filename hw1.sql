-- --------------------------------------------------------
-- Host:                         192.168.2.22
-- Versione server:              10.3.39-MariaDB - MariaDB Server
-- S.O. server:                  Linux
-- HeidiSQL Versione:            12.5.0.6677
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dump della struttura del database hw1
CREATE DATABASE IF NOT EXISTS `hw1` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `hw1`;

-- Dump della struttura di tabella hw1.cart
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qty` int(11) NOT NULL,
  `id_items` int(11) DEFAULT NULL,
  `id_users` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Indice 4` (`id_items`,`id_users`) USING BTREE,
  KEY `idx_items` (`id_items`),
  KEY `idx_users` (`id_users`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`id_items`) REFERENCES `items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella hw1.cart: ~9 rows (circa)
INSERT INTO `cart` (`id`, `qty`, `id_items`, `id_users`) VALUES
	(153, 1, 15, 1),
	(157, 11, 11, 2),
	(168, 1, 10, 2),
	(169, 1, 17, 2),
	(173, 4, 20, 1),
	(177, 3, 12, 1),
	(180, 4, 20, 2),
	(184, 5, 7, 1),
	(189, 1, 16, 1);

-- Dump della struttura di tabella hw1.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` char(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella hw1.categories: ~4 rows (circa)
INSERT INTO `categories` (`id`, `nome`) VALUES
	(3, 'electronics'),
	(2, 'jewelery'),
	(1, 'men\'s clothing'),
	(4, 'women\'s clothing');

-- Dump della struttura di tabella hw1.favourites
CREATE TABLE IF NOT EXISTS `favourites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Indice 4` (`id_user`,`id_item`),
  KEY `idxiduser` (`id_user`),
  KEY `idxiditem` (`id_item`),
  CONSTRAINT `favourites_ibfk_1` FOREIGN KEY (`id_item`) REFERENCES `items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `favourites_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella hw1.favourites: ~18 rows (circa)
INSERT INTO `favourites` (`id`, `id_item`, `id_user`) VALUES
	(95, 1, 1),
	(84, 2, 1),
	(93, 5, 1),
	(90, 6, 1),
	(88, 7, 1),
	(89, 8, 1),
	(68, 9, 1),
	(73, 11, 1),
	(67, 13, 1),
	(87, 14, 1),
	(62, 15, 1),
	(63, 16, 1),
	(99, 19, 1),
	(98, 20, 1),
	(76, 2, 2),
	(78, 9, 2),
	(45, 19, 2),
	(44, 20, 2);

-- Dump della struttura di tabella hw1.items
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) DEFAULT NULL,
  `title` longtext DEFAULT NULL,
  `price` float DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image_path` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`id_category`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella hw1.items: ~20 rows (circa)
INSERT INTO `items` (`id`, `id_category`, `title`, `price`, `description`, `image_path`) VALUES
	(1, 1, 'Fjallraven - Foldsack No. 1 Backpack, Fits 15 Laptops', 109.95, 'Your perfect pack for everyday use and walks in the forest. Stash your laptop (up to 15 inches) in the padded sleeve, your everyday', 'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg'),
	(2, 1, 'Mens Casual Premium Slim Fit T-Shirts ', 22.3, 'Slim-fitting style, contrast raglan long sleeve, three-button henley placket, light weight & soft fabric for breathable and comfortable wearing. And Solid stitched shirts with round neck made for durability and a great fit for casual fashion wear and diehard baseball fans. The Henley style round neckline includes a three-button placket.', 'https://fakestoreapi.com/img/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg'),
	(3, 1, 'Mens Cotton Jacket', 55.99, 'great outerwear jackets for Spring/Autumn/Winter, suitable for many occasions, such as working, hiking, camping, mountain/rock climbing, cycling, traveling or other outdoors. Good gift choice for you or your family member. A warm hearted love to Father, husband or son in this thanksgiving or Christmas Day.', 'https://fakestoreapi.com/img/71li-ujtlUL._AC_UX679_.jpg'),
	(4, 1, 'Mens Casual Slim Fit', 15.99, 'The color could be slightly different between on the screen and in practice. / Please note that body builds vary by person, therefore, detailed size information should be reviewed below on the product description.', 'https://fakestoreapi.com/img/71YXzeOuslL._AC_UY879_.jpg'),
	(5, 2, 'John Hardy Women\'s Legends Naga Gold & Silver Dragon Station Chain Bracelet', 695, 'From our Legends Collection, the Naga was inspired by the mythical water dragon that protects the ocean\'s pearl. Wear facing inward to be bestowed with love and abundance, or outward for protection.', 'https://fakestoreapi.com/img/71pWzhdJNwL._AC_UL640_QL65_ML3_.jpg'),
	(6, 2, 'Solid Gold Petite Micropave ', 168, 'Satisfaction Guaranteed. Return or exchange any order within 30 days.Designed and sold by Hafeez Center in the United States. Satisfaction Guaranteed. Return or exchange any order within 30 days.', 'https://fakestoreapi.com/img/61sbMiUnoGL._AC_UL640_QL65_ML3_.jpg'),
	(7, 2, 'White Gold Plated Princess', 9.99, 'Classic Created Wedding Engagement Solitaire Diamond Promise Ring for Her. Gifts to spoil your love more for Engagement, Wedding, Anniversary, Valentine\'s Day...', 'https://fakestoreapi.com/img/71YAIFU48IL._AC_UL640_QL65_ML3_.jpg'),
	(8, 2, 'Pierced Owl Rose Gold Plated Stainless Steel Double', 10.99, 'Rose Gold Plated Double Flared Tunnel Plug Earrings. Made of 316L Stainless Steel', 'https://fakestoreapi.com/img/51UDEzMJVpL._AC_UL640_QL65_ML3_.jpg'),
	(9, 3, 'WD 2TB Elements Portable External Hard Drive - USB 3.0 ', 64, 'USB 3.0 and USB 2.0 Compatibility Fast data transfers Improve PC Performance High Capacity; Compatibility Formatted NTFS for Windows 10, Windows 8.1, Windows 7; Reformatting may be required for other operating systems; Compatibility may vary depending on userâ€™s hardware configuration and operating system', 'https://fakestoreapi.com/img/61IBBVJvSDL._AC_SY879_.jpg'),
	(10, 3, 'SanDisk SSD PLUS 1TB Internal SSD - SATA III 6 Gb/s', 109, 'Easy upgrade for faster boot up, shutdown, application load and response (As compared to 5400 RPM SATA 2.5â€ hard drive; Based on published specifications and internal benchmarking tests using PCMark vantage scores) Boosts burst write performance, making it ideal for typical PC workloads The perfect balance of performance and reliability Read/write speeds of up to 535MB/s/450MB/s (Based on internal testing; Performance may vary depending upon drive capacity, host device, OS and application.)', 'https://fakestoreapi.com/img/61U7T1koQqL._AC_SX679_.jpg'),
	(11, 3, 'Silicon Power 256GB SSD 3D NAND A55 SLC Cache Performance Boost SATA III 2.5', 109, '3D NAND flash are applied to deliver high transfer speeds Remarkable transfer speeds that enable faster bootup and improved overall system performance. The advanced SLC Cache Technology allows performance boost and longer lifespan 7mm slim design suitable for Ultrabooks and Ultra-slim notebooks. Supports TRIM command, Garbage Collection technology, RAID, and ECC (Error Checking & Correction) to provide the optimized performance and enhanced reliability.', 'https://fakestoreapi.com/img/71kWymZ+c+L._AC_SX679_.jpg'),
	(12, 3, 'WD 4TB Gaming Drive Works with Playstation 4 Portable External Hard Drive', 114, 'Expand your PS4 gaming experience, Play anywhere Fast and easy, setup Sleek design with high capacity, 3-year manufacturer\'s limited warranty', 'https://fakestoreapi.com/img/61mtL65D4cL._AC_SX679_.jpg'),
	(13, 3, 'Acer SB220Q bi 21.5 inches Full HD (1920 x 1080) IPS Ultra-Thin', 599, '21. 5 inches Full HD (1920 x 1080) widescreen IPS display And Radeon free Sync technology. No compatibility for VESA Mount Refresh Rate: 75Hz - Using HDMI port Zero-frame design | ultra-thin | 4ms response time | IPS panel Aspect ratio - 16: 9. Color Supported - 16. 7 million colors. Brightness - 250 nit Tilt angle -5 degree to 15 degree. Horizontal viewing angle-178 degree. Vertical viewing angle-178 degree 75 hertz', 'https://fakestoreapi.com/img/81QpkIctqPL._AC_SX679_.jpg'),
	(14, 3, 'Samsung 49-Inch CHG90 144Hz Curved Gaming Monitor (LC49HG90DMNXZA) â€“ Super Ultrawide Screen QLED ', 999.99, '49 INCH SUPER ULTRAWIDE 32:9 CURVED GAMING MONITOR with dual 27 inch screen side by side QUANTUM DOT (QLED) TECHNOLOGY, HDR support and factory calibration provides stunningly realistic and accurate color and contrast 144HZ HIGH REFRESH RATE and 1ms ultra fast response time work to eliminate motion blur, ghosting, and reduce input lag', 'https://fakestoreapi.com/img/81Zt42ioCgL._AC_SX679_.jpg'),
	(15, 4, 'BIYLACLESEN Women\'s 3-in-1 Snowboard Jacket Winter Coats', 56.99, 'Note:The Jackets is US standard size, Please choose size as your usual wear Material: 100% Polyester; Detachable Liner Fabric: Warm Fleece. Detachable Functional Liner: Skin Friendly, Lightweigt and Warm.Stand Collar Liner jacket, keep you warm in cold weather. Zippered Pockets: 2 Zippered Hand Pockets, 2 Zippered Pockets on Chest (enough to keep cards or keys)and 1 Hidden Pocket Inside.Zippered Hand Pockets and Hidden Pocket keep your things secure. Humanized Design: Adjustable and Detachable Hood and Adjustable cuff to prevent the wind and water,for a comfortable fit. 3 in 1 Detachable Design provide more convenience, you can separate the coat and inner as needed, or wear it together. It is suitable for different season and help you adapt to different climates', 'https://fakestoreapi.com/img/51Y5NI-I5jL._AC_UX679_.jpg'),
	(16, 4, 'Lock and Love Women\'s Removable Hooded Faux Leather Moto Biker Jacket', 29.95, '100% POLYURETHANE(shell) 100% POLYESTER(lining) 75% POLYESTER 25% COTTON (SWEATER), Faux leather material for style and comfort / 2 pockets of front, 2-For-One Hooded denim style faux leather jacket, Button detail on waist / Detail stitching at sides, HAND WASH ONLY / DO NOT BLEACH / LINE DRY / DO NOT IRON', 'https://fakestoreapi.com/img/81XH0e8fefL._AC_UY879_.jpg'),
	(17, 4, 'Rain Jacket Women Windbreaker Striped Climbing Raincoats', 39.99, 'Lightweight perfet for trip or casual wear---Long sleeve with hooded, adjustable drawstring waist design. Button and zipper front closure raincoat, fully stripes Lined and The Raincoat has 2 side pockets are a good size to hold all kinds of things, it covers the hips, and the hood is generous but doesn\'t overdo it.Attached Cotton Lined Hood with Adjustable Drawstrings give it a real styled look.', 'https://fakestoreapi.com/img/71HblAHs5xL._AC_UY879_-2.jpg'),
	(18, 4, 'MBJ Women\'s Solid Short Sleeve Boat Neck V ', 9.85, '95% RAYON 5% SPANDEX, Made in USA or Imported, Do Not Bleach, Lightweight fabric with great stretch for comfort, Ribbed on sleeves and neckline / Double stitching on bottom hem', 'https://fakestoreapi.com/img/71z3kpMAYsL._AC_UY879_.jpg'),
	(19, 4, 'Opna Women\'s Short Sleeve Moisture', 7.95, '100% Polyester, Machine wash, 100% cationic polyester interlock, Machine Wash & Pre Shrunk for a Great Fit, Lightweight, roomy and highly breathable with moisture wicking fabric which helps to keep moisture away, Soft Lightweight Fabric with comfortable V-neck collar and a slimmer fit, delivers a sleek, more feminine silhouette and Added Comfort', 'https://fakestoreapi.com/img/51eg55uWmdL._AC_UX679_.jpg'),
	(20, 4, 'DANVOUY Womens T Shirt Casual Cotton Short', 12.99, '95%Cotton,5%Spandex, Features: Casual, Short Sleeve, Letter Print,V-Neck,Fashion Tees, The fabric is soft and has some stretch., Occasion: Casual/Office/Beach/School/Home/Street. Season: Spring,Summer,Autumn,Winter.', 'https://fakestoreapi.com/img/61pHAEJ4NML._AC_UX679_.jpg');

-- Dump della struttura di tabella hw1.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` char(50) NOT NULL,
  `cognome` char(50) NOT NULL,
  `email` char(50) NOT NULL,
  `cellulare` char(13) NOT NULL,
  `username` char(20) NOT NULL,
  `password` char(255) NOT NULL,
  `ragione_sociale` char(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `cellulare` (`cellulare`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dump dei dati della tabella hw1.users: ~2 rows (circa)
INSERT INTO `users` (`id`, `nome`, `cognome`, `email`, `cellulare`, `username`, `password`, `ragione_sociale`) VALUES
	(1, 'Andrea', 'Pagano', 'andreapagano.0225@gmail.com', '+393925570499', 'andrea0225', '$2y$10$3V953asH4SR5tSpyGsWRC.aBlKYwuG90H086B/Fi2KhiyOzjWwuAi', ''),
	(2, 'salvatore', 'pagano', 'salvopagano96@gmail.com', '3925548722', 'salvo96', '$2y$10$QTlXCnoJaoV/PWJyyn4RGehJ8S5a25eLKJ7EPTbS8w/K.3qAbeGW2', '');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
