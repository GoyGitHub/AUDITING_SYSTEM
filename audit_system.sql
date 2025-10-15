-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2025 at 10:38 AM
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
-- Database: `audit_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents2`
--

CREATE TABLE `agents2` (
  `id` int(11) NOT NULL,
  `agent_firstname` varchar(100) NOT NULL,
  `agent_lastname` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `team` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agents2`
--

INSERT INTO `agents2` (`id`, `agent_firstname`, `agent_lastname`, `birthday`, `email`, `team`) VALUES
(1, 'Joy', 'Bilmunte', '2025-10-10', 'joybilmunte@gmail.com', 'Team Bravo'),
(2, 'BongBong', 'Marcos', '2025-10-24', 'bongbongmarcos@gmail.com', 'Team Charlie'),
(3, 'Alex', 'Gonzaga', '2025-10-05', 'alexgonsaga@gmail.com', 'Team Alpha'),
(4, 'Alex', 'Belmonte', '2025-10-14', 'joybilmunte@gmail.com', 'Team Alpha'),
(5, 'Alex', 'Belmonte', '2025-10-14', 'joybilmunte@gmail.com', 'Team Alpha'),
(6, 'Alex', 'Belmonte', '2025-10-14', 'joybilmunte@gmail.com', 'Team Alpha');

-- --------------------------------------------------------

--
-- Table structure for table `auditors`
--

CREATE TABLE `auditors` (
  `id` int(11) NOT NULL,
  `auditor_firstname` varchar(100) NOT NULL,
  `auditor_lastname` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auditors`
--

INSERT INTO `auditors` (`id`, `auditor_firstname`, `auditor_lastname`, `birthday`, `email`, `department`) VALUES
(1, 'Sarah', 'Discaya', '2025-10-18', 'sarahdisakaya@gmail.com', 'IT Support'),
(2, 'Matt', 'De Luna', '2025-10-20', 'mattdelona@gmail.com', 'QA Department');

-- --------------------------------------------------------

--
-- Table structure for table `auditors2`
--

CREATE TABLE `auditors2` (
  `id` int(11) NOT NULL,
  `auditor_firstname` varchar(100) NOT NULL,
  `auditor_lastname` varchar(100) NOT NULL,
  `birthday` date DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auditors2`
--

INSERT INTO `auditors2` (`id`, `auditor_firstname`, `auditor_lastname`, `birthday`, `email`, `department`) VALUES
(0, 'Sarah', 'Duterte', '2025-10-04', 'sarahdutirti@gmail.com', 'Operations'),
(1, 'Alyssa Marie', 'Maranan', '1985-06-15', 'john.smith@gmail.com', 'Quality Assurance'),
(2, 'Sitti Aisha', 'Alpad', '1985-06-15', 'sitti.alpad@gmail.com', 'Quality Assurance');

-- --------------------------------------------------------

--
-- Table structure for table `coaching_sessions`
--

CREATE TABLE `coaching_sessions` (
  `id` int(11) NOT NULL,
  `coach` varchar(100) NOT NULL,
  `agent` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `type` varchar(50) NOT NULL,
  `notes` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coaching_sessions`
--

INSERT INTO `coaching_sessions` (`id`, `coach`, `agent`, `date`, `time`, `type`, `notes`, `created_at`) VALUES
(0, 'BBM', 'Alex Gonzaga', '2025-10-15', '22:49:00', 'Behavioral', 'Tanga e, turuan mo nga', '2025-10-14 13:50:43'),
(0, 'Sarah Duterte', 'Alex Gonzaga', '2025-10-14', '09:54:00', 'Attendance', 'Di pumapasok, napaka tamad puta', '2025-10-14 13:51:12'),
(0, 'BBM', 'Alex Gonzaga', '2025-10-07', '11:52:00', 'Behavioral', 'asdasdasdas', '2025-10-14 15:50:47');

-- --------------------------------------------------------

--
-- Table structure for table `data_analysts`
--

CREATE TABLE `data_analysts` (
  `id` int(11) NOT NULL,
  `data_analyst_lastname` varchar(100) NOT NULL,
  `data_analyst_firstname` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `department` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_analysts`
--

INSERT INTO `data_analysts` (`id`, `data_analyst_lastname`, `data_analyst_firstname`, `birthday`, `email`, `department`, `created_at`) VALUES
(0, 'Duterte', 'Sarah', '2025-10-08', 'sarahduterte@gmail.com', 'QA Department', '2025-10-14 12:34:59'),
(0, 'Condeno', 'James Rhyan', '2025-10-11', 'jamecondeno@gmail.com', 'IT Support', '2025-10-15 04:30:56');

-- --------------------------------------------------------

--
-- Table structure for table `data_reports`
--

CREATE TABLE `data_reports` (
  `id` int(11) NOT NULL,
  `reviewer_name` varchar(255) NOT NULL,
  `agent_name` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `week` varchar(50) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `caller_name` varchar(100) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `queue` varchar(100) DEFAULT NULL,
  `mdn` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `q1` varchar(5) DEFAULT NULL,
  `q2` varchar(5) DEFAULT NULL,
  `q3` varchar(5) DEFAULT NULL,
  `q4` varchar(5) DEFAULT NULL,
  `q5` varchar(5) DEFAULT NULL,
  `q6` varchar(5) DEFAULT NULL,
  `q7` varchar(5) DEFAULT NULL,
  `q8` varchar(5) DEFAULT NULL,
  `q9` varchar(5) DEFAULT NULL,
  `q10` varchar(5) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_reports`
--

INSERT INTO `data_reports` (`id`, `reviewer_name`, `agent_name`, `status`, `date`, `week`, `time`, `caller_name`, `duration`, `queue`, `mdn`, `account_number`, `q1`, `q2`, `q3`, `q4`, `q5`, `q6`, `q7`, `q8`, `q9`, `q10`, `comment`, `created_at`) VALUES
(4, 'Sitti Aisha Alpad', 'Coco Langot', 'Others', '2025-10-01', 'Week 40', '20:53:00', 'sadasd', '12:12:12', NULL, '323414143', '43431431431431', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Sorry', '2025-10-14 12:57:45'),
(5, 'Sitti Aisha Alpad', 'Alex Gonzaga', 'Trainee', '2025-10-02', 'Week 40', '01:45:00', 'Sarah Duterte', '05:05:35', NULL, '09319342379', '12345678900', 'Yes', 'No', 'Yes', 'No', 'Yes', 'No', 'Yes', 'No', 'Yes', 'Yes', 'Sorry', '2025-10-14 14:41:48'),
(6, 'Alyssa Marie Maranan', 'Alex Gonzaga', 'Probationary', '2025-10-16', 'Week 42', '22:46:00', 'Sarah Duterte', '11:17:07', NULL, '09319342379', '12345678900', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', '', '2025-10-14 14:44:38'),
(7, 'Alyssa Marie Maranan', 'Alex Gonzaga', 'Trainee', '2025-11-10', 'Week 46', '12:50:00', 'Leni', '01:30:20', NULL, '09665783451', '1123554885', 'Yes', 'Yes', 'No', 'Yes', 'Yes', 'Yes', 'No', 'No', 'Yes', 'Yes', 'great job!', '2025-10-14 14:50:03'),
(8, 'Alyssa Marie Maranan', 'Coco Langot', 'Regular', '2025-10-02', 'Week 40', '14:50:00', 'Kiko', '01:10:10', NULL, '09785643211', '2324576', 'Yes', 'Yes', 'Yes', 'No', 'No', 'Yes', 'Yes', 'No', 'Yes', 'Yes', 'well done!', '2025-10-14 14:51:59'),
(9, 'Alyssa Marie Maranan', 'Alex Gonzaga', 'Probationary', '2025-12-01', 'Week 49', '10:30:00', 'Donny', '01:50:30', NULL, '09123567841', '1232445545', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'No', 'Yes', 'Yes', 'Yes', 'Great, you\'ve passed!', '2025-10-14 14:53:41'),
(10, 'Sitti Aisha Alpad', 'Coco Langot', 'Regular', '2025-10-30', 'Week 44', '08:40:00', 'Melani', '02:10:10', NULL, '09678543211', '1243154315', 'Yes', 'Yes', 'No', 'No', 'No', 'Yes', 'Yes', 'No', 'Yes', 'Yes', 'Good job!', '2025-10-14 14:55:13'),
(11, 'Sarah Duterte', 'Joy Bilmunte', 'Probationary', '2025-10-16', 'Week 42', '06:27:00', 'Sarah Duterte', '11:34:34', NULL, '54654755454', '43431431431431', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Sorry', '2025-10-15 08:25:51');

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL,
  `supervisor_lastname` varchar(100) NOT NULL,
  `supervisor_firstname` varchar(100) NOT NULL,
  `birthday` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `team` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisors`
--

INSERT INTO `supervisors` (`id`, `supervisor_lastname`, `supervisor_firstname`, `birthday`, `email`, `team`, `created_at`) VALUES
(1, 'Adrid', 'Jayvee', '1954-08-31', 'coupalbossing@collectivesolutions.com', 'Team Bravo', '2025-08-31 07:01:01'),
(0, 'Marcos', 'BongBong', '2025-10-07', 'bongbongmarcos@gmail.com', 'Team Charlie', '2025-10-14 11:53:59'),
(0, 'Marcos', 'BongBong', '2025-10-16', 'bongbongmarcos@gmail.com', 'Team Charlie', '2025-10-14 12:33:42'),
(0, 'Marcos', 'BongBong', '2025-10-03', 'bongbongmarcos@gmail.com', 'Team Charlie', '2025-10-15 04:29:44'),
(0, 'Duterte', 'Sarah', '2025-10-04', 'sarahduterte@gmail.com', 'Team Bravo', '2025-10-15 04:30:05'),
(0, 'Morenencia', 'Kian', '2025-10-27', 'kiankopalogs@gmail.com', 'Team Bravo', '2025-10-15 04:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','auditor','supervisor','data_analyst') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', '2025-07-31 17:15:31'),
(2, 'jhay', 'password', 'auditor', '2025-07-31 17:15:31'),
(3, 'alyssa', '12345', 'admin', '2025-08-20 05:43:34'),
(5, 'anna', '123', 'data_analyst', '2025-09-09 23:32:49'),
(6, '123', '123', 'data_analyst', '2025-10-14 06:30:46'),
(7, 'Andrew Brozo', '123', 'admin', '2025-10-14 07:51:35'),
(9, 'andro', '123', 'auditor', '2025-10-15 02:13:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents2`
--
ALTER TABLE `agents2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auditors`
--
ALTER TABLE `auditors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auditors2`
--
ALTER TABLE `auditors2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_reports`
--
ALTER TABLE `data_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents2`
--
ALTER TABLE `agents2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `auditors`
--
ALTER TABLE `auditors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_reports`
--
ALTER TABLE `data_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
