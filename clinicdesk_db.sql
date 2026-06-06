-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2026 at 08:14 PM
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
(1, 7, 1, '2026-05-01', '09:00:00', 'completed', 'Recurring headaches and general fatigue', 'Patient examined. Prescribed painkillers and rest.', '2026-04-28 05:00:00'),
(2, 8, 2, '2026-05-03', '10:00:00', 'completed', 'Chest pain and shortness of breath', 'ECG normal. Advised to reduce stress and monitor blood pressure.', '2026-04-30 06:00:00'),
(3, 9, 3, '2026-05-05', '09:30:00', 'completed', 'Child with fever and persistent cough', 'Bacterial throat infection. Antibiotic course prescribed.', '2026-05-02 07:00:00'),
(4, 10, 4, '2026-05-07', '11:00:00', 'completed', 'Sleep disorder and ongoing anxiety', 'Diagnosed with generalized anxiety disorder. Therapy started.', '2026-05-04 08:00:00'),
(5, 11, 5, '2026-05-10', '10:30:00', 'completed', 'Knee pain following sports injury', 'Partial cartilage tear. Cast applied for 3 weeks.', '2026-05-07 05:30:00'),
(6, 12, 1, '2026-05-12', '09:00:00', 'completed', 'Cold and fever', 'Seasonal viral infection. Rest and antivirals prescribed.', '2026-05-09 06:00:00'),
(7, 13, 2, '2026-05-14', '10:00:00', 'completed', 'High blood pressure', 'BP 150/95. Started Amlodipine.', '2026-05-11 07:00:00'),
(8, 14, 3, '2026-05-16', '09:30:00', 'completed', 'Child growth delay concern', 'Growth within normal range. Follow-up in 6 months.', '2026-05-13 06:30:00'),
(9, 15, 4, '2026-05-18', '11:00:00', 'completed', 'Depression and social withdrawal', 'Treatment plan set. Weekly therapy sessions scheduled.', '2026-05-15 08:00:00'),
(10, 16, 5, '2026-05-20', '10:30:00', 'completed', 'Chronic lower back pain', 'Lumbar disc herniation L4-L5. Physical therapy for one month.', '2026-05-17 07:00:00'),
(11, 7, 2, '2026-05-22', '10:00:00', 'confirmed', 'Routine cardiac check-up', NULL, '2026-05-19 05:00:00'),
(12, 8, 3, '2026-05-23', '09:30:00', 'confirmed', 'Child vaccinations', NULL, '2026-05-20 06:00:00'),
(13, 9, 1, '2026-05-25', '09:00:00', 'confirmed', 'Annual general health check', NULL, '2026-05-22 07:00:00'),
(14, 10, 5, '2026-05-27', '10:30:00', 'confirmed', 'Shoulder injury follow-up', NULL, '2026-05-24 05:30:00'),
(15, 11, 4, '2026-05-28', '11:00:00', 'confirmed', 'Mental health follow-up session', NULL, '2026-05-25 08:00:00'),
(16, 12, 2, '2026-05-15', '10:00:00', 'cancelled', 'Sudden chest pain', NULL, '2026-05-12 04:00:00'),
(17, 13, 1, '2026-05-17', '09:00:00', 'cancelled', 'Lab results review', NULL, '2026-05-14 05:00:00'),
(18, 14, 2, '2026-06-08', '10:00:00', 'pending', 'Routine heart check-up', NULL, '2026-06-01 06:00:00'),
(19, 15, 3, '2026-06-09', '09:30:00', 'pending', 'Pediatric consultation', NULL, '2026-06-01 07:00:00'),
(20, 16, 1, '2026-06-10', '09:00:00', 'pending', 'Migraine headache', NULL, '2026-06-02 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `specialization_id` int(10) UNSIGNED NOT NULL,
  `bio` text DEFAULT NULL,
  `consultation_fee` decimal(8,2) NOT NULL DEFAULT 0.00,
  `available_days` varchar(50) NOT NULL DEFAULT 'Sun,Mon,Tue,Wed,Thu',
  `photo` varchar(255) DEFAULT NULL,
  `years_experience` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `specialization_id`, `bio`, `consultation_fee`, `available_days`, `photo`, `years_experience`) VALUES
