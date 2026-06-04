-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 09:29 PM
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
-- Database: `clinicdesk_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `appt_date` date NOT NULL,
  `appt_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `reason` varchar(255) DEFAULT NULL,
  `doctor_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appt_date`, `appt_time`, `status`, `reason`, `doctor_notes`, `created_at`) VALUES
(1, 6, 1, '2026-05-21', '09:00:00', 'confirmed', 'Chest pain and shortness of breath', NULL, '2026-05-21 14:47:37'),
(2, 7, 1, '2026-05-21', '11:00:00', 'confirmed', 'High blood pressure', NULL, '2026-05-21 14:47:37'),
(3, 8, 1, '2026-05-21', '13:00:00', 'completed', 'Routine checkup', NULL, '2026-05-21 14:47:37'),
(4, 9, 2, '2026-05-21', '10:00:00', 'completed', 'Child has high fever', NULL, '2026-05-21 14:47:37'),
(5, 10, 2, '2026-05-21', '14:00:00', 'pending', 'Routine vaccination', NULL, '2026-05-21 14:47:37'),
(7, 6, 4, '2026-05-22', '11:00:00', 'cancelled', 'Severe migraine', NULL, '2026-05-21 14:47:37'),
(8, 7, 2, '2026-05-22', '13:30:00', 'confirmed', 'Child with persistent cough', NULL, '2026-05-21 14:47:37'),
(9, 8, 1, '2026-05-23', '10:00:00', 'cancelled', 'Cardiology follow-up', NULL, '2026-05-21 14:47:37'),
(10, 9, 3, '2026-05-23', '14:00:00', 'completed', 'Hand fracture', NULL, '2026-05-21 14:47:37'),
(11, 10, 4, '2026-05-24', '12:00:00', 'pending', 'Numbness in limbs', NULL, '2026-05-21 14:47:37'),
(13, 6, 1, '2026-05-16', '09:00:00', 'pending', 'Routine checkup', NULL, '2026-05-21 14:47:37'),
(14, 7, 3, '2026-05-14', '11:00:00', 'completed', 'Back pain', NULL, '2026-05-21 14:47:37'),
(15, 8, 2, '2026-05-11', '10:00:00', 'confirmed', 'Vaccinations', NULL, '2026-05-21 14:47:37'),
(16, 9, 4, '2026-05-18', '14:00:00', 'completed', 'Severe headache', NULL, '2026-05-21 14:47:37'),
(17, 10, 1, '2026-05-06', '12:00:00', 'completed', 'Blood pressure check', NULL, '2026-05-21 14:47:37'),
(19, 6, 2, '2026-05-26', '10:00:00', 'completed', 'Patient requested cancellation', 'Reschedule next week', '2026-05-21 14:47:37'),
(20, 8, 4, '2026-05-20', '15:00:00', 'cancelled', 'Emergency - patient could not come', 'Call to reschedule', '2026-05-21 14:47:37'),
(21, 7, 3, '2026-05-28', '10:30:00', 'completed', 'sc', NULL, '2026-05-27 06:34:16'),
(22, 9, 8, '2026-06-04', '14:30:00', 'pending', 'NULL', NULL, '2026-06-02 20:53:58');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `specialization_id` int(10) UNSIGNED NOT NULL,
  `years_experience` int(11) DEFAULT 0,
  `consultation_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `available_days` varchar(50) NOT NULL DEFAULT 'Sun,Mon,Tue,Wed,Thu',
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `specialization_id`, `years_experience`, `consultation_fee`, `available_days`, `bio`, `created_at`, `photo`) VALUES
(1, 2, 4, 8, 800.00, 'Sun,Mon,Tue,Wed,Thu', 'This is an updated bio from automated test - 2026-05-28', '2026-05-21 14:47:28', NULL),
(2, 3, 4, 5, 180.00, 'Sun,Tue,Thu', 'Pediatrics Specialist - 5 years experience', '2026-05-21 14:47:28', NULL),
(3, 4, 5, 12, 300.00, 'Sun,Mon,Wed,Thu', 'Orthopedic Surgeon - 12 years experience', '2026-05-21 14:47:28', NULL),
(4, 5, 6, 7, 280.00, 'Mon,Wed,Thu', 'Neurology Specialist - 7 years experience', '2026-05-21 14:47:28', NULL),
(8, 21, 1, 5, 500.00, 'Sun,Mon,Tue,Wed,Thu', NULL, '2026-05-28 17:04:12', NULL),
(13, 27, 4, 30, 12.00, 'Tue,Wed', 'The Best Doctor in Gaza Strip', '2026-05-31 11:13:43', NULL),
(17, 32, 3, 4, 2.00, 'Mon', '', '2026-06-03 07:56:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `patient_condition` text DEFAULT NULL,
  `diagnosis` text NOT NULL,
  `medications` text NOT NULL,
  `notes` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `appointment_id`, `patient_condition`, `diagnosis`, `medications`, `notes`, `file_path`, `created_at`) VALUES
(5, 13, NULL, 'Hypertension', 'Lisinopril 10mg once daily\nAmlodipine 5mg once daily', 'Follow up in 2 weeks. Monitor blood pressure daily.', NULL, '2026-05-21 20:11:41'),
(6, 14, NULL, 'Common Cold', 'Paracetamol 500mg every 6 hours\nRest and plenty of fluids', 'Symptoms should improve in 3-5 days', NULL, '2026-05-21 20:11:41'),
(7, 15, NULL, 'Lower Back Pain', 'Ibuprofen 400mg three times daily after meals\nHeat therapy', 'Avoid heavy lifting. Physiotherapy recommended.', NULL, '2026-05-21 20:11:41'),
(11, 4, 'No Food for 5 hourse after medicane', 'cccc', 'Actamoal', 'Rest for 3 days', NULL, '2026-06-03 06:54:17');

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

CREATE TABLE `specializations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`id`, `name`) VALUES
(2, 'Cardiology'),
(3, 'Dermatology'),
(8, 'ENT'),
(11, 'Explore Test Specialization'),
(1, 'General Practice'),
(10, 'Gynecology'),
(6, 'Neurology'),
(7, 'Ophthalmology'),
(5, 'Orthopedics'),
(4, 'Pediatrics'),
(9, 'Psychiatry');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','patient') NOT NULL DEFAULT 'patient',
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `avatar`, `is_active`, `created_at`) VALUES
(1, 'System Admin', 'admin@clinic.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0500000000', NULL, 1, '2026-05-21 14:47:19'),
(2, 'Dr. Ahmed Mahmoud', 'doctor1@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'doctor', '0599999799', NULL, 1, '2026-05-21 14:47:19'),
(3, 'Dr. Sarah Khalid', 'doctor2@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'doctor', '0502222222', NULL, 1, '2026-05-21 14:47:19'),
(4, 'Dr. Jana Saleh', 'doctor3@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'doctor', '0503333333', NULL, 1, '2026-05-21 14:47:19'),
(5, 'Dr. Nora Alqahtani', 'doctor4@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'doctor', '0504444444', NULL, 1, '2026-05-21 14:47:19'),
(6, 'Rami osama', 'patient1@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'patient', '0551111171', NULL, 1, '2026-05-21 14:47:19'),
(7, 'Fatima Ali', 'patient2@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'patient', '0552222223', NULL, 1, '2026-05-21 14:47:19'),
(8, 'Mohammed Saeed', 'patient3@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'patient', '0553333333', NULL, 1, '2026-05-21 14:47:19'),
(9, 'Nora Ahmed', 'patient4@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'patient', '0554444444', NULL, 1, '2026-05-21 14:47:19'),
(10, 'Khalid Ibrahim', 'patient5@clinic.com', '$2y$10$wF5fmPT/uxO1Xf0eYgaa9OWa8KQ8nrWQQjviAtLTDbAi.l.9eDa5S', 'patient', '0555555555', NULL, 1, '2026-05-21 14:47:19'),
(12, 'Abd Al-Rhman Al Nawati', 'kingabedalrhmn110@gmail.com', '$2y$10$Pacs6F9Snfu65ukB2lnWxeZsZ35bLm4A0Qzqr20EJmvIfek24dBvK', 'doctor', '0595227272', NULL, 0, '2026-05-21 19:53:54'),
(19, 'Test Patient', 'testpatient@example.com', '$2y$10$UvGQu0pZdbRoug0/Cfg4nOxSOfUyRNvXeO7lKMhb/2sdA9fUvY6zi', 'patient', '', NULL, 1, '2026-05-28 16:47:15'),
(20, 'Explore Test User', 'explore-test@clinic.com', '$2y$10$yLhR2zsMlN2AwEoJLVj8IOPXbn4Mp1e0nnb56n5sLwKGAw5Fc9MJq', 'patient', '1234567890', NULL, 1, '2026-05-28 17:03:41'),
(21, 'Dr. Test Jasmine', 'test_jasmine@clinic.com', '$2y$10$4Er9V7hOttB8WyKna0hVSeWsCwPWRKi6eeY53xbame9/vAH0E5t06', 'doctor', '', NULL, 1, '2026-05-28 17:04:12'),
(22, 'Explore Test Doctor', 'test-doctor@clinic.com', '$2y$10$0iJem4pP5AxS7y3oaNaxV.VJCz3CO1y84peFXahHK893.oTSnb4bG', 'doctor', '', NULL, 1, '2026-05-28 17:06:59'),
(23, 'Ahmed Saqllah', 'ahmed2@gmial.com', '$2y$10$r.pjhu7U/D7vNq.pwxaNoOrOzfVW0nXztRONYMhqjUo8FYyFyTgpq', 'patient', '', NULL, 1, '2026-05-31 10:58:16'),
(24, 'Dr.Fadil Naim', 'fadil@gmail.com', '$2y$10$MgTgUJTHhBkcNgMeqU4PQe/.TUfWkZINI9P7Ae1pBrw2WQil4oZmG', 'doctor', '', NULL, 1, '2026-05-31 10:59:06'),
(25, 'Dr.Fadil Naim', 'fa22dil@gmail.com', '$2y$10$tJtkhsagmuHrj1gzTPnsXeSe/6fz0D5uYzLEIOIWRsf5rAQNPHsh2', 'doctor', '', NULL, 1, '2026-05-31 11:03:56'),
(26, 'Dr.Osama Saleh', 'osama11@gmail.com', '$2y$10$eBZOrz1xLSF9ReO0QAgaG.JE3R7/QPDmUtUDXFYfMvGehcAm1K88a', 'doctor', '', NULL, 1, '2026-05-31 11:10:22'),
(27, 'Dr.Osama Saleh', 'osamaAlshami22@gmail.com', '$2y$10$9estzt91.MCz5j.0cv6dLOjZvHp5o6T0vyBGSMFxeFcwspxMJbiUu', 'doctor', '0000000000', NULL, 1, '2026-05-31 11:13:43'),
(28, 'Assad Salem', 'assadf22@gmail.com', '$2y$10$Jc6kSyLVV2YO6yF4.BEYs.rfuHcKKsUoyEgOt9vfUL.3o43XJyBkC', 'patient', '3123232321', NULL, 1, '2026-05-31 12:01:32'),
(29, 'Dr.Osama Saleh', 'osama123211@gmail.com', '$2y$10$9SypdPx4OFH1QVqzmAKPVupQ.jvGGdteMRqJF2l/NAUQHCSCjVYxy', 'doctor', '', NULL, 1, '2026-05-31 20:42:03'),
(30, 'sacascsc', 'dawmdwmd@gmail.com', '$2y$10$7n2cWKeG8q.uADscK3J6eejeDV1BkODhbF1Qr3XIjPVQ8sZulpBqm', 'doctor', '0594142412424', NULL, 1, '2026-05-31 20:44:06'),
(32, 'Mazen Saqallah', 'aa@gmail.com', '$2y$10$AD8F.6VBkrmfoQte9FDDPOgCbM/61kE4KUdeMvDGn4Z0j2y.yewnS', 'doctor', '0000000000', NULL, 1, '2026-06-03 07:56:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_double_booking` (`doctor_id`,`appt_date`,`appt_time`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `specialization_id` (`specialization_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctors_ibfk_2` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
