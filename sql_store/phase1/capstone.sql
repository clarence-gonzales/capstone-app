-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 22, 2025 at 11:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capstone`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `username`, `comment`, `created_at`) VALUES
(63, 65, 'clarence', 'ola', '2025-04-30 04:07:00'),
(67, 64, 'prettyjoraine', 'let\'s try again?', '2025-04-30 04:11:22'),
(69, 65, 'prettyjoraine', 'hola', '2025-04-30 04:15:13'),
(70, 65, 'prettyjoraine', 'let\'s try again, baby?', '2025-04-30 04:15:31'),
(71, 64, 'prettyjoraine', 'oumki', '2025-04-30 04:25:02'),
(72, 65, 'prettyjoraine', 'asas', '2025-04-30 04:27:03'),
(73, 63, 'prettyjoraine', 'hello', '2025-04-30 07:57:07');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `username`, `created_at`) VALUES
(28, 61, 'clarence', '2025-04-30 03:54:23'),
(29, 64, 'clarence', '2025-04-30 03:54:48'),
(30, 63, 'clarence', '2025-04-30 03:54:50'),
(31, 56, 'clarence', '2025-04-30 03:57:49'),
(32, 65, 'clarence', '2025-04-30 04:08:23'),
(33, 65, 'prettyjoraine', '2025-04-30 04:09:30'),
(34, 64, 'prettyjoraine', '2025-04-30 04:25:05'),
(35, 63, 'prettyjoraine', '2025-04-30 07:57:00'),
(36, 66, 'clarence', '2025-04-30 08:21:50'),
(37, 69, 'clarence', '2025-05-04 13:12:04'),
(38, 69, 'prettyjoraine', '2025-05-04 13:17:32');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sender_username` varchar(255) NOT NULL,
  `receiver_username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messagess`
--

CREATE TABLE `messagess` (
  `msg_id` int(11) NOT NULL,
  `incoming_msg_id` varchar(255) NOT NULL,
  `outgoing_msg_id` varchar(255) NOT NULL,
  `msg` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messagess`
--

INSERT INTO `messagess` (`msg_id`, `incoming_msg_id`, `outgoing_msg_id`, `msg`) VALUES
(13, 'Joraine', 'clarence', 'oy'),
(21, 'Clarence', 'prettyjoraine', 'Hi, Clarence!'),
(154, 'Joraine', 'clarence', 'Hello'),
(155, 'Clarence', 'prettyjoraine', 'uy, hi'),
(156, 'Joraine', 'clarence', 'Marunong ka ba kumanta?'),
(157, 'Clarence', 'prettyjoraine', 'medyo lang'),
(158, 'Clarence', 'prettyjoraine', 'bakit?'),
(159, 'Joraine', 'clarence', 'Baka pwede kita makasama bumuo ng banda?'),
(160, 'Clarence', 'prettyjoraine', 'G!'),
(161, 'Joraine', 'clarence', 'Sakto marunong ako mag gitara hahha'),
(162, 'Clarence', 'prettyjoraine', 'sige-sige'),
(163, 'Joraine', 'clarence', 'Kita nalang tayo sa likod ng grandstand ng NEUST?'),
(164, 'Clarence', 'prettyjoraine', 'kk, see yah'),
(165, 'Joraine', 'clarence', 'hii, again'),
(166, 'Clarence L.', 'prettyjoraine', 'uy, hi'),
(167, 'Clarence L.', 'prettyjoraine', 'uwu'),
(168, 'Joraine', 'clarence', 'haha');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `instrument` varchar(255) NOT NULL,
  `like_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `image`, `created_at`, `username`, `instrument`, `like_count`) VALUES
