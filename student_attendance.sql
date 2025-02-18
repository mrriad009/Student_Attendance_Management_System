-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2025 at 07:16 PM
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
-- Database: `student_attendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `total_classes` int(11) NOT NULL,
  `present` int(11) NOT NULL,
  `absent` int(11) NOT NULL,
  `percentage` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `total_classes`, `present`, `absent`, `percentage`) VALUES
(1, 5555, 10, 5, 0, 0),
(2, 997, 5, 5, 0, 0),
(3, 898, 7, 6, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `department`) VALUES
(898, 'RIAD', 'mrx@gmail.com', 'Mechanical Engineering'),
(997, 'MAHTAB', 'mdmahamudulislamriad20000@gmail.com', 'Electrical Engineering'),
(5555, 'FDGFDG', 'daa.attrack01@gmail.com', 'Computer Science'),
(5556, 'John Doe', 'john.doe@example.com', 'CSE'),
(5557, 'Jane Smith', 'jane.smith@example.com', 'EEE'),
(5558, 'Alice Johnson', 'alice.johnson@example.com', 'BBA'),
(5559, 'Bob Brown', 'bob.brown@example.com', 'CSE'),
(5560, 'Carol White', 'carol.white@example.com', 'EEE'),
(5561, 'David Wilson', 'david.wilson@example.com', 'BBA'),
(5562, 'Eve Black', 'eve.black@example.com', 'CSE'),
(5563, 'Frank Clark', 'frank.clark@example.com', 'EEE'),
(5564, 'Grace Hall', 'grace.hall@example.com', 'BBA'),
(5565, 'Henry Allen', 'henry.allen@example.com', 'CSE'),
(5566, 'Isabel Scott', 'isabel.scott@example.com', 'EEE'),
(5567, 'Jack Young', 'jack.young@example.com', 'BBA'),
(5568, 'Karen King', 'karen.king@example.com', 'CSE'),
(5569, 'Louis Adams', 'louis.adams@example.com', 'EEE'),
(5570, 'Monica Baker', 'monica.baker@example.com', 'BBA'),
(5571, 'Nick Carter', 'nick.carter@example.com', 'CSE'),
(5572, 'Olivia Davis', 'olivia.davis@example.com', 'EEE'),
(5573, 'Paul Evans', 'paul.evans@example.com', 'BBA'),
(5574, 'Quincy Frank', 'quincy.frank@example.com', 'CSE'),
(5575, 'Rachel Green', 'rachel.green@example.com', 'EEE'),
(5576, 'Steve Hill', 'steve.hill@example.com', 'BBA'),
(5577, 'Tina Irvine', 'tina.irvine@example.com', 'CSE'),
(5578, 'Ursula Jones', 'ursula.jones@example.com', 'EEE'),
(5579, 'Victor Klein', 'victor.klein@example.com', 'BBA'),
(5580, 'Wendy Lee', 'wendy.lee@example.com', 'CSE'),
(5581, 'Xavier Moore', 'xavier.moore@example.com', 'EEE'),
(5582, 'Yvonne Nelson', 'yvonne.nelson@example.com', 'BBA'),
(5583, 'Zachary Ochoa', 'zachary.ochoa@example.com', 'CSE'),
(5584, 'Amelia Perry', 'amelia.perry@example.com', 'EEE'),
(5585, 'Bruce Quinn', 'bruce.quinn@example.com', 'BBA'),
(5586, 'Cynthia Richards', 'cynthia.richards@example.com', 'CSE'),
(5587, 'Derek Stevens', 'derek.stevens@example.com', 'EEE'),
(5588, 'Elena Torres', 'elena.torres@example.com', 'BBA'),
(5589, 'Felix Upton', 'felix.upton@example.com', 'CSE'),
(5590, 'Gina Vincent', 'gina.vincent@example.com', 'EEE'),
(5591, 'Howard Wells', 'howard.wells@example.com', 'BBA'),
(5592, 'Ingrid Xavier', 'ingrid.xavier@example.com', 'CSE'),
(5593, 'Jasper York', 'jasper.york@example.com', 'EEE'),
(5594, 'Kara Zelman', 'kara.zelman@example.com', 'BBA'),
(5595, 'Liam Anderson', 'liam.anderson@example.com', 'CSE'),
(5596, 'Mia Brown', 'mia.brown@example.com', 'EEE'),
(5597, 'Nolan Clark', 'nolan.clark@example.com', 'BBA'),
(5598, 'Oprah Dean', 'oprah.dean@example.com', 'CSE'),
(5599, 'Pete East', 'pete.east@example.com', 'EEE'),
(5600, 'Quinn Foster', 'quinn.foster@example.com', 'BBA'),
(5601, 'Ruby Gold', 'ruby.gold@example.com', 'CSE'),
(5602, 'Sid Hart', 'sid.hart@example.com', 'EEE'),
(5603, 'Tiffany Ivy', 'tiffany.ivy@example.com', 'BBA'),
(5604, 'Uma Jain', 'uma.jain@example.com', 'CSE'),
(5605, 'Vince Kite', 'vince.kite@example.com', 'EEE');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5606;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
