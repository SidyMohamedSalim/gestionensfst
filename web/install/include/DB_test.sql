-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2015 at 02:50 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `gs1`
--

-- --------------------------------------------------------

--
-- Table structure for table `affectation`
--

--
-- nouveau champ for table `affectation`
--
ALTER TABLE `affectation` ADD `partage` TINYINT(2) NOT NULL DEFAULT '0' ;
 
ALTER TABLE `affectation` CHANGE `enseignantID` `enseignantID` INT(11) NULL DEFAULT NULL; 

-- --------------------------------------------------------

--
-- Table structure for table `affectation_partage`
--

CREATE TABLE IF NOT EXISTS `affectation_partage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affectationID` int(11) NOT NULL,
  `enseignantID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affectationID` (`affectationID`),
  KEY `enseignantID` (`enseignantID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `affectation_partage`
--

--
-- Constraints for dumped tables
--

--
-- Constraints for table `affectation_partage`
--
ALTER TABLE `affectation_partage`
  ADD CONSTRAINT `FK_aff_partage_aff_id` FOREIGN KEY (`affectationID`) REFERENCES `affectation` (`affectationID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_aff_partage_ens_id` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`) ON DELETE CASCADE ON UPDATE CASCADE;