(1, NULL, 'kddg', NULL, '2025-04-09 05:38:48', 'clarence', '', 0),
(2, NULL, 'smfnsf', NULL, '2025-04-09 05:45:18', 'clarence', '', 0),
(3, NULL, 'sjnfsf\r\n', NULL, '2025-04-09 05:48:23', 'clarence', '', 0),
(4, NULL, 'clarence', NULL, '2025-04-09 05:51:06', 'clarence', '', 0),
(5, NULL, 'karding\r\n', NULL, '2025-04-09 05:59:33', 'clarence', '', 0),
(6, NULL, 'lele', NULL, '2025-04-09 06:12:45', 'clarence', '', 0),
(7, NULL, 'kingkong\r\n', NULL, '2025-04-09 06:19:32', 'clarence', '', 0),
(8, NULL, 'fjsnfs', NULL, '2025-04-09 06:22:46', 'clarence', '', 0),
(9, NULL, 'LOL\r\n', NULL, '2025-04-09 06:25:37', 'clarence', '', 0),
(10, NULL, 'ano ba toooooo aaaaaaa', NULL, '2025-04-09 06:27:11', 'clarence', '', 0),
(11, NULL, 'hi', NULL, '2025-04-09 06:40:18', 'clarence', '', 0),
(12, NULL, 'kksd', NULL, '2025-04-09 06:51:16', 'clarence', '', 0),
(13, NULL, 'potangina\r\n', NULL, '2025-04-09 06:53:07', 'clarence', '', 0),
(14, NULL, 'hays\r\n', NULL, '2025-04-09 06:58:13', 'clarence', '', 0),
(15, NULL, 'bakit ba sa dashboard.php napupuntaaaaa!\r\n', NULL, '2025-04-09 07:00:29', 'clarence', '', 0),
(16, NULL, 'Looking for durmmer na marunong tumugtog ng gitara HAHHAHAHAHAHAHHAHAH', NULL, '2025-04-09 07:01:30', 'clarence', '', 0),
(17, NULL, 'hi ulittt', NULL, '2025-04-09 07:06:42', 'clarence', '', 0),
(18, NULL, 'gumana ka naaaa!', NULL, '2025-04-09 07:07:16', 'clarence', '', 0),
(19, NULL, 'sfslmfs', NULL, '2025-04-09 07:08:55', 'clarence', '', 0),
(20, NULL, 'lolo\r\n', NULL, '2025-04-09 07:09:48', 'clarence', '', 0),
(21, NULL, 'Gumana na???\r\n', NULL, '2025-04-09 07:09:59', 'clarence', '', 0),
(22, NULL, 'Potaa sa wakass!!!', NULL, '2025-04-09 07:10:35', 'clarence', '', 0),
(23, NULL, 'orayts', NULL, '2025-04-09 07:12:55', 'clarence', '', 0),
(24, NULL, 'lala', NULL, '2025-04-09 07:14:44', 'clarence', '', 0),
(25, NULL, 'los', NULL, '2025-04-09 07:18:37', 'clarence', '', 0),
(26, NULL, 'angeles', NULL, '2025-04-09 07:19:34', 'clarence', '', 0),
(29, NULL, 'praise the lord', NULL, '2025-04-09 07:21:23', 'clarence', '', 0),
(30, NULL, 'sifksfs', NULL, '2025-04-09 07:23:08', 'clarence', '', 0),
(31, NULL, 'orayts', NULL, '2025-04-09 07:23:48', 'clarence', '', 0),
(32, NULL, 'clarence', NULL, '2025-04-09 07:37:06', 'clarence', '', 0),
(33, NULL, 'L.', NULL, '2025-04-09 07:42:54', 'clarence', '', 0),
(34, NULL, 'Gonzales', NULL, '2025-04-09 07:49:17', 'clarence', '', 0),
(35, NULL, 'Jo', NULL, '2025-04-09 07:50:53', 'prettyjoraine', '', 0),
(36, NULL, 'C', NULL, '2025-04-09 08:24:30', 'clarence', '', 0),
(37, NULL, 'Loose - Daniel Caesar\r\n', NULL, '2025-04-09 08:29:45', 'prettyjoraine', '', 1),
(38, NULL, 'Hold Me Down - Daniel Caesar\r\n', NULL, '2025-04-09 08:30:45', 'clarence', '', 1),
(39, NULL, 'looking for naman', NULL, '2025-04-09 09:11:45', 'clarence', '', 0),
(40, NULL, 'Looking for drummer', NULL, '2025-04-09 09:29:40', 'clarence', '', 0),
(41, NULL, 'cc', NULL, '2025-04-09 09:48:26', 'clarence', '', 0),
(42, NULL, 'instrument', NULL, '2025-04-09 09:54:30', 'clarence', '', 1),
(43, NULL, 'guitar look po ako haha', NULL, '2025-04-09 10:01:14', 'clarence', 'Guitarist', 0),
(44, NULL, 'ako naman po lf marunong mag piano bahahha', NULL, '2025-04-09 10:06:33', 'prettyjoraine', 'Pianist', 0),
(45, NULL, 'I\'m looking for you', NULL, '2025-04-09 11:09:43', 'clarence', '', 0),
(46, NULL, 'makikisali po, looking for my love of my life, ah wala?', NULL, '2025-04-09 11:13:45', 'jamesharden', '', 0),
(47, NULL, 'lovvvv', 'uploads/ale2.jpg', '2025-04-09 12:06:34', 'clarence', '', 0),
(48, NULL, 'whyy', NULL, '2025-04-09 12:25:07', 'clarence', '', 2),
(49, NULL, 'cla', 'uploads/2x2 Pictures_Clarence.png', '2025-04-09 12:34:08', 'clarence', '', 0),
(50, NULL, 'jo', 'uploads/ale2.jpg', '2025-04-09 12:34:25', 'clarence', '', 1),
(51, NULL, 'Helloooo', NULL, '2025-04-09 12:35:30', 'clarence', '', 1),
(52, NULL, 'DFD', 'uploads/DFD Level 1.png', '2025-04-09 14:00:47', 'clarence', 'Saxophonist', 1),
(53, NULL, 'Like this post\r\n', NULL, '2025-04-09 14:50:37', 'prettyjoraine', '', 2),
(54, NULL, 'CLAREMCE', NULL, '2025-04-10 11:27:28', 'prettyjoraine', '', 1),
(55, NULL, 'I am looking for someone who know how to play Guitar that is perfectly to be partner of my voice, anyone? Cabanatuan City are', 'uploads/ale.jpg', '2025-04-10 14:13:18', 'prettyjoraine', 'Guitarist', 1),
(56, NULL, 'This is post', NULL, '2025-04-11 03:57:23', 'clarence', '', 3),
(57, NULL, 'hi', NULL, '2025-04-15 09:40:16', 'prettyjoraine', '', 1),
(58, NULL, 'Lilayyy missu', NULL, '2025-04-15 10:46:33', 'lilaythedog', '', 0),
(59, NULL, 'The Amazing Spider-Man', NULL, '2025-04-15 14:25:32', 'peterspider', '', 0),
(61, NULL, 'elo\r\n', NULL, '2025-04-20 16:05:37', 'jamesharden', '', 1),
(62, NULL, 'lol\r\n', NULL, '2025-04-20 16:43:10', 'huawei', '', 0),
(63, NULL, 'hi', NULL, '2025-04-20 16:47:09', 'huaweinova7i', '', 3),
(64, NULL, 'hays', NULL, '2025-04-29 14:05:19', 'clarence', 'Drummer', 3),
(65, NULL, 'try post', NULL, '2025-04-30 04:03:38', 'clarence', 'Saxophonist', 2),
(66, NULL, 'Is there anyone here who knows how to play guitar?', NULL, '2025-04-30 08:21:43', 'clarence', 'Guitarist', 1),
(67, NULL, 'hola\r\n', NULL, '2025-05-02 09:51:49', 'clarencegonzales12', 'Violinist', 0),
(68, NULL, 'uy', NULL, '2025-05-02 09:58:04', 'joraine', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sendmessages`
--

CREATE TABLE `sendmessages` (
  `id` int(11) NOT NULL,
  `sender_username` varchar(255) NOT NULL,
  `receiver_username` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sendmessages`
--

INSERT INTO `sendmessages` (`id`, `sender_username`, `receiver_username`, `message`, `created_at`) VALUES
(1, 'clarence', 'Joraine Cantos', 'hello', '2025-04-21 06:51:31'),
(2, 'clarence', 'Joraine Cantos', 'hii', '2025-04-21 06:51:42'),
(3, 'clarence', 'Joraine Cantos', 'hhii', '2025-04-21 06:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `unique_id` int(200) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phonenumber` varchar(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(50) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `unique_id`, `firstname`, `lastname`, `email`, `phonenumber`, `username`, `password`, `profile_picture`, `status`) VALUES
(1, 0, 'Clarence', 'Gonzales', 'gclarence261@gmail.com', '', 'clarence', 'c71232a4713c128aa61f736302f5aeb3', 'uploads/clarence_1746861533.jpg', ''),
(3, 0, 'Joraine', 'Cantos', 'joraine@gmail.com', '', 'prettyjoraine', 'c54bc756860b9c08780ba54bfe6f10ca', 'uploads/prettyjoraine_1745999878.jpg', ''),
(5, 0, 'Lily', 'Dog', 'gclarence261@gmail.com', '', 'lily', 'b0204f6da6fc761293f4167dfa9808fa', NULL, ''),
(6, 0, 'James', 'Harden', 'gclarence261@gmail.com', '', 'jamesharden', '4f07a18254de88d3e4e4bd7a3996f915', 'uploads/jamesharden_1744197267.jpg', ''),
(10, 0, 'Lilay', 'Dog', 'joraine@gmail.com', '', 'lilaythedog', '7de42a81a94d5d0a6bfa3fb38e44b7cb', 'uploads/lilaythedog_1745671419.jpg', ''),
(11, 0, 'Peter', 'Parker', 'gclarence261@gmail.com', '', 'peterspider', '90ac60637bafbb46bf59c7657ad0430b', NULL, ''),
(12, 0, 'Huawei', 'Nova', 'joraine@gmail.com', '', 'huawei', '04f54071466112842059b4e2f0bf81e7', NULL, ''),
(13, 0, 'Huawei', 'Nova 7i', 'joraine@gmail.com', '', 'huaweinova7i', '0ab5dce5f1b69f1db50fd3b4dafc6925', NULL, ''),
(15, 0, 'Acer', 'Laptop', 'gclarence261@gmail.com', '', 'acerlaptop', '2a7b409292dfc7fea04952bb97211a19', NULL, ''),
(16, 0, 'sfsfsfsfsff', 'fdgdgd', 'dgsglsgs@gmail.com', '', 'clang', '4f2757c3476046173a377330098d4193', NULL, ''),
(17, 0, 'cla', 'gonzales', 'clarence@gmail.com', '', 'clarenceeee', '13ee668e3a29264c2b1ac54b6127eb26', NULL, ''),
(18, 0, 'Elsa', 'Ancheta', 'elsaancheta@gmail.com', '', 'elsa', 'd871e7f4fc56d7fab587b13f9a048254', NULL, ''),
(19, 1030605692, 'Joraine', 'Kj', 'jorainekj@gmail.com', '9638894808', 'joraine', '5ba9f08c4284c62431fa6e739e9eaf27', 'uploads/joraine_1746178774.jpg', 'Active now'),
(20, 1478419068, 'Clarence L.', 'Gonzales', 'gclarence261@gmail.com', '963889480', 'clarencegonzales12', 'fd7f34734552720496eab11df5484147', 'uploads/clarencegonzales12_1746179285.jpg', 'Active now'),
(21, 1476695897, 'Annaliza', 'Ancheta', 'annaliza@gmail.com', '9123456789', 'annaliza', 'ce2f064dafae113874759d1b296d9c5a', '0', 'Active now'),
(22, 1161078115, 'Clarenceee', 'Gonzalesss', 'gclarence261@gmail.com', '9123456789', 'clarencepogs', '63ad1de8f69dde474c41cd20383a77b9', '0', 'Active now');

-- --------------------------------------------------------

--
-- Table structure for table `userss`
--

CREATE TABLE `userss` (
  `user_id` int(11) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_cards`
--

CREATE TABLE `user_cards` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `owner_username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_messages`
--

CREATE TABLE `user_messages` (
  `id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_post_id` (`post_id`) USING BTREE;

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messagess`
--
ALTER TABLE `messagess`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sendmessages`
--
ALTER TABLE `sendmessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `userss`
--
ALTER TABLE `userss`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_cards`
--
ALTER TABLE `user_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_messages`
--
ALTER TABLE `user_messages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `messagess`
--
ALTER TABLE `messagess`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `sendmessages`
--
ALTER TABLE `sendmessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_cards`
--
ALTER TABLE `user_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_messages`
--
ALTER TABLE `user_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_username_posts` FOREIGN KEY (`username`) REFERENCES `users` (`username`),
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
