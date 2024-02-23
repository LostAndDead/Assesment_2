-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2024 at 12:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assesment_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `uuid` varchar(36) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `status` int(11) NOT NULL,
  `owner` varchar(36) NOT NULL,
  `completion_date` date NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` int(11) NOT NULL DEFAULT 2,
  `deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`uuid`, `title`, `content`, `status`, `owner`, `completion_date`, `last_updated`, `priority`, `deleted`) VALUES
('05204791-cfee-11ee-bb31-0a002700000a', 'new task', 'a new task', 2, '331a3957-c4d0-11ee-a6a8-0a002700000c', '2024-02-17', '2024-02-20 12:45:41', 3, 1),
('331a3957-c4d0-11ee-a6a8-0a002700000c', 'finish things', 'do some stuff', 2, '331a3957-c4d0-11ee-a6a8-0a002700000c', '2024-02-29', '2024-02-06 11:09:32', 1, 0),
('4ab7ecf4-cfda-11ee-bb31-0a002700000a', 'Bobs task', 'this is a task for bob, bob should do this task, bob is lazy', 1, '0affb41e-ca54-11ee-a6a8-0a002700000c', '2024-03-01', '2024-02-20 10:24:29', 3, 0),
('d7d882b2-c4dd-11ee-a6a8-0a002700000c', 'Test task', 'bbbbbbb', 3, '331a3957-c4d0-11ee-a6a8-0a002700000c', '2024-02-16', '2024-02-06 10:52:38', 2, 0),
('f19bb27f-cfd2-11ee-bb31-0a002700000a', 'Task 3', 'this is task 3 it has a big content that goes on for a while beans beans aaaaaaa I wanna keep going, i need to come up with words but idk what words to do, this should be enough i hope, ok maybe some more', 2, '331a3957-c4d0-11ee-a6a8-0a002700000c', '2024-02-23', '2024-02-20 09:31:53', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uuid` varchar(36) NOT NULL,
  `username` text NOT NULL,
  `email` text NOT NULL,
  `password_hash` text NOT NULL,
  `password_reset` tinyint(1) NOT NULL DEFAULT 1,
  `permission_level` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `session_uuid` varchar(36) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uuid`, `username`, `email`, `password_hash`, `password_reset`, `permission_level`, `last_updated`, `active`, `session_uuid`) VALUES
('0affb41e-ca54-11ee-a6a8-0a002700000c', 'bob', 'bob@bob.com', '$2y$10$ivb7hhaz5/AY//JZAZeQeupQ1sNDbW0b5OUr0lxutZmgfPET1Ho/6', 0, 1, '2024-02-13 09:40:50', 1, '-1'),
('12425f1f-ca54-11ee-a6a8-0a002700000c', 'dead', 'dead@dead.com', '$2y$10$3kN7msZxiux5AvrSSFT/8.wMH8QvSRP//igPdhQdA8pv5tOtpGXcW', 0, 0, '2024-02-13 09:41:02', 0, 'UUID()'),
('331a3957-c4d0-11ee-a6a8-0a002700000c', 'admin', 'admin@admin.com', '$2y$10$lVY9rq7zCD57gNazCWHRH.K00z7H8ssRu5yWFVDKZKTwC6cDfFzl6', 0, 2, '2024-02-06 09:14:58', 1, 'ce6c5824-d241-11ee-bb31-0a002700000a'),
('fdb23656-cfec-11ee-bb31-0a002700000a', 'test', 'test@test.com', '$2y$10$haBn9nslUEfQX7L3z7idVeSEEwD8V1SoSkDmuuSzGpvm9tvOSao1O', 0, 0, '2024-02-20 12:38:19', 1, 'UUID()');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`uuid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uuid`),
  ADD UNIQUE KEY `username` (`username`) USING HASH,
  ADD UNIQUE KEY `email` (`email`) USING HASH;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
