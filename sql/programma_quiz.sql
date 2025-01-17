-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2025 at 09:02 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `anno_inizio` year(4) NOT NULL,
  `anno_fine` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classe`
--

INSERT INTO `classe` (`id`, `nome`, `anno_inizio`, `anno_fine`) VALUES
(1, '5AII', '2024', '2025'),
(2, '4AII', '2024', '2025');

-- --------------------------------------------------------

--
-- Table structure for table `docente_classe`
--

CREATE TABLE `docente_classe` (
  `id_docente` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL
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
(9, 'Scegli il numero piu grande', 'multipla', 4),
(14, 'When did last time Messi win the world cup?', 'aperta', 9),
(15, 'How many goals did Mbappe do in the 2022 world cup final?', 'multipla', 9),
(16, 'How many world cup does Argentina has?', 'aperta', 9),
(17, 'Which country has most world cup? And how many?', 'aperta', 9),
(18, 'Which country defeated Portugal in the 2022 world cup quarter final?', 'multipla', 9),
(19, 'come ti chiami?', 'aperta', 10),
(20, 'ciao albero', 'multipla', 10),
(27, 'una domanda strana', 'aperta', 10),
(61, 'conta', 'multipla', 10);

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
(40, '3', 0, 9),
(41, '1', 0, 15),
(42, '4', 0, 15),
(43, '2', 0, 15),
(44, '3', 1, 15),
(45, 'Brazil', 0, 18),
(46, 'Morocco', 1, 18),
(47, 'Croatia', 0, 18),
(48, 'Argentina', 0, 18),
(477, 'lool', 0, 20),
(478, 'lol', 0, 20),
(479, 'ala', 0, 20),
(480, 'lal', 1, 20),
(481, '123', 1, 61),
(482, '12', 0, 61),
(483, '23132', 0, 61),
(484, '34', 0, 61);

-- --------------------------------------------------------

--
-- Table structure for table `risposte_date`
--

CREATE TABLE `risposte_date` (
  `id` int(11) NOT NULL,
  `id_studente` int(11) NOT NULL,
  `id_sessione` int(11) NOT NULL,
  `id_domanda` int(11) NOT NULL,
  `tipologia_domanda` enum('multipla','aperta') NOT NULL,
  `risposta_data` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risposte_date`
--

INSERT INTO `risposte_date` (`id`, `id_studente`, `id_sessione`, `id_domanda`, `tipologia_domanda`, `risposta_data`) VALUES
(37, 1, 14, 8, 'aperta', 'Roman'),
(38, 1, 14, 9, 'multipla', '2'),
(39, 1, 10, 1, 'multipla', '2'),
(40, 1, 10, 2, 'multipla', '3'),
(41, 1, 10, 3, 'multipla', '1'),
(42, 1, 10, 4, 'aperta', 'boh'),
(43, 1, 10, 5, 'aperta', 'INSERT'),
(44, 1, 10, 6, 'aperta', 'delete');

-- --------------------------------------------------------

--
-- Table structure for table `risultati`
--

