-- phpMyAdmin SQL Dump
-- TAItter - Social Media Platform Database
-- Database: `taitter`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `taitter`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `username`, `password`, `bio`, `date_of_birth`, `created_at`) VALUES
(1, 'Autsku', 'User', 'autsku@taitter.com', 'autsku', '$2y$10$abcdefghijklmnopqrstuv', 'New to TAItter! Excited to be here 🚀', '2000-01-15', '2025-01-10 10:00:00'),
(2, 'Demo', 'User', 'demo@taitter.com', 'demo_user', '$2y$10$abcdefghijklmnopqrstuv', 'Tech enthusiast and AI lover', '1998-05-20', '2025-01-12 14:30:00'),
(3, 'TAItter', 'Assistant', 'assistant@taitter.com', 'taitter_ai', '$2y$10$abcdefghijklmnopqrstuv', 'Official TAItter AI Assistant', '2025-01-01', '2025-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` varchar(144) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`post_id`),
  KEY `fk_post_user` (`user_id`),
  CONSTRAINT `fk_post_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 'Hello! I\'m new to TAItter 🚀 I hope to help this community. #awesome', '2025-01-15 10:00:00'),
(2, 2, 'Just tried the new AI content suggestions feature – it\'s amazing how it understands exactly what I\'m interested in! 🔥 #TAItter #AI', '2025-01-15 11:30:00'),
(3, 3, 'Welcome to TAItter! 🚀 Our AI helps you discover relevant content, connect with like-minded people. #welcome', '2025-01-15 09:00:00'),
(4, 3, 'Here\'s how the new trending dashboard looks 👇 #features #update', '2025-01-16 12:00:00'),
(5, 1, 'Thanks @taitter_ai for the warm welcome! #grateful', '2025-01-15 10:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `hashtags`
--

DROP TABLE IF EXISTS `hashtags`;

CREATE TABLE `hashtags` (
  `hashtag_id` int(11) NOT NULL AUTO_INCREMENT,
  `hashtag_name` varchar(50) NOT NULL,
  PRIMARY KEY (`hashtag_id`),
  UNIQUE KEY `hashtag_name` (`hashtag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hashtags`
--

INSERT INTO `hashtags` (`hashtag_id`, `hashtag_name`) VALUES
(1, 'awesome'),
(2, 'TAItter'),
(3, 'AI'),
(4, 'welcome'),
(5, 'features'),
(6, 'update'),
(7, 'grateful');

-- --------------------------------------------------------

--
-- Table structure for table `post_hashtags`
--

DROP TABLE IF EXISTS `post_hashtags`;

CREATE TABLE `post_hashtags` (
  `post_id` int(11) NOT NULL,
  `hashtag_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`, `hashtag_id`),
  KEY `fk_ph_hashtag` (`hashtag_id`),
  CONSTRAINT `fk_ph_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ph_hashtag` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtags` (`hashtag_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `post_hashtags`
--

INSERT INTO `post_hashtags` (`post_id`, `hashtag_id`) VALUES
(1, 1),
(2, 2),
(2, 3),
(3, 4),
(4, 5),
(4, 6),
(5, 7);

-- --------------------------------------------------------

--
-- Table structure for table `mentions`
--

DROP TABLE IF EXISTS `mentions`;

CREATE TABLE `mentions` (
  `mention_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `mentioned_user_id` int(11) NOT NULL,
  PRIMARY KEY (`mention_id`),
  KEY `fk_mention_post` (`post_id`),
  KEY `fk_mention_user` (`mentioned_user_id`),
  CONSTRAINT `fk_mention_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mention_user` FOREIGN KEY (`mentioned_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mentions`
--

INSERT INTO `mentions` (`mention_id`, `post_id`, `mentioned_user_id`) VALUES
(1, 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `followed_hashtags`
--

DROP TABLE IF EXISTS `followed_hashtags`;

CREATE TABLE `followed_hashtags` (
  `user_id` int(11) NOT NULL,
  `hashtag_id` int(11) NOT NULL,
  `followed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`, `hashtag_id`),
  KEY `fk_fh_hashtag` (`hashtag_id`),
  CONSTRAINT `fk_fh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fh_hashtag` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtags` (`hashtag_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `followed_hashtags`
--

INSERT INTO `followed_hashtags` (`user_id`, `hashtag_id`, `followed_at`) VALUES
(1, 2, '2025-01-15 10:00:00'),
(1, 3, '2025-01-15 10:00:00'),
(2, 1, '2025-01-15 11:00:00'),
(2, 2, '2025-01-15 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `followed_users`
--

DROP TABLE IF EXISTS `followed_users`;

CREATE TABLE `followed_users` (
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `followed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`follower_id`, `followed_id`),
  KEY `fk_fu_followed` (`followed_id`),
  CONSTRAINT `fk_fu_follower` FOREIGN KEY (`follower_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fu_followed` FOREIGN KEY (`followed_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `followed_users`
--

INSERT INTO `followed_users` (`follower_id`, `followed_id`, `followed_at`) VALUES
(1, 3, '2025-01-15 10:00:00'),
(2, 3, '2025-01-15 11:00:00'),
(1, 2, '2025-01-15 12:00:00');

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `hashtags`
  MODIFY `hashtag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `mentions`
  MODIFY `mention_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;