-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2025 at 06:25 PM
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
-- Database: `medicare`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `phone`, `password`) VALUES
(1, '12345678', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `Appointment_Id` int(11) NOT NULL,
  `Patient_Id` int(11) NOT NULL,
  `Patient_Name` varchar(255) NOT NULL,
  `Patient_Mobile_No` varchar(20) NOT NULL,
  `Doctor_Name` varchar(255) NOT NULL,
  `Doctor_License_Number` varchar(255) DEFAULT NULL,
  `Hospital_Name` varchar(255) NOT NULL,
  `Hospital_License` varchar(255) NOT NULL,
  `Appointment_Date` date NOT NULL,
  `Appointment_Time` time NOT NULL,
  `Serial_Number` int(11) DEFAULT NULL,
  `Is_Accepted` enum('accepted','rejected','pending') DEFAULT 'pending',
  `Calling_Time` time DEFAULT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  `Manager_Name` varchar(255) DEFAULT NULL,
  `Manager_Mobile_No` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`Appointment_Id`, `Patient_Id`, `Patient_Name`, `Patient_Mobile_No`, `Doctor_Name`, `Doctor_License_Number`, `Hospital_Name`, `Hospital_License`, `Appointment_Date`, `Appointment_Time`, `Serial_Number`, `Is_Accepted`, `Calling_Time`, `Created_At`, `Manager_Name`, `Manager_Mobile_No`) VALUES
(2, 3, 'sourav', '852', 'Dr a', '123', 'dc', '22', '2025-01-06', '12:00:00', 2, 'accepted', '14:01:00', '2025-01-05 20:37:48', 'abc', '22'),
(8, 9, 'trs', '987', 'Dr a', '123', 'dc', '22', '2025-01-08', '09:30:00', NULL, 'pending', NULL, '2025-01-06 10:26:02', 'abc', '22'),
(9, 9, 'trs', '987', 'Dr Dalim', '852258', 'Popular', '12345', '2025-01-13', '10:00:00', 15, 'accepted', '11:00:00', '2025-01-06 10:29:19', 'Zarsis', '1234'),
(10, 8, 'r', '2', 'Dr a', '123', 'dc', '22', '2025-01-20', '01:00:00', NULL, 'pending', NULL, '2025-01-13 16:50:53', 'abc', '22');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `d_id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `License_No` varchar(255) NOT NULL,
  `Dr_Categories` enum('Gynecologist','Cardiologist','Nephrologist','Dermatologist','Neurologist','Pediatrician','Orthopedic Surgeon','Gastroenterologist','Endocrinologist','Oncologist') NOT NULL,
  `Expertise` varchar(255) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Details` text DEFAULT NULL,
  `Is_Accepted` enum('accepted','rejected','pending') DEFAULT 'pending',
  `Mobile_No` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`d_id`, `Name`, `License_No`, `Dr_Categories`, `Expertise`, `Password`, `Email`, `Details`, `Is_Accepted`, `Mobile_No`) VALUES
(1, 'Dr Faisal', '123123123', 'Gynecologist', NULL, '$2y$10$IyfFjC8Pn.dO6tkjxExyteSxy/iDKeYa.OUN6LqRTJ59ONAdczsfu', 'f@gmail.com', NULL, 'accepted', '123456'),
(2, 'Fahim', '54321', 'Pediatrician', NULL, '$2y$10$rY.68ecQFVWwMwK71yZP6Os1eBNgpzFdnmf6QCDDKCDVdR.v0PN82', 'abc@de', NULL, 'accepted', '54321'),
(3, 'Musa Kabir', '159753', 'Gynecologist', 'Surgeon', '$2y$10$jDsz1sVBKKNlKOv9MAr9peTIuFeqv.38qTBZhFITDXhZtQz1SOyeu', 'nuhash.ai911@gmail.com', 'Best Dr Awarded', 'accepted', ''),
(4, 'q', '6', 'Pediatrician', 'Surgeon', '$2y$10$kM19K920Tzx7Iojk2PRs4u2Cnb08W7FqNkg7leQkyUr8wfB6KM5RS', 'f@f', 'eeeee', 'pending', '6');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_chat`
--

CREATE TABLE `doctor_chat` (
  `id` int(11) NOT NULL,
  `conversation_id` varchar(255) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_chat`