CREATE TABLE `risultati` (
  `id` int(11) NOT NULL,
  `id_studente` int(11) NOT NULL,
  `id_sessione` int(11) NOT NULL,
  `punteggio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `risultati`
--

INSERT INTO `risultati` (`id`, `id_studente`, `id_sessione`, `punteggio`) VALUES
(18, 1, 14, 1),
(19, 1, 10, 3);

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
(3, 10, 'docente'),
(2, 1, 'studente');

-- --------------------------------------------------------

--
-- Table structure for table `sessione_test`
--

CREATE TABLE `sessione_test` (
  `id` int(11) NOT NULL,
  `id_test` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `data_inizio` datetime NOT NULL,
  `data_fine` datetime NOT NULL,
  `stato` enum('completato','in corso','programmato') NOT NULL DEFAULT 'programmato',
  `creato_da` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessione_test`
--

INSERT INTO `sessione_test` (`id`, `id_test`, `nome`, `id_classe`, `data_inizio`, `data_fine`, `stato`, `creato_da`) VALUES
(10, 1, 'Verifica 1', 1, '2025-01-02 22:20:00', '2025-01-29 22:20:00', 'in corso', 3),
(11, 4, 'Matematica', 1, '2025-01-15 22:21:00', '2025-01-30 22:21:00', 'programmato', 3),
(14, 4, 'Matematica 2', 1, '2025-01-04 22:23:00', '2025-02-02 22:23:00', 'in corso', 3),
(15, 1, 'Verifica 2', 1, '2025-01-08 23:09:00', '2025-01-28 23:10:00', 'in corso', 3),
(16, 9, 'test 2', 1, '2025-01-01 08:43:00', '2025-01-11 08:43:00', 'completato', 3);

-- --------------------------------------------------------

--
-- Table structure for table `studente_classe`
--

CREATE TABLE `studente_classe` (
  `id_studente` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studente_classe`
--

INSERT INTO `studente_classe` (`id_studente`, `id_classe`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `titolo` varchar(255) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `creato_da` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `titolo`, `descrizione`, `creato_da`) VALUES
(1, 'Test di Informatica', 'quiz sui database', 3),
(4, 'Test matematica', 'numeri', 3),
(9, 'English final test 2024 world cup', 'Good luck for the test', 3),
(10, 'test modifiche', 'modifica', 3);

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
(1, 'mario', '$2y$10$qjmd5gOWhllP3aznOrabBOvY0OWGgFQIKk3oPqMvdkcwLy.AT20IS', 'Mario', 'Rossi', 'mario.rossi@gmail.it'),
(2, 'admin', '$2y$10$IsCaTcJ.d.uzJyMCIoNnOeQqDfIxTseeeDtV.J0bFsIVqmFYqB12O', 'Admin', 'Admin', ''),
(3, 'luigi', '$2y$10$svasPPTUr7JunweMkDlF2.NsMIHQwEnxycjBkSz0rPGaAjo.hxSf6', 'Luigi', 'Biangi', 'luigi@test.com'),
(10, 'angelo', '$2y$10$IjsLk0AfvRCwDlte7ijehObN9NIJrK7y7UMIwWSpwBoDcIbtWjCAG', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classe`
--
ALTER TABLE `classe`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `docente_classe`
--
ALTER TABLE `docente_classe`
  ADD PRIMARY KEY (`id_docente`,`id_classe`),
  ADD KEY `docente_classe_ibfk_2` (`id_classe`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_studente` (`id_studente`),
  ADD KEY `id_domanda` (`id_domanda`),
  ADD KEY `fk_risposte_date_sessione` (`id_sessione`);

--
-- Indexes for table `risultati`
--
ALTER TABLE `risultati`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_studente` (`id_studente`),
  ADD KEY `id_sessione` (`id_sessione`) USING BTREE;

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
  ADD KEY `fk_ruolo` (`ruolo`),
  ADD KEY `ruolo_users_ibfk_2` (`id_user`);

--
-- Indexes for table `sessione_test`
--
ALTER TABLE `sessione_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sessione_creato_da` (`creato_da`),
  ADD KEY `sessione_test_ibfk_1` (`id_classe`),
  ADD KEY `sessione_test_ibfk_2` (`id_test`);

--
-- Indexes for table `studente_classe`
--
ALTER TABLE `studente_classe`
  ADD PRIMARY KEY (`id_studente`,`id_classe`),
  ADD KEY `studente_classe_ibfk_2` (`id_classe`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_creato_da` (`creato_da`);

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
-- AUTO_INCREMENT for table `classe`
--
ALTER TABLE `classe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `domanda`
--
ALTER TABLE `domanda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `risposta`
--
ALTER TABLE `risposta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485;

--
-- AUTO_INCREMENT for table `risposte_date`
--
ALTER TABLE `risposte_date`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `risultati`
--
ALTER TABLE `risultati`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ruolo`
--
ALTER TABLE `ruolo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessione_test`
--
ALTER TABLE `sessione_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `docente_classe`
--
ALTER TABLE `docente_classe`
  ADD CONSTRAINT `docente_classe_ibfk_1` FOREIGN KEY (`id_docente`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `docente_classe_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classe` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_risposte_date_sessione` FOREIGN KEY (`id_sessione`) REFERENCES `sessione_test` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `risultati`
--
ALTER TABLE `risultati`
  ADD CONSTRAINT `fk_risultati_sessione` FOREIGN KEY (`id_sessione`) REFERENCES `sessione_test` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `risultati_ibfk_1` FOREIGN KEY (`id_studente`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ruolo_users`
--
ALTER TABLE `ruolo_users`
  ADD CONSTRAINT `fk_ruolo` FOREIGN KEY (`ruolo`) REFERENCES `ruolo` (`ruolo`) ON DELETE CASCADE,
  ADD CONSTRAINT `ruolo_users_ibfk_1` FOREIGN KEY (`id_ruolo`) REFERENCES `ruolo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ruolo_users_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessione_test`
--
ALTER TABLE `sessione_test`
  ADD CONSTRAINT `fk_sessione_creato_da` FOREIGN KEY (`creato_da`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessione_test_ibfk_1` FOREIGN KEY (`id_classe`) REFERENCES `classe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessione_test_ibfk_2` FOREIGN KEY (`id_test`) REFERENCES `test` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `studente_classe`
--
ALTER TABLE `studente_classe`
  ADD CONSTRAINT `studente_classe_ibfk_1` FOREIGN KEY (`id_studente`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `studente_classe_ibfk_2` FOREIGN KEY (`id_classe`) REFERENCES `classe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test`
--
ALTER TABLE `test`
  ADD CONSTRAINT `fk_creato_da` FOREIGN KEY (`creato_da`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
