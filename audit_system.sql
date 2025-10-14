-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 05:08 AM
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
-- Database: `audit_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target_table` varchar(100) NOT NULL,
  `target_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `agent_lastname` varchar(100) NOT NULL,
  `agent_firsttname` varchar(100) NOT NULL,
  `birthday` date DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `team` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `agent_lastname`, `agent_firsttname`, `birthday`, `email`, `team`) VALUES
(1, 'Gonzaga', 'Alex', '1995-01-10', 'alex.gonzaga@yahoo.com', 'Sales'),
(2, 'Langot', 'Coco', '1992-09-18', 'coco.langot@gmail.com', 'Support'),
(3, 'Brozo', 'Nel Andrew', '2004-12-11', 'brozoandrewm@collectivesolution.com', 'Team Yey'),
(8, 'Puwitan', 'Daril', '2015-12-15', 'tanga@collectivesolution.com', 'Team Bravo'),
(10, 'De luna', 'Matt', '2004-12-11', 'mattdeluna@gmail.com', 'Team Alpha');

-- --------------------------------------------------------

--
-- Table structure for table `auditors2`
--

CREATE TABLE `auditors2` (
  `id` int(11) NOT NULL,
  `auditor_firstname` varchar(100) NOT NULL,
  `auditor_lasttname` varchar(100) NOT NULL,
  `birthday` date DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auditors2`
--

INSERT INTO `auditors2` (`id`, `auditor_firstname`, `auditor_lasttname`, `birthday`, `email`, `department`) VALUES
(1, 'Alyssa Marie', 'Maranan', '1985-06-15', 'john.smith@gmail.com', 'Quality Assurance'),
(2, 'Sitti Aisha', 'Alpad', '1985-06-15', 'sitti.alpad@gmail.com', 'Quality Assurance'),
(3, 'Nel Andrew', 'Brozo', '1221-12-12', 'brozoandrew@collectivesolutions.com', 'QA Department'),
(5, 'Sheldon Lee', 'Cooper', '1984-09-05', 'coopal@collectivesolutions.com', 'IT Support'),
(11, 'Daril', 'Cooper', '2005-05-05', 'coopal@collectivesolutions.com', 'IT Support'),
(12, 'Rejena', 'Lagarder', '2211-12-12', 'asdasdasdasd@gmail.com', 'IT Support'),
(13, 'Daril', 'Brozo', '1222-12-12', 'brozoandrew@collectivesolutions.com', 'Operations');

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
(1, 'Sitti', 'Nel Andrew Brozo', '2025-10-02', '19:24:00', 'Performance', 'Omsim', '2025-10-12 11:21:20'),
(4, 'Sitti', 'Alex Gonzaga', '2025-10-01', '05:00:00', 'Attendance', 'tanga tanga', '2025-10-12 11:26:42'),
(5, 'Darel', 'Nel Andrew Brozo', '2025-10-23', '20:00:00', 'Behavioral', 'I love you', '2025-10-12 11:41:12'),
(6, 'Darel', 'Alex Gonzaga', '2025-10-03', '19:45:00', 'Performance', 'asda', '2025-10-12 11:43:34'),
(7, 'Darel', 'Alex Gonzaga', '2025-10-03', '19:45:00', 'Performance', 'asda', '2025-10-12 11:44:24');

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
(1, 'Brozo', 'Nel Andrew', '2004-12-11', 'brozoandro@collectivesolutions.com', 'Operations', '2025-08-31 15:00:25');

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
(14, 'Sitti Aisha Alpad', 'Nel Andrew Brozo', 'Trainee', '2004-12-11', 'Week 50', '00:52:00', 'Bongbong Marcos', '12:12:12', NULL, '0993834929', '6546154456141', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'Yes', 'Yes', 'Bobo', '2025-08-31 13:48:50'),
(15, 'Alyssa Marie Maranan', 'Coco Langot', 'Regular', '1999-08-25', 'Week 34', '23:52:00', 'Bogart Dela Cruz', '08:08:08', NULL, '0993834929', '6546154456141', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'No', 'sorry', '2025-08-31 13:50:23'),
(17, 'Daril Brozo', 'Daril Puwitan', 'Trainee', '1211-12-12', 'Week 50', '05:13:00', 'Bongbong Marcos', '11:11:11', NULL, '0993834929', '6546154456141', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'ihh ang bangis', '2025-09-09 18:12:01'),
(18, 'Sitti Aisha Alpad', 'Coco Langot', 'Regular', '2025-10-09', 'Week 41', '15:37:00', 'Bongbong Marcos', '11:01:01', NULL, '0993834929', '6546154456141', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', '', '2025-10-12 07:35:12'),
(19, 'Daril Brozo', 'Matt De luna', 'Probationary', '2025-10-09', 'Week 41', '07:43:00', 'Bongbong Marcos', '11:11:11', NULL, '0993834929', '6546154456141', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Sorry', '2025-10-12 11:41:53'),
(20, 'Daril Cooper', 'Daril Puwitan', 'Probationary', '2025-10-16', 'Week 42', '07:59:00', 'Bongbong Marcos', '12:11:12', NULL, '0993834929', '6546154456141', 'N/A', 'No', 'Yes', 'No', 'No', 'Yes', 'Yes', 'No', 'No', 'Yes', 'Sorry po', '2025-10-12 11:58:36');

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
(1, 'Adrid', 'Jayvee', '1954-08-31', 'coupalbossing@collectivesolutions.com', 'Team Bravo', '2025-08-31 15:01:01'),
(3, 'Brozo', 'Andrew', '2025-10-24', 'coupalbossing1@collectivesolutions.com', 'Team Alpha', '2025-10-12 07:32:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','auditor','supervisor','data_analyst') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', '2025-07-21 16:15:59'),
(2, 'Jhay', '123456', 'supervisor', '2025-07-21 16:15:59'),
(17, 'Hana', '123456', 'admin', '2025-08-31 11:31:17'),
(18, 'Sitti', '123456', 'supervisor', '2025-08-31 11:50:18'),
(23, 'audit', 'audit123', 'auditor', '2025-09-09 11:10:16'),
(26, 'anna', '123', 'data_analyst', '2025-09-09 11:22:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `auditors2`
--
ALTER TABLE `auditors2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coaching_sessions`
--
ALTER TABLE `coaching_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_analysts`
--
ALTER TABLE `data_analysts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `data_reports`
--
ALTER TABLE `data_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `auditors2`
--
ALTER TABLE `auditors2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `coaching_sessions`
--
ALTER TABLE `coaching_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `data_analysts`
--
ALTER TABLE `data_analysts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_reports`
--
ALTER TABLE `data_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