--

INSERT INTO `doctor_chat` (`id`, `conversation_id`, `sender_id`, `receiver_id`, `message`, `timestamp`) VALUES
(1, 'doctor_2_doctor_1', 2, 1, 'kemon acho?', '2025-01-25 17:04:46');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `Hospital_Name` varchar(255) NOT NULL,
  `Hospital_License` varchar(255) DEFAULT NULL,
  `Manager_Name` varchar(255) DEFAULT NULL,
  `Manager_Mobile_Number` varchar(20) DEFAULT NULL,
  `Doctor_Name` varchar(255) DEFAULT NULL,
  `Doctor_License_Number` varchar(255) DEFAULT NULL,
  `Location` enum('Dhaka','Rajshahi','Chittagong','Other') NOT NULL,
  `Dr_Categories` enum('Gynecologist','Cardiologist','Nephrologist','Dermatologist','Neurologist','Pediatrician','Orthopedic Surgeon','Gastroenterologist','Endocrinologist','Oncologist') NOT NULL,
  `Visit_Day_Start` set('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `Visit_Time_Start` time NOT NULL,
  `Visit_Time_End` time NOT NULL,
  `Visit_Money` int(11) NOT NULL,
  `Is_Accepted` enum('accepted','rejected','pending') DEFAULT 'pending',
  `Hospital_Mobile_Number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `Hospital_Name`, `Hospital_License`, `Manager_Name`, `Manager_Mobile_Number`, `Doctor_Name`, `Doctor_License_Number`, `Location`, `Dr_Categories`, `Visit_Day_Start`, `Visit_Time_Start`, `Visit_Time_End`, `Visit_Money`, `Is_Accepted`, `Hospital_Mobile_Number`) VALUES
(6, 'dc', '22', 'abc', '22', 'Dr a', '123', 'Dhaka', 'Cardiologist', 'Monday,Wednesday', '01:00:00', '13:00:00', 50000, 'accepted', '22'),
(7, 'Popular', '12345', 'Zarsis', '1234', 'Dr Dalim', '852258', 'Rajshahi', 'Pediatrician', 'Monday,Tuesday,Thursday,Sunday', '10:00:00', '17:00:00', 998, 'pending', '1234'),
(8, 'Popular', '12345', 'Zarsis', '1234', 'Dr Karim', '78787878', 'Dhaka', 'Cardiologist', 'Monday,Tuesday,Wednesday', '10:00:00', '16:00:00', 1000, 'pending', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_reviews`
--

CREATE TABLE `hospital_reviews` (
  `Review_Id` int(11) NOT NULL,
  `Patient_Id` int(11) NOT NULL,
  `Hospital_Id` int(11) NOT NULL,
  `Review_Text` text DEFAULT NULL,
  `Image` longblob DEFAULT NULL,
  `Is_Accepted` enum('accepted','rejected','pending') DEFAULT 'pending',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  `Hospital_License` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_reviews`
--

INSERT INTO `hospital_reviews` (`Review_Id`, `Patient_Id`, `Hospital_Id`, `Review_Text`, `Image`, `Is_Accepted`, `Created_At`, `Hospital_License`) VALUES
(13, 8, 6, 'faltu', NULL, 'accepted', '2025-01-20 06:12:20', '22'),
(14, 8, 7, 'baad', NULL, 'accepted', '2025-01-20 13:29:20', '12345'),
(18, 8, 7, 'not so good,very bad', NULL, 'pending', '2025-01-20 13:45:17', '12345'),
(45, 8, 7, 'heeellloo', NULL, 'accepted', '2025-01-20 16:38:40', '');

-- --------------------------------------------------------

--
-- Table structure for table `managers`
--

CREATE TABLE `managers` (
  `m_id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Hospital_Name` varchar(255) DEFAULT NULL,
  `Hospital_License_Number` varchar(255) NOT NULL,
  `Is_Accepted` enum('accepted','rejected','pending') DEFAULT 'pending',
  `Mobile_No` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `managers`
--

INSERT INTO `managers` (`m_id`, `Name`, `Hospital_Name`, `Hospital_License_Number`, `Is_Accepted`, `Mobile_No`, `Password`) VALUES
(4, 'Noman', 'Faraz Hospital', '0123456789', 'pending', '789987', '$2y$10$eOWZRfI/gd3jF0KBXP6jbuHk9ONbDQD1cvwYIsNozZ9qhuUtqfVW2'),
(2, 'Zarsis', 'Popular', '12345', 'pending', '1234', '$2y$10$apKrwYISkBc1IgZTckEyc.zCs/A.cTE8HvmDrVCy99q/J9Fh0vgfy'),
(3, 'abc', 'dc', '22', 'pending', '22', '$2y$10$K4YzVON9F1A/xs2e38pjYOZcYkz3R9QeYoMvk.o7Vv2HcNF3xanbi');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `p_id` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Mobile_No` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`p_id`, `Name`, `Location`, `Mobile_No`, `Password`) VALUES
(3, 'sourav', NULL, '852', '$2y$10$X94.d1MDd69X4Wq79dCUrOZk7p6psEbF9aFH57yp1MPAjryg8MNnK'),
(8, 'r', NULL, '2', '$2y$10$uC1lzfMoGiZhDxUe7KQZEeWtyvl4vBVKTBnuhLnZl0vfN7xjVqKEq'),
(9, 'trs', NULL, '987', '$2y$10$vDxFwqBOn3T/ifAzoc1GceLlgyRhDBixWVGWWR51flAR5gTLEdJBe'),
(10, 'Abdullah Al Mamun', NULL, '0123456789', '$2y$10$cRi5al3ztWygqOjH2eX/aeKvLyg6eVIk87JWkwJo.o/9ISUYAJJXi'),
(11, 'Selim', NULL, '5', '$2y$10$JZyEt3zkrVS1wqM8986tFez3VfVeVzbQXIm1UQLYarJKIOjgTw/Na'),
(12, 'eee', NULL, '1', '$2y$10$rEJFoxGoaXhzIalhB3PaaOcd/fV.Z.N5v4MbhashhXWiReRpLc1WK');

-- --------------------------------------------------------

--
-- Table structure for table `patients_history`
--

CREATE TABLE `patients_history` (
  `history_id` int(11) NOT NULL,
  `Patient_Mobile_No` varchar(20) NOT NULL,
  `Weight` float DEFAULT NULL,
  `Age` int(11) DEFAULT NULL,
  `Blood_Group` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') DEFAULT NULL,
  `Symptoms` text DEFAULT NULL,
  `Diagnosis` text DEFAULT NULL,
  `Medical_Test` text DEFAULT NULL,
  `Medicine` text DEFAULT NULL,
  `Times` set('Morning','Noon','Night','After Meal','Before Meal') DEFAULT NULL,
  `Dosage` varchar(255) DEFAULT NULL,
  `Duration` varchar(50) DEFAULT NULL,
  `Next_Visit` varchar(50) DEFAULT NULL,
  `Doctor_Name` varchar(255) NOT NULL,
  `Doctor_Category` varchar(255) NOT NULL,
  `Form_Fill_Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients_history`
--

INSERT INTO `patients_history` (`history_id`, `Patient_Mobile_No`, `Weight`, `Age`, `Blood_Group`, `Symptoms`, `Diagnosis`, `Medical_Test`, `Medicine`, `Times`, `Dosage`, `Duration`, `Next_Visit`, `Doctor_Name`, `Doctor_Category`, `Form_Fill_Date`) VALUES
(3, '2', 65, 25, 'B+', 'Fever', '104\'', 'CBC', 'Napa', 'Night', '1', '5', '7', 'Fahim', 'Pediatrician', '2025-01-12 17:12:18'),
(4, '2', 65, 25, 'B+', 'Fever', '104\'', 'CBC', 'Fiva', 'After Meal', '1', '2', NULL, 'Fahim', 'Pediatrician', '2025-01-12 17:12:18'),
(5, '2', 60, 26, 'B+', 'abc', 'def', 'X-ray', 'xyz', 'Morning', '10', '7', '15', 'Dr Faisal', 'Gynecologist', '2025-01-13 14:43:57'),
(6, '2', 60, 26, 'B+', 'abc', 'def', 'X-ray', 'wer', 'Night', '1', '1', NULL, 'Dr Faisal', 'Gynecologist', '2025-01-13 14:43:57');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `Patient_Mobile_No` varchar(20) DEFAULT NULL,
  `Diagnosis` text DEFAULT NULL,
  `Disease` text DEFAULT NULL,
  `Next_Appointment` datetime DEFAULT NULL,
  `Is_Accepted` enum('accepted','rejected','pending') DEFAULT 'pending',
  `Calling_Time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`Appointment_Id`),
  ADD KEY `fk_hospital_license` (`Hospital_License`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`d_id`),
  ADD UNIQUE KEY `License_No` (`License_No`),
  ADD UNIQUE KEY `Mobile_No` (`Mobile_No`);

--
-- Indexes for table `doctor_chat`
--
ALTER TABLE `doctor_chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_manager_mobile` (`Manager_Mobile_Number`);

--
-- Indexes for table `hospital_reviews`
--
ALTER TABLE `hospital_reviews`
  ADD PRIMARY KEY (`Review_Id`),
  ADD KEY `fk_patient_id` (`Patient_Id`),
  ADD KEY `fk_hospital_id` (`Hospital_Id`);

--
-- Indexes for table `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`Hospital_License_Number`),
  ADD UNIQUE KEY `Mobile_No` (`Mobile_No`),
  ADD UNIQUE KEY `Mobile_No_2` (`Mobile_No`),
  ADD UNIQUE KEY `m_id` (`m_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `Mobile_No` (`Mobile_No`);

--
-- Indexes for table `patients_history`
--
ALTER TABLE `patients_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `Patient_Mobile_No` (`Patient_Mobile_No`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD KEY `Patient_Mobile_No` (`Patient_Mobile_No`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `Appointment_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `d_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `doctor_chat`
--
ALTER TABLE `doctor_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `hospital_reviews`
--
ALTER TABLE `hospital_reviews`
  MODIFY `Review_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `managers`
--
ALTER TABLE `managers`
  MODIFY `m_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `patients_history`
--
ALTER TABLE `patients_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_hospital_license` FOREIGN KEY (`Hospital_License`) REFERENCES `managers` (`Hospital_License_Number`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_chat`
--
ALTER TABLE `doctor_chat`
  ADD CONSTRAINT `doctor_chat_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `doctors` (`d_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_chat_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `doctors` (`d_id`) ON DELETE CASCADE;

--
-- Constraints for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD CONSTRAINT `fk_manager_mobile` FOREIGN KEY (`Manager_Mobile_Number`) REFERENCES `managers` (`Mobile_No`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_reviews`
--
ALTER TABLE `hospital_reviews`
  ADD CONSTRAINT `fk_hospital_review_hospital` FOREIGN KEY (`Hospital_Id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hospital_review_patient` FOREIGN KEY (`Patient_Id`) REFERENCES `patients` (`p_id`) ON DELETE CASCADE;

--
-- Constraints for table `patients_history`
--
ALTER TABLE `patients_history`
  ADD CONSTRAINT `patients_history_ibfk_1` FOREIGN KEY (`Patient_Mobile_No`) REFERENCES `patients` (`Mobile_No`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`Patient_Mobile_No`) REFERENCES `patients` (`Mobile_No`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
