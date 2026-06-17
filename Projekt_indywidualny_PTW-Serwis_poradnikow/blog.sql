-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 15, 2026 at 08:35 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog`
--
CREATE DATABASE IF NOT EXISTS `blog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `blog`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `glosy`
--

DROP TABLE IF EXISTS `glosy`;
CREATE TABLE `glosy` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_posta` int(11) NOT NULL,
  `glosowanie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `glosy`
--

INSERT INTO `glosy` (`id`, `id_uzytkownika`, `id_posta`, `glosowanie`) VALUES
(1, 1, 3, 1),
(2, 1, 2, 1),
(3, 2, 3, 1),
(4, 2, 1, 1),
(5, 3, 2, 1),
(6, 3, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE `kategorie` (
  `nazwa_kategorii` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorie`
--

INSERT INTO `kategorie` (`nazwa_kategorii`) VALUES
('DIY'),
('Motoryzacja'),
('Podróże'),
('Programowanie'),
('Technologia');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `komentarze`
--

DROP TABLE IF EXISTS `komentarze`;
CREATE TABLE `komentarze` (
  `id_komentarza` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_posta` int(11) NOT NULL,
  `tresc_komentarza` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komentarze`
--

INSERT INTO `komentarze` (`id_komentarza`, `id_uzytkownika`, `id_posta`, `tresc_komentarza`) VALUES
(1, 2, 1, 'Przydatne, szczególnie dla osób zaczynających z PHP.'),
(2, 1, 2, 'Dobry pomysł z opaskami do kabli.'),
(3, 1, 3, 'Warto jeszcze pamiętać o płynie chłodniczym.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kroki_poradnika`
--

DROP TABLE IF EXISTS `kroki_poradnika`;
CREATE TABLE `kroki_poradnika` (
  `id_kroku` int(11) NOT NULL,
  `id_posta` int(11) NOT NULL,
  `numer_kroku` int(11) NOT NULL,
  `tresc_kroku` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kroki_poradnika`
--

INSERT INTO `kroki_poradnika` (`id_kroku`, `id_posta`, `numer_kroku`, `tresc_kroku`) VALUES
(1, 1, 1, 'Uruchom panel XAMPP i włącz moduł Apache oraz MySQL.'),
(2, 1, 2, 'Utwórz folder projektu w katalogu htdocs.'),
(3, 1, 3, 'Dodaj plik index.php i wpisz prosty kod HTML z elementami PHP.'),
(4, 1, 4, 'Otwórz w przeglądarce adres localhost/nazwa_folderu.'),
(5, 2, 1, 'Odłącz przewody, które nie są aktualnie potrzebne.'),
(6, 2, 2, 'Pogrupuj kable według urządzeń, na przykład monitor, komputer, ładowarka.'),
(7, 2, 3, 'Zepnij przewody opaskami i schowaj nadmiar kabla za biurkiem.'),
(8, 3, 1, 'Sprawdź poziom oleju silnikowego na zimnym silniku.'),
(9, 3, 2, 'Skontroluj ciśnienie w oponach i stan bieżnika.'),
(10, 3, 3, 'Uzupełnij płyn do spryskiwaczy i sprawdź światła.'),
(14, 4, 1, 'Zanim włożysz cokolwiek do torby, skorzystaj z zestawienia najpotrzebniejszych przedmiotów lub sprawdź ⁠praktyczne wskazówki pakowania, które pozwolą Ci uniknąć zabrania zbyt wielu rzeczy.'),
(15, 4, 2, 'Sposób, w jaki układasz rzeczy, zadecyduje o pojemności Twojego bagażu: zwijanie w rulon, wypełnianie luk, warstwy.'),
(16, 4, 3, 'Użyj dodatkowych akcesoriów, aby zachować ład i porządek przez cały wyjazd: organizery podróżne, worki próżniowe, bagaż podręczny.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `posty`
--

DROP TABLE IF EXISTS `posty`;
CREATE TABLE `posty` (
  `id_posta` int(11) NOT NULL,
  `id_uzytkownika` int(11) DEFAULT NULL,
  `tytul` varchar(255) NOT NULL,
  `tresc` varchar(10000) NOT NULL,
  `kategoria` varchar(255) NOT NULL,
  `data_utworzenia` datetime NOT NULL DEFAULT current_timestamp(),
  `zdjecie` varchar(255) NOT NULL,
  `poziom_trudnosci` varchar(30) NOT NULL,
  `czas_wykonania` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posty`
--

INSERT INTO `posty` (`id_posta`, `id_uzytkownika`, `tytul`, `tresc`, `kategoria`, `data_utworzenia`, `zdjecie`, `poziom_trudnosci`, `czas_wykonania`) VALUES
(1, 1, 'Jak przygotować prostą stronę w PHP?', 'Ten poradnik pokazuje podstawowe kroki potrzebne do przygotowania prostej strony PHP działającej w XAMPP. Dzięki temu można szybko sprawdzić, czy Apache, PHP i folder htdocs działają poprawnie.', 'Programowanie', '2026-06-08 12:00:00', 'zdjecia/technology.jpg', 'Łatwy', '20 minut'),
(2, 2, 'Jak uporządkować przewody przy biurku?', 'Krótki poradnik pokazujący prosty sposób na uporządkowanie kabli przy stanowisku komputerowym. Nie wymaga drogich akcesoriów, wystarczą opaski, pudełko i trochę cierpliwości.', 'DIY', '2026-06-08 12:10:00', 'zdjecia/diy.jpg', 'Łatwy', '15 minut'),
(3, 3, 'Jak sprawdzić podstawowe rzeczy przed jazdą?', 'Poradnik dla początkujących kierowców. Opisuje proste czynności kontrolne, które warto wykonać przed dłuższą trasą samochodem.', 'Motoryzacja', '2026-06-08 12:20:00', 'zdjecia/automotive.jpg', 'Średni', '10 minut'),
(4, 4, 'Jak się efektywnie spakować?', 'Efektywne pakowanie to przede wszystkim minimalizm i organizacja. Zamiast składać ubrania w kostkę, zroluj je, aby zaoszczędzić miejsce i uniknąć zagnieceń. Wykorzystaj wolne przestrzenie, wkładając skarpetki i ładowarki do wnętrza butów. Ciężkie przedmioty zawsze umieszczaj na dole walizki, przy jej kółkach.', 'Podróże', '2026-06-15 19:21:35', 'uploads/1781544095_6a30349fab717.jpg', 'Łatwy', '20 minut');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ukonczone_kroki`
--

DROP TABLE IF EXISTS `ukonczone_kroki`;
CREATE TABLE `ukonczone_kroki` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_kroku` int(11) NOT NULL,
  `data_ukonczenia` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ukonczone_kroki`
--

INSERT INTO `ukonczone_kroki` (`id`, `id_uzytkownika`, `id_kroku`, `data_ukonczenia`) VALUES
(1, 1, 8, '2026-06-15 20:33:19'),
(2, 1, 9, '2026-06-15 20:33:19'),
(3, 1, 10, '2026-06-15 20:33:19'),
(4, 1, 5, '2026-06-15 20:33:29'),
(5, 1, 6, '2026-06-15 20:33:29'),
(6, 1, 7, '2026-06-15 20:33:29'),
(7, 2, 8, '2026-06-15 20:33:54'),
(8, 2, 9, '2026-06-15 20:33:54'),
(9, 2, 10, '2026-06-15 20:33:54'),
(10, 2, 1, '2026-06-15 20:33:58'),
(11, 2, 2, '2026-06-15 20:33:58'),
(12, 2, 3, '2026-06-15 20:33:58'),
(13, 2, 4, '2026-06-15 20:33:58'),
(17, 3, 5, '2026-06-15 20:34:25'),
(18, 3, 6, '2026-06-15 20:34:25'),
(19, 3, 7, '2026-06-15 20:34:25'),
(20, 3, 1, '2026-06-15 20:34:30'),
(21, 3, 2, '2026-06-15 20:34:30'),
(22, 3, 3, '2026-06-15 20:34:30'),
(23, 3, 4, '2026-06-15 20:34:30');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ukonczone_poradniki`
--

DROP TABLE IF EXISTS `ukonczone_poradniki`;
CREATE TABLE `ukonczone_poradniki` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_posta` int(11) NOT NULL,
  `data_ukonczenia` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ukonczone_poradniki`
--

INSERT INTO `ukonczone_poradniki` (`id`, `id_uzytkownika`, `id_posta`, `data_ukonczenia`) VALUES
(1, 1, 3, '2026-06-15 20:33:19'),
(2, 1, 2, '2026-06-15 20:33:29'),
(3, 2, 3, '2026-06-15 20:33:54'),
(4, 2, 1, '2026-06-15 20:33:58'),
(5, 3, 2, '2026-06-15 20:34:25'),
(6, 3, 1, '2026-06-15 20:34:30');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

DROP TABLE IF EXISTS `uzytkownicy`;
CREATE TABLE `uzytkownicy` (
  `id_uzytkownika` int(11) NOT NULL,
  `nazwa_uzytkownika` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id_uzytkownika`, `nazwa_uzytkownika`, `haslo`) VALUES
(1, 'user1', '$2y$10$GmmdYq6KSDxWrZe4/sULEeczbgxelwaNOk.1ZEG00q8q2OOUBiRDC'),
(2, 'user2', '$2y$10$E47VGlqOyPSF3idUEmTBNeih.9GcESZyKpjcYwKnYs6FXwLA3fVti'),
(3, 'user3', '$2y$10$VMP7WkquR76wbiDQPrVYHOGOiCKzDlJdKIoRxBQv/2ezbD2j1C6tK'),
(4, 'user4', '$2y$10$lM6OLv.ok5Yrt.hIqr.id.ga25t.qAsYxbaet2VYADsRwy2OglIGq');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `glosy`
--
ALTER TABLE `glosy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jeden_glos_na_post` (`id_uzytkownika`,`id_posta`),
  ADD KEY `id_posta` (`id_posta`);

--
-- Indeksy dla tabeli `kategorie`
--
ALTER TABLE `kategorie`
  ADD PRIMARY KEY (`nazwa_kategorii`);

--
-- Indeksy dla tabeli `komentarze`
--
ALTER TABLE `komentarze`
  ADD PRIMARY KEY (`id_komentarza`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
  ADD KEY `id_posta` (`id_posta`);

--
-- Indeksy dla tabeli `kroki_poradnika`
--
ALTER TABLE `kroki_poradnika`
  ADD PRIMARY KEY (`id_kroku`),
  ADD KEY `id_posta` (`id_posta`);

--
-- Indeksy dla tabeli `posty`
--
ALTER TABLE `posty`
  ADD PRIMARY KEY (`id_posta`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
  ADD KEY `kategoria` (`kategoria`);

--
-- Indeksy dla tabeli `ukonczone_kroki`
--
ALTER TABLE `ukonczone_kroki`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jeden_ukonczony_krok` (`id_uzytkownika`,`id_kroku`),
  ADD KEY `id_kroku` (`id_kroku`);

--
-- Indeksy dla tabeli `ukonczone_poradniki`
--
ALTER TABLE `ukonczone_poradniki`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jeden_ukonczony_poradnik` (`id_uzytkownika`,`id_posta`),
  ADD KEY `id_posta` (`id_posta`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id_uzytkownika`),
  ADD UNIQUE KEY `nazwa_uzytkownika` (`nazwa_uzytkownika`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `glosy`
--
ALTER TABLE `glosy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `komentarze`
--
ALTER TABLE `komentarze`
  MODIFY `id_komentarza` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kroki_poradnika`
--
ALTER TABLE `kroki_poradnika`
  MODIFY `id_kroku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `posty`
--
ALTER TABLE `posty`
  MODIFY `id_posta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ukonczone_kroki`
--
ALTER TABLE `ukonczone_kroki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `ukonczone_poradniki`
--
ALTER TABLE `ukonczone_poradniki`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id_uzytkownika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `glosy`
--
ALTER TABLE `glosy`
  ADD CONSTRAINT `glosy_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`),
  ADD CONSTRAINT `glosy_ibfk_2` FOREIGN KEY (`id_posta`) REFERENCES `posty` (`id_posta`);

--
-- Constraints for table `komentarze`
--
ALTER TABLE `komentarze`
  ADD CONSTRAINT `komentarze_ibfk_1` FOREIGN KEY (`id_posta`) REFERENCES `posty` (`id_posta`),
  ADD CONSTRAINT `komentarze_ibfk_2` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`);

--
-- Constraints for table `kroki_poradnika`
--
ALTER TABLE `kroki_poradnika`
  ADD CONSTRAINT `kroki_poradnika_ibfk_1` FOREIGN KEY (`id_posta`) REFERENCES `posty` (`id_posta`);

--
-- Constraints for table `posty`
--
ALTER TABLE `posty`
  ADD CONSTRAINT `posty_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`),
  ADD CONSTRAINT `posty_ibfk_2` FOREIGN KEY (`kategoria`) REFERENCES `kategorie` (`nazwa_kategorii`);

--
-- Constraints for table `ukonczone_kroki`
--
ALTER TABLE `ukonczone_kroki`
  ADD CONSTRAINT `ukonczone_kroki_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`),
  ADD CONSTRAINT `ukonczone_kroki_ibfk_2` FOREIGN KEY (`id_kroku`) REFERENCES `kroki_poradnika` (`id_kroku`);

--
-- Constraints for table `ukonczone_poradniki`
--
ALTER TABLE `ukonczone_poradniki`
  ADD CONSTRAINT `ukonczone_poradniki_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`),
  ADD CONSTRAINT `ukonczone_poradniki_ibfk_2` FOREIGN KEY (`id_posta`) REFERENCES `posty` (`id_posta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