(1, 2, 1, 'Experienced general practitioner covering a wide range of common illnesses.', 50.00, 'Sun,Mon,Tue,Wed,Thu', NULL, 8),
(2, 3, 2, 'Specialist in cardiovascular diseases and heart health.', 80.00, 'Sun,Mon,Tue,Wed,Thu', NULL, 12),
(3, 4, 4, 'Pediatrician specializing in child care from newborns up to age 18.', 60.00, 'Sun,Mon,Tue,Wed', NULL, 10),
(4, 5, 6, 'Neurologist specializing in nervous system disorders and mental health.', 70.00, 'Mon,Tue,Wed,Thu', NULL, 9),
(5, 6, 5, 'Orthopedic surgeon specializing in sports injuries and joint replacement.', 90.00, 'Sun,Tue,Thu', NULL, 15);

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
(1, 1, 'Patient with recurring headaches and fatigue', 'Chronic tension headache', 'Ibuprofen 400mg – twice daily after meals for 5 days\nVitamin B12 1000mcg – once daily', 'Get adequate rest and stay hydrated. Reduce screen time.', NULL, '2026-05-01 07:00:00'),
(2, 2, 'Patient with chest pain and shortness of breath', 'Anxiety with mild hypertension', 'Amlodipine 5mg – once daily in the morning\nAspirin 81mg – once daily with food', 'Monitor blood pressure daily. Reduce salt and fat intake.', NULL, '2026-05-03 08:00:00'),
(3, 3, 'Child with high fever and severe cough', 'Acute bacterial throat infection', 'Amoxicillin 250mg – 3 times daily for 7 days\nParacetamol syrup 5ml – every 6 hours as needed', 'Complete the full antibiotic course even if symptoms improve.', NULL, '2026-05-05 07:30:00'),
(4, 4, 'Patient with anxiety and sleep disturbance', 'Generalized anxiety disorder', 'Sertraline 50mg – once daily in the morning\nMelatonin 3mg – 1 hour before bedtime', 'Weekly psychotherapy sessions. Avoid caffeine after noon.', NULL, '2026-05-07 09:00:00'),
(5, 5, 'Patient with sports-related knee injury', 'Partial medial meniscus tear', 'Diclofenac 50mg – twice daily after meals\nCalcium + Vitamin D3 – once daily', 'Wear cast for 3 weeks. Physical therapy 3 times per week.', NULL, '2026-05-10 08:30:00'),
(6, 6, 'Patient with cold and fever', 'Seasonal viral infection', 'Paracetamol 500mg – every 8 hours as needed\nZinc 10mg – once daily', 'Full rest. Plenty of fluids. Avoid work for 3 days.', NULL, '2026-05-12 07:00:00'),
(7, 7, 'Patient with high blood pressure', 'Stage 1 hypertension', 'Amlodipine 5mg – once daily in the morning\nLosartan 50mg – once daily in the evening', 'Record blood pressure daily. Follow a low-sodium diet.', NULL, '2026-05-14 08:00:00'),
(8, 8, 'Child growth monitoring visit', 'Normal growth with mild iron deficiency', 'Iron syrup 5ml – once daily before meals\nVitamin D drops 400IU – once daily', 'Follow-up in 6 months. Encourage iron-rich foods.', NULL, '2026-05-16 07:30:00'),
(9, 9, 'Patient with depression and social withdrawal', 'Mild to moderate depressive disorder', 'Fluoxetine 20mg – once daily in the morning\nOmega-3 1000mg – once daily with food', 'Weekly cognitive behavioral therapy sessions. Maintain social contact.', NULL, '2026-05-18 09:00:00'),
(10, 10, 'Patient with chronic lower back pain', 'Lumbar disc herniation L4-L5', 'Naproxen 500mg – twice daily after meals\nCyclobenzaprine 5mg – once at night', 'Physical therapy 3 times per week for one month. Avoid prolonged sitting.', NULL, '2026-05-20 08:30:00');

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
(1, 'General Practice'),
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
(1, 'System Admin', 'admin@clinic.com', '$2y$10$QKvga9mENMIHtepEmwj5xOOwGzmCpzhQhpdUgbV8NCfMzE3fqN1QK', 'admin', '0599000000', NULL, 1, '2026-06-03 09:14:14'),
(2, 'Dr. Ahmed Marwan', 'doctor@clinic.com', '$2y$10$JvJSzMzbCRguJbmbP654TOej5rSRtKU7WP2pLw8TZh8NKX3pQoQfm', 'doctor', '0599100001', NULL, 1, '2026-06-03 09:14:14'),
(3, 'Dr. Sara Khalidi', 'doctor1@clinic.com', '$2y$10$JvJSzMzbCRguJbmbP654TOej5rSRtKU7WP2pLw8TZh8NKX3pQoQfm', 'doctor', '0599100002', NULL, 1, '2026-06-03 09:14:14'),
(4, 'Dr. Mohammad Omari', 'doctor2@clinic.com', '$2y$10$JvJSzMzbCRguJbmbP654TOej5rSRtKU7WP2pLw8TZh8NKX3pQoQfm', 'doctor', '0599100003', NULL, 1, '2026-06-03 09:14:14'),
(5, 'Dr. Lina Haddad', 'doctor3@clinic.com', '$2y$10$JvJSzMzbCRguJbmbP654TOej5rSRtKU7WP2pLw8TZh8NKX3pQoQfm', 'doctor', '0599100004', NULL, 1, '2026-06-03 09:14:14'),
(6, 'Dr. Khaled Nabulsi', 'doctor4@clinic.com', '$2y$10$JvJSzMzbCRguJbmbP654TOej5rSRtKU7WP2pLw8TZh8NKX3pQoQfm', 'doctor', '0599100005', NULL, 1, '2026-06-03 09:14:14'),
(7, 'Abdul Rahman Nawati', 'patient@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200001', NULL, 1, '2026-06-03 09:14:14'),
(8, 'Ahmed Sheikh', 'patient1@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200002', NULL, 1, '2026-06-03 09:14:14'),
(9, 'Khaled Awad', 'patient2@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200003', NULL, 1, '2026-06-03 09:14:14'),
(10, 'Mona Salama', 'patient3@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200004', NULL, 1, '2026-06-03 09:14:14'),
(11, 'Reem Hamouri', 'patient4@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200005', NULL, 1, '2026-06-03 09:14:14'),
(12, 'Yousef Abdullah', 'patient5@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200006', NULL, 1, '2026-06-03 09:14:14'),
(13, 'Nour Qasim', 'patient6@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200007', NULL, 1, '2026-06-03 09:14:14'),
(14, 'Tamer Zidan', 'patient7@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200008', NULL, 1, '2026-06-03 09:14:14'),
(15, 'Heba Jamal', 'patient8@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200009', NULL, 1, '2026-06-03 09:14:14'),
(16, 'Salma Ibrahim', 'patient9@clinic.com', '$2y$10$aYXwLOqkX3awQlnRa5viiepCXaREQacBkUcSEZ1ii6ewMkMp4k6Ia', 'patient', '0599200010', NULL, 1, '2026-06-03 09:14:14');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
