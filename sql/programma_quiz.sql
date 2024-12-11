-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 12:38 PM
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
-- Database: `programma_quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `classe`
--

CREATE TABLE `classe` (
  `codice` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domanda`
--

CREATE TABLE `domanda` (
  `id` int(11) NOT NULL,
  `testo` varchar(2000) NOT NULL,
  `tipo` enum('multipla','aperta') DEFAULT NULL,
  `id_test` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `domanda`
--

INSERT INTO `domanda` (`id`, `testo`, `tipo`, `id_test`) VALUES
(1, 'Quale comando SQL viene utilizzato per recuperare dati da un database?', 'multipla', 1),
(2, 'Qual è lo scopo di una chiave primaria in un database relazionale?', 'multipla', 1),
(3, 'Quale comando SQL viene utilizzato per aggiungere una nuova colonna a una tabella?', 'multipla', 1),
(4, 'Qual è lo scopo della chiave primaria in una tabella?', 'aperta', 1),
(5, 'Qual è il comando per inserire un nuovo record in SQL?', 'aperta', 1),
(6, 'Cosa fa il comando DELETE in SQL?', 'aperta', 1),
(8, 'come ti chiami?', 'aperta', 4),
(9, 'Scegli il numero piu grande', 'multipla', 4);

-- --------------------------------------------------------

--
-- Table structure for table `risposta`
--

CREATE TABLE `risposta` (
  `id` int(11) NOT NULL,
  `testo` varchar(2000) DEFAULT NULL,
  `corretta` tinyint(1) NOT NULL,
  `id_domanda` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risposta`
--

INSERT INTO `risposta` (`id`, `testo`, `corretta`, `id_domanda`) VALUES
(25, 'INSERT', 0, 1),
(26, 'SELECT', 1, 1),
(27, 'UPDATE', 0, 1),
(28, 'DELETE', 0, 1),
(29, 'Connettere due tabelle', 0, 2),
(30, 'Ordinare i dati in una tabella', 0, 2),
(31, 'Identificare univocamente ogni riga di una tabella', 1, 2),
(32, 'Limitare l\'accesso ai dati', 0, 2),
(33, 'ALTER TABLE', 1, 3),
(34, 'UPDATE', 0, 3),
(35, 'CREATE TABLE', 0, 3),
(36, 'ADD COLUMN', 0, 3),
(37, '1', 0, 9),
(38, '5', 1, 9),
(39, '2', 0, 9),
(40, '3', 0, 9);

-- --------------------------------------------------------

--
-- Table structure for table `risposte_date`
--

CREATE TABLE `risposte_date` (
  `id_test` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_domanda` int(11) NOT NULL,
  `tipologia_domanda` enum('multipla','aperta') NOT NULL,
  `risposta_data` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `risultati`
--

CREATE TABLE `risultati` (
  `id` int(11) NOT NULL,
  `id_studente` int(11) NOT NULL,
  `id_test` int(11) NOT NULL,
  `punteggio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ruolo`
--

CREATE TABLE `ruolo` (
  `id` int(11) NOT NULL,
  `ruolo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruolo`
--

INSERT INTO `ruolo` (`id`, `ruolo`) VALUES
(1, 'admin'),
(3, 'docente'),
(2, 'studente');

-- --------------------------------------------------------

--
-- Table structure for table `ruolo_users`
--

CREATE TABLE `ruolo_users` (
  `id_ruolo` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `ruolo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruolo_users`
--

INSERT INTO `ruolo_users` (`id_ruolo`, `id_user`, `ruolo`) VALUES
(1, 2, 'admin'),
(3, 3, 'docente'),
(2, 1, 'studente');

-- --------------------------------------------------------

--
-- Table structure for table `sessione_test`
--

CREATE TABLE `sessione_test` (
  `id` int(11) NOT NULL,
  `id_test` int(11) NOT NULL,
  `codice_classe` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `titolo`, `descrizione`) VALUES
(1, 'Test di Informatica', 'quiz sui database'),
(4, 'Test matematica', 'numeri');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`) VALUES
(1, 'mario', '$2y$10$IsCaTcJ.d.uzJyMCIoNnOeQqDfIxTseeeDtV.J0bFsIVqmFYqB12O', 'Mario', 'Rossi', NULL),
(2, 'admin', '$2y$10$IsCaTcJ.d.uzJyMCIoNnOeQqDfIxTseeeDtV.J0bFsIVqmFYqB12O', NULL, NULL, NULL),
(3, 'luigi', '$2y$10$svasPPTUr7JunweMkDlF2.NsMIHQwEnxycjBkSz0rPGaAjo.hxSf6', 'Luigi', 'Biangi', 'luigi@test.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classe`
--
ALTER TABLE `classe`
  ADD PRIMARY KEY (`codice`);

--
-- Indexes for table `domanda`
--
ALTER TABLE `domanda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_test` (`id_test`);

--
-- Indexes for table `risposta`
--
ALTER TABLE `risposta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_domanda` (`id_domanda`);

--
-- Indexes for table `risposte_date`
--
ALTER TABLE `risposte_date`
  ADD PRIMARY KEY (`id_test`,`id_user`,`id_domanda`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_domanda` (`id_domanda`);

--
-- Indexes for table `risultati`
--
ALTER TABLE `risultati`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_studente` (`id_studente`),
  ADD KEY `id_test` (`id_test`);

--
-- Indexes for table `ruolo`
--
ALTER TABLE `ruolo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ruolo` (`ruolo`);

--
-- Indexes for table `ruolo_users`
--
ALTER TABLE `ruolo_users`
  ADD PRIMARY KEY (`id_ruolo`,`id_user`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `fk_ruolo` (`ruolo`);

--
-- Indexes for table `sessione_test`
--
ALTER TABLE `sessione_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_test` (`id_test`),
  ADD KEY `codice_classe` (`codice_classe`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
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
-- AUTO_INCREMENT for table `domanda`
--
ALTER TABLE `domanda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `risposta`
--
ALTER TABLE `risposta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `risultati`
--
ALTER TABLE `risultati`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ruolo`
--
ALTER TABLE `ruolo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessione_test`
--
ALTER TABLE `sessione_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `domanda`
--
ALTER TABLE `domanda`
  ADD CONSTRAINT `domanda_ibfk_1` FOREIGN KEY (`id_test`) REFERENCES `test` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risposta`
--
ALTER TABLE `risposta`
  ADD CONSTRAINT `fk_risposta_domanda` FOREIGN KEY (`id_domanda`) REFERENCES `domanda` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risposte_date`
--
ALTER TABLE `risposte_date`
  ADD CONSTRAINT `risposte_date_ibfk_1` FOREIGN KEY (`id_test`) REFERENCES `test` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `risposte_date_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `risposte_date_ibfk_3` FOREIGN KEY (`id_domanda`) REFERENCES `domanda` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risultati`
--
ALTER TABLE `risultati`
  ADD CONSTRAINT `risultati_ibfk_1` FOREIGN KEY (`id_studente`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `risultati_ibfk_2` FOREIGN KEY (`id_test`) REFERENCES `test` (`id`);

--
-- Constraints for table `ruolo_users`
--
ALTER TABLE `ruolo_users`
  ADD CONSTRAINT `fk_ruolo` FOREIGN KEY (`ruolo`) REFERENCES `ruolo` (`ruolo`),
  ADD CONSTRAINT `ruolo_users_ibfk_1` FOREIGN KEY (`id_ruolo`) REFERENCES `ruolo` (`id`),
  ADD CONSTRAINT `ruolo_users_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Constraints for table `sessione_test`
--
ALTER TABLE `sessione_test`
  ADD CONSTRAINT `sessione_test_ibfk_1` FOREIGN KEY (`id_test`) REFERENCES `test` (`id`),
  ADD CONSTRAINT `sessione_test_ibfk_2` FOREIGN KEY (`codice_classe`) REFERENCES `classe` (`codice`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
