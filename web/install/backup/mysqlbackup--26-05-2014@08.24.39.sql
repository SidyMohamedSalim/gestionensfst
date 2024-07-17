-- MySQL dump 10.13  Distrib 5.5.24, for Win32 (x86)
--
-- Host: localhost    Database: gestion0
-- ------------------------------------------------------
-- Server version	5.5.24-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fonction` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'ZOUAK','Mohcine','Mohcine.ZOUAK@fst-usmba.ac.ma','Doyen');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affectation`
--

DROP TABLE IF EXISTS `affectation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affectation` (
  `enseignantID` int(11) NOT NULL,
  `element_Module_DetailsID` int(11) NOT NULL,
  `annee_UniversitaireID` int(11) NOT NULL,
  `nature` varchar(50) NOT NULL,
  `affectationID` int(11) NOT NULL AUTO_INCREMENT,
  `groups` int(11) NOT NULL,
  `auto` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`affectationID`),
  KEY `enseignantID` (`enseignantID`),
  KEY `element_Module_DetailsID` (`element_Module_DetailsID`),
  KEY `annee_UniversitaireID` (`annee_UniversitaireID`),
  CONSTRAINT `Annee_Universitaire` FOREIGN KEY (`annee_UniversitaireID`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `Element_Module_Details` FOREIGN KEY (`element_Module_DetailsID`) REFERENCES `element_module_details` (`element_Module_DetailsID`),
  CONSTRAINT `Enseignant` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affectation`
--

LOCK TABLES `affectation` WRITE;
/*!40000 ALTER TABLE `affectation` DISABLE KEYS */;
INSERT INTO `affectation` VALUES (2,1,1,'cours',1,1,0),(2,1,1,'TD',2,1,0),(2,1,1,'TP',3,2,0),(2,5,1,'cours',5,1,0),(1,9,1,'cours',6,1,0),(1,5,1,'TD',8,2,0),(1,5,1,'TP',9,1,0),(2,5,1,'TD',10,2,0),(1,3,1,'cours',11,1,0),(1,3,1,'TP',12,1,0),(1,9,1,'TD',13,1,0),(1,4,1,'TD',14,1,0),(1,9,1,'TP',18,2,0),(1,13,1,'TD',24,1,0),(1,12,1,'TP',30,1,0),(2,12,1,'TP',37,1,0),(2,17,1,'cours',39,1,0),(2,17,1,'TD',40,2,0),(2,17,1,'TP',41,5,0),(2,18,1,'cours',42,1,0),(2,18,1,'TD',43,2,0),(2,3,1,'TD',45,1,0),(7,24,1,'TP',46,3,0),(1,22,1,'cours',47,1,0),(5,29,1,'TP',49,1,0),(8,29,1,'cours',50,1,0),(4,16,2,'TP',52,1,0),(4,14,1,'cours',53,1,0),(4,14,1,'TD',54,2,0),(4,14,1,'TP',55,2,0),(4,15,1,'cours',56,1,0),(4,15,1,'TD',57,2,0),(4,15,1,'TP',58,2,0),(1,30,2,'cours',59,1,0),(1,24,1,'cours',60,1,0),(1,30,2,'TP',62,1,0),(4,41,2,'TD',63,2,0),(8,18,1,'TP',64,2,0),(1,34,3,'cours',66,1,1),(1,34,3,'TD',67,2,1),(1,34,3,'TP',68,2,1),(1,36,3,'cours',69,1,1),(2,36,3,'TD',70,1,1),(2,36,3,'TP',71,1,1),(2,35,3,'cours',72,1,1),(2,35,3,'TD',73,2,1),(2,35,3,'TP',74,2,1),(1,39,3,'TD',75,1,1),(1,39,3,'TP',76,1,1),(1,40,3,'cours',77,1,1),(4,12,1,'TD',78,1,0),(2,22,1,'TP',81,1,0),(8,22,1,'TP',84,1,0),(1,22,1,'TP',85,2,0),(4,10,1,'TD',86,1,0),(4,10,1,'TP',87,2,0),(4,10,1,'cours',88,1,0),(1,33,2,'TP',90,3,0),(2,33,2,'TP',91,3,0),(2,31,2,'TP',92,2,0);
/*!40000 ALTER TABLE `affectation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annee_universitaire`
--

DROP TABLE IF EXISTS `annee_universitaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annee_universitaire` (
  `annee_univ` varchar(50) NOT NULL,
  `annee_UniversitaireID` int(11) NOT NULL AUTO_INCREMENT,
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annee_universitaire`
--

LOCK TABLES `annee_universitaire` WRITE;
/*!40000 ALTER TABLE `annee_universitaire` DISABLE KEYS */;
INSERT INTO `annee_universitaire` VALUES ('2012-2013',1,1),('2013-2014',2,1),('2014-2015',3,0);
/*!40000 ALTER TABLE `annee_universitaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuration` (
  `configID` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `annee_courrante` int(11) NOT NULL,
  PRIMARY KEY (`configID`),
  KEY `FK_configuration_AnneeUniv` (`annee_courrante`),
  KEY `FK_configuration_user` (`user`),
  CONSTRAINT `FK_configuration_AnneeUniv` FOREIGN KEY (`annee_courrante`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `FK_configuration_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuration`
--

LOCK TABLES `configuration` WRITE;
/*!40000 ALTER TABLE `configuration` DISABLE KEYS */;
INSERT INTO `configuration` VALUES (1,1,2),(3,5,3),(7,9,2),(8,12,2),(9,11,2);
/*!40000 ALTER TABLE `configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuration_globale`
--

DROP TABLE IF EXISTS `configuration_globale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configuration_globale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `param` varchar(50) NOT NULL,
  `valeur` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuration_globale`
--

LOCK TABLES `configuration_globale` WRITE;
/*!40000 ALTER TABLE `configuration_globale` DISABLE KEYS */;
INSERT INTO `configuration_globale` VALUES (1,'smtp_host','smtp.mail.yahoo.com'),(2,'smtp_user','site.marrakech@yahoo.com'),(3,'smtp_pass','marrakech123'),(4,'smtp_port','465'),(5,'site_name','Gestion des services FSTF');
/*!40000 ALTER TABLE `configuration_globale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cycle`
--

DROP TABLE IF EXISTS `cycle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cycle` (
  `designation` varchar(50) NOT NULL,
  `nb_semestres` int(11) DEFAULT NULL,
  `cycleID` int(11) NOT NULL AUTO_INCREMENT,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`cycleID`),
  KEY `annee` (`annee`),
  CONSTRAINT `cycle_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cycle`
--

LOCK TABLES `cycle` WRITE;
/*!40000 ALTER TABLE `cycle` DISABLE KEYS */;
INSERT INTO `cycle` VALUES ('Licence',6,1,1),('Master',4,2,1),('Ingenieur',6,3,1),('Doctorat',6,4,1);
/*!40000 ALTER TABLE `cycle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cycle_actif`
--

DROP TABLE IF EXISTS `cycle_actif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cycle_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cycle` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cycle_actif_univ` (`annee`),
  KEY `cycle_actif_cycle` (`cycle`),
  CONSTRAINT `cycle_actif_cycle` FOREIGN KEY (`cycle`) REFERENCES `cycle` (`cycleID`),
  CONSTRAINT `cycle_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cycle_actif`
--

LOCK TABLES `cycle_actif` WRITE;
/*!40000 ALTER TABLE `cycle_actif` DISABLE KEYS */;
INSERT INTO `cycle_actif` VALUES (1,1,3,1),(2,2,3,1),(3,3,3,1),(4,4,3,1),(5,1,1,1),(6,2,1,1),(7,3,1,1),(8,4,1,1),(9,1,2,1),(10,2,2,1),(11,3,2,1),(12,4,2,0);
/*!40000 ALTER TABLE `cycle_actif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departement`
--

DROP TABLE IF EXISTS `departement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departement` (
  `designation` varchar(50) NOT NULL,
  `chef_enseignantID` int(11) DEFAULT NULL,
  `departementID` int(11) NOT NULL AUTO_INCREMENT,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`departementID`),
  KEY `chef_enseignantID` (`chef_enseignantID`),
  KEY `annee` (`annee`),
  CONSTRAINT `departement_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `FK_Departement_Enseignant` FOREIGN KEY (`chef_enseignantID`) REFERENCES `enseignant` (`enseignantID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departement`
--

LOCK TABLES `departement` WRITE;
/*!40000 ALTER TABLE `departement` DISABLE KEYS */;
INSERT INTO `departement` VALUES ('Informatique',NULL,1,1),('Mathematique',NULL,2,2);
/*!40000 ALTER TABLE `departement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departement_actif`
--

DROP TABLE IF EXISTS `departement_actif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departement_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departement` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `dept_actif_univ` (`annee`),
  KEY `dept_actif_dept` (`departement`),
  CONSTRAINT `dept_actif_dept` FOREIGN KEY (`departement`) REFERENCES `departement` (`departementID`),
  CONSTRAINT `dept_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departement_actif`
--

LOCK TABLES `departement_actif` WRITE;
/*!40000 ALTER TABLE `departement_actif` DISABLE KEYS */;
INSERT INTO `departement_actif` VALUES (1,1,1,1),(2,1,2,1),(3,1,3,1),(4,2,2,0),(5,2,1,0),(7,2,3,0);
/*!40000 ALTER TABLE `departement_actif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departement_chef`
--

DROP TABLE IF EXISTS `departement_chef`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departement_chef` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departement` int(11) NOT NULL,
  `enseignant` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dept_chef` (`enseignant`),
  KEY `dept_chef_annee` (`annee`),
  KEY `dept_chef_dept` (`departement`),
  CONSTRAINT `dept_chef` FOREIGN KEY (`enseignant`) REFERENCES `enseignant` (`enseignantID`),
  CONSTRAINT `dept_chef_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `dept_chef_dept` FOREIGN KEY (`departement`) REFERENCES `departement` (`departementID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departement_chef`
--

LOCK TABLES `departement_chef` WRITE;
/*!40000 ALTER TABLE `departement_chef` DISABLE KEYS */;
INSERT INTO `departement_chef` VALUES (1,1,2,1),(2,1,1,2),(3,1,1,3);
/*!40000 ALTER TABLE `departement_chef` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_module`
--

DROP TABLE IF EXISTS `element_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element_module` (
  `code` varchar(50) DEFAULT NULL,
  `designation` varchar(50) NOT NULL,
  `heures_cours` int(11) DEFAULT NULL,
  `heures_td` int(11) DEFAULT NULL,
  `heures_tp` int(11) DEFAULT NULL,
  `departementID` int(11) NOT NULL,
  `element_ModuleID` int(11) NOT NULL AUTO_INCREMENT,
  `moduleID` int(11) NOT NULL,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`element_ModuleID`),
  KEY `departementID` (`departementID`),
  KEY `moduleID` (`moduleID`),
  KEY `annee` (`annee`),
  CONSTRAINT `element_module_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `FK_Element_Module_Departement` FOREIGN KEY (`departementID`) REFERENCES `departement` (`departementID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_Element_Module_Module` FOREIGN KEY (`moduleID`) REFERENCES `module` (`moduleID`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_module`
--

LOCK TABLES `element_module` WRITE;
/*!40000 ALTER TABLE `element_module` DISABLE KEYS */;
INSERT INTO `element_module` VALUES ('I101-1','Architecture',20,13,12,1,1,1,1),('I101-2','système d\'exploitation',20,13,12,1,2,1,1),('I102-1','Programmation et algorithmique',40,20,25,1,4,2,1),('I201-1','Compilation',50,30,20,1,5,3,1),('I202-1','Structures de données',40,20,20,1,6,4,1),('I301-1','Architecture II',30,10,10,1,7,5,1),('I301-2','Systèmes d’exploitation II',30,10,10,1,8,5,1),('I302-1','Réseaux',50,20,20,1,9,6,1),('I303-1','Bases de données',25,10,10,1,10,7,1),('I303-2','Systèmes d’information',25,10,10,1,11,7,1),('I304-1','Concepts et Programmation orientée objet',25,10,10,1,12,8,1),('I304-2','Interface Homme Machine',25,10,10,1,13,8,1),('I305-1','Génie logiciel',25,10,10,1,14,9,1),('I305-1','Analyse orienté objet en UML',25,10,10,1,15,9,1),('I306-1','Techniques du Web',25,10,10,1,16,10,1),('I306-2','Multimédia',25,10,10,1,17,10,1),('I402-1','securite',50,20,20,1,18,11,1),('I402-2','Secuite web',20,20,20,1,19,11,1),('MTH105-1','basic Algebra',50,20,20,2,25,14,1),('MTH105-2','Linear ',60,20,25,1,26,14,1),('I-301-1','Intell. artificille',50,50,30,1,27,15,1),('kkk-1','kjkjh',30,30,30,1,28,16,3),('t2-1','test02-1',50,52,50,1,29,17,3),('t2-2','test02-2',32,20,20,1,30,17,3),('hhh','lllllllllll',50,50,22,1,31,17,3);
/*!40000 ALTER TABLE `element_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_module_actif`
--

DROP TABLE IF EXISTS `element_module_actif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element_module_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_module` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `EM_actif_annee` (`annee`),
  KEY `EM_actif_EM` (`element_module`),
  CONSTRAINT `EM_actif_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `EM_actif_EM` FOREIGN KEY (`element_module`) REFERENCES `element_module` (`element_ModuleID`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_module_actif`
--

LOCK TABLES `element_module_actif` WRITE;
/*!40000 ALTER TABLE `element_module_actif` DISABLE KEYS */;
INSERT INTO `element_module_actif` VALUES (1,1,3,1),(2,2,3,1),(3,4,3,1),(4,5,3,1),(5,6,3,1),(6,7,3,0),(7,8,3,0),(8,9,3,1),(9,10,3,1),(10,11,3,1),(11,12,3,1),(12,13,3,1),(13,14,3,1),(14,15,3,0),(15,16,3,1),(16,17,3,1),(17,18,3,1),(18,19,3,1),(19,1,2,1),(20,2,2,1),(21,7,2,0),(22,8,2,0),(23,4,2,1),(24,5,2,1),(25,6,2,1),(26,9,2,1),(27,10,2,1),(28,11,2,1),(29,12,2,1),(30,13,2,1),(31,14,2,1),(32,15,2,1),(33,16,2,1),(34,17,2,1),(35,18,2,1),(36,19,2,1),(37,1,1,1),(38,2,1,1),(39,4,1,1),(40,5,1,1),(41,6,1,1),(42,7,1,1),(43,8,1,1),(44,9,1,1),(45,10,1,1),(46,11,1,1),(47,12,1,1),(48,13,1,1),(49,14,1,1),(50,15,1,1),(51,16,1,1),(52,17,1,1),(53,18,1,1),(54,19,1,1),(56,25,1,1),(57,26,1,1),(58,27,3,1),(59,25,2,0),(60,26,2,0),(61,28,3,1),(62,29,3,1),(63,30,3,1),(64,31,3,1);
/*!40000 ALTER TABLE `element_module_actif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `element_module_details`
--

DROP TABLE IF EXISTS `element_module_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `element_module_details` (
  `grp_td` int(11) DEFAULT NULL,
  `grp_tp` int(11) DEFAULT NULL,
  `element_ModuleID` int(11) NOT NULL,
  `element_Module_DetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `module_DetailsID` int(11) NOT NULL,
  PRIMARY KEY (`element_Module_DetailsID`),
  KEY `element_ModuleID` (`element_ModuleID`),
  KEY `module_DetailsID` (`module_DetailsID`),
  CONSTRAINT `FK_Element_Module_Details_Element_Module` FOREIGN KEY (`element_ModuleID`) REFERENCES `element_module` (`element_ModuleID`),
  CONSTRAINT `FK_Element_Module_Details_Module_Details` FOREIGN KEY (`module_DetailsID`) REFERENCES `module_details` (`module_DetailsID`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `element_module_details`
--

LOCK TABLES `element_module_details` WRITE;
/*!40000 ALTER TABLE `element_module_details` DISABLE KEYS */;
INSERT INTO `element_module_details` VALUES (2,2,14,1,1),(2,2,15,2,1),(1,2,1,3,3),(1,2,2,4,3),(4,1,5,5,4),(1,2,5,6,5),(1,2,6,9,8),(1,2,16,10,9),(1,2,17,11,9),(1,2,7,12,10),(1,2,8,13,10),(2,2,16,14,11),(2,2,17,15,11),(3,3,4,16,12),(2,5,14,17,13),(2,5,15,18,13),(5,5,1,19,14),(5,5,2,20,14),(5,6,18,21,15),(5,6,18,22,16),(2,3,9,24,18),(4,6,10,26,20),(4,6,11,27,20),(4,5,4,29,22),(2,4,5,30,23),(2,2,1,31,24),(2,2,2,32,24),(5,6,18,33,25),(2,2,1,34,26),(2,2,2,35,26),(1,2,9,36,27),(1,2,16,37,28),(1,2,17,38,28),(2,3,10,39,29),(2,3,11,40,29),(2,2,6,41,30),(2,3,14,52,38);
/*!40000 ALTER TABLE `element_module_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_template`
--

DROP TABLE IF EXISTS `email_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `corp` text NOT NULL,
  `variables` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_template`
--

LOCK TABLES `email_template` WRITE;
/*!40000 ALTER TABLE `email_template` DISABLE KEYS */;
INSERT INTO `email_template` VALUES (1,'Creation d\'un compte utilisateir','Votre compte a été créé avec succès (GS_FSTF)','<ul><li>login:  [login]  </li><li>Mot de passe:  [pass] </li></ul>','login;pass'),(2,'Génération d\'un nouveau mot de passe','Nouveau mot de passe (GS_FSTF)','<p>Un nouveau mot de passe a &eacute;t&eacute; g&eacute;n&eacute;rer pour vous par l\'administrateur..</p>\r\n<ul>\r\n<li>login: [login]</li>\r\n<li>Mot de passe: [pass]</li>\r\n</ul>','login;pass'),(3,'Suppresion d\'un compte utilisateur','Suppression du compte (GS_FSTF)','<p>votre compte a été supprimé par l\'administrateur..</p>',NULL),(4,'Mot de passe oublié','Nouveau mot de passe (GS_FSTF)','<ul><li>login: [login]</li><li>Mot de passe: [pass]</li></ul>','login;pass'),(5,'Notification a propos des fiches de shouhaits','Remplir la fiche de souhaits','<h1 style=\"text-align: center;\">SERVICE GESTION DU DEPARTEMENT INFO</h1>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">Merci de bien vouloir remplir et valider la fiche de souhaits dans votre espace enseignant (<a href=\"fiches_souhaits.php\" target=\"_blank\">ici</a>)</span></p>\r\n<p style=\"text-align: center;\">Cordiallement, de la part du chef du departement</p>\r\n<p style=\"text-align: center;\"><img src=\"http://www.fst-usmba.ac.ma/communfile/logofst.gif\" alt=\"logo fst\" width=\"47\" height=\"52\" /></p>\r\n<p style=\"text-align: center;\"><span class=\"fin\"><strong>FACULTE DES SCIENCES ET TECHNIQUES DE FES <br /> B.P. 2202 &ndash; Route d&rsquo;Imouzzer &ndash; FES <strong>&ndash;</strong> MAROC </strong></span></p>\r\n<p>&nbsp;</p>',NULL);
/*!40000 ALTER TABLE `email_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enseignant`
--

DROP TABLE IF EXISTS `enseignant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enseignant` (
  `email` varchar(50) DEFAULT NULL,
  `grade` int(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `enseignantID` int(11) NOT NULL AUTO_INCREMENT,
  `departementID` int(11) DEFAULT NULL,
  `vacataire` tinyint(1) NOT NULL DEFAULT '0',
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`enseignantID`),
  KEY `departementID` (`departementID`),
  KEY `FK_Enseignant_Grade` (`grade`),
  KEY `FK_Enseignant_Annee` (`annee`),
  CONSTRAINT `FK_Enseignant_Annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `FK_Enseignant_Departement` FOREIGN KEY (`departementID`) REFERENCES `departement` (`departementID`),
  CONSTRAINT `FK_Enseignant_Grade` FOREIGN KEY (`grade`) REFERENCES `grade` (`gradeID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enseignant`
--

LOCK TABLES `enseignant` WRITE;
/*!40000 ALTER TABLE `enseignant` DISABLE KEYS */;
INSERT INTO `enseignant` VALUES ('rachid.benabbou@fst-usmba.ac.ma',1,'BEN ABBOU','Rachid',1,1,0,1),('azzeddine.zahi@fst-usmba.ac.ma',1,'Zahi','Azzeddine',2,1,0,1),('samir.belfkih@fst-usmba.ac.ma',1,'BELFKIH','Samir',3,1,0,1),('ahlame.begdouri@fst-usmba.ac.ma',2,'BEGDOURI','Ahlam',4,1,0,1),('ismail.nait@gmail.com',2,'nait','ismail',5,0,1,1),('email1@email.com',4,'SGH','Othman',7,0,1,1),('arsalane.zarghili@fst-usmba.ac.ma',2,'ZARGHILI','Arsalan',8,1,0,1),('loubna@fstf.ma',2,'Lamrini','Loubna',9,1,0,2),('test@vac.com',4,'nom_vac','prenom_vac',10,0,1,2);
/*!40000 ALTER TABLE `enseignant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enseignant_actif`
--

DROP TABLE IF EXISTS `enseignant_actif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enseignant_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enseignant` int(11) NOT NULL,
  `grade` int(11) DEFAULT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `prof_actif` (`enseignant`),
  KEY `prof_actif_annee` (`annee`),
  KEY `prof_actif_grade` (`grade`),
  CONSTRAINT `prof_actif` FOREIGN KEY (`enseignant`) REFERENCES `enseignant` (`enseignantID`),
  CONSTRAINT `prof_actif_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `prof_actif_grade` FOREIGN KEY (`grade`) REFERENCES `grade` (`gradeID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enseignant_actif`
--

LOCK TABLES `enseignant_actif` WRITE;
/*!40000 ALTER TABLE `enseignant_actif` DISABLE KEYS */;
INSERT INTO `enseignant_actif` VALUES (1,9,2,1,0),(2,1,1,1,1),(3,2,1,1,1),(4,3,3,1,0),(5,4,2,1,1),(6,8,2,1,1),(7,1,1,2,1),(8,2,1,2,1),(9,3,3,2,0),(10,4,2,2,1),(11,8,2,2,1),(12,9,2,2,0),(13,1,1,3,1),(14,2,1,3,1),(15,3,3,3,0),(16,4,2,3,1),(17,8,2,3,1),(18,9,2,3,1),(19,5,2,1,1),(20,7,4,1,1),(21,10,4,3,0),(22,5,2,2,1),(23,7,4,2,0),(24,10,4,2,0),(25,5,2,3,0),(26,7,4,3,0);
/*!40000 ALTER TABLE `enseignant_actif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fiche_souhait`
--

DROP TABLE IF EXISTS `fiche_souhait`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fiche_souhait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee_universitaire` int(11) NOT NULL,
  `departement` int(11) NOT NULL,
  `debut` date NOT NULL,
  `fin` date NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `annee_universitaire` (`annee_universitaire`),
  CONSTRAINT `annee_univ` FOREIGN KEY (`annee_universitaire`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fiche_souhait`
--

LOCK TABLES `fiche_souhait` WRITE;
/*!40000 ALTER TABLE `fiche_souhait` DISABLE KEYS */;
INSERT INTO `fiche_souhait` VALUES (1,2,1,'2013-10-10','2013-10-26',0,0),(2,3,1,'2013-11-21','2013-11-23',0,-1);
/*!40000 ALTER TABLE `fiche_souhait` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fiche_souhait_details`
--

DROP TABLE IF EXISTS `fiche_souhait_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fiche_souhait_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiche` int(11) NOT NULL,
  `enseignantID` int(11) NOT NULL,
  `element_Module_DetailsID` int(11) NOT NULL,
  `nature` varchar(50) NOT NULL,
  `groups` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fiche` (`fiche`),
  KEY `prof` (`enseignantID`),
  KEY `elem_detail` (`element_Module_DetailsID`),
  CONSTRAINT `elem_detail` FOREIGN KEY (`element_Module_DetailsID`) REFERENCES `element_module_details` (`element_Module_DetailsID`),
  CONSTRAINT `fiche` FOREIGN KEY (`fiche`) REFERENCES `fiche_souhait` (`id`),
  CONSTRAINT `prof` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fiche_souhait_details`
--

LOCK TABLES `fiche_souhait_details` WRITE;
/*!40000 ALTER TABLE `fiche_souhait_details` DISABLE KEYS */;
INSERT INTO `fiche_souhait_details` VALUES (1,1,1,30,'TD',1),(2,1,1,16,'cours',1),(3,1,1,30,'cours',0),(4,1,1,30,'TP',1),(5,1,1,16,'TP',3),(6,1,1,16,'TD',2),(7,1,2,30,'TD',2),(8,1,2,16,'TP',2),(9,1,1,31,'cours',1),(10,1,1,32,'TD',1),(11,1,1,32,'cours',0),(12,1,1,33,'TD',4),(13,1,1,33,'TP',5),(14,1,2,33,'TP',3),(15,1,2,33,'TD',4),(16,1,2,31,'cours',1),(17,2,1,34,'cours',1),(18,2,1,34,'TD',2),(19,2,1,34,'TP',2),(20,2,1,36,'cours',1),(21,2,1,37,'TD',1),(22,2,2,35,'cours',1),(23,2,2,35,'TD',2),(24,2,2,35,'TP',2),(25,2,2,36,'TD',1),(26,2,2,36,'TP',1),(27,2,2,37,'TD',1),(28,2,2,37,'TP',0),(29,2,1,39,'TP',1),(30,2,1,39,'TD',1),(31,2,1,40,'cours',1),(32,1,4,30,'TP',1),(33,1,1,31,'TD',0),(34,2,1,35,'cours',1),(35,2,1,38,'TD',0);
/*!40000 ALTER TABLE `fiche_souhait_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fiche_souhait_valid`
--

DROP TABLE IF EXISTS `fiche_souhait_valid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fiche_souhait_valid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiche` int(11) NOT NULL,
  `enseignant` int(11) NOT NULL,
  `valid` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `prof_fiche` (`enseignant`),
  KEY `fiche_valid` (`fiche`),
  CONSTRAINT `fiche_valid` FOREIGN KEY (`fiche`) REFERENCES `fiche_souhait` (`id`),
  CONSTRAINT `prof_fiche` FOREIGN KEY (`enseignant`) REFERENCES `enseignant` (`enseignantID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fiche_souhait_valid`
--

LOCK TABLES `fiche_souhait_valid` WRITE;
/*!40000 ALTER TABLE `fiche_souhait_valid` DISABLE KEYS */;
INSERT INTO `fiche_souhait_valid` VALUES (1,1,1,1),(2,2,2,1);
/*!40000 ALTER TABLE `fiche_souhait_valid` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filiere` (
  `designation` varchar(200) NOT NULL,
  `filiereID` int(11) NOT NULL AUTO_INCREMENT,
  `cycleID` int(11) NOT NULL,
  `departementID` int(11) DEFAULT NULL,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`filiereID`),
  KEY `cycleID` (`cycleID`),
  KEY `departementID` (`departementID`),
  KEY `annee` (`annee`),
  CONSTRAINT `filiere_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `FK_Filiere_Cycle` FOREIGN KEY (`cycleID`) REFERENCES `cycle` (`cycleID`),
  CONSTRAINT `FK_Filiere_Departement` FOREIGN KEY (`departementID`) REFERENCES `departement` (`departementID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filiere`
--

LOCK TABLES `filiere` WRITE;
/*!40000 ALTER TABLE `filiere` DISABLE KEYS */;
INSERT INTO `filiere` VALUES ('Tronc commun MIP',1,1,NULL,1),('Tronc commun BCG ',2,1,NULL,1),('Génie Informatique',3,1,1,1),('Systèmes intelligents et Réseaux',4,2,1,1),('Systèmes Electroniques et Télécommunications',5,3,1,1);
/*!40000 ALTER TABLE `filiere` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `filiere_actif`
--

DROP TABLE IF EXISTS `filiere_actif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filiere_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filiere` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filiere_actif_filiere` (`filiere`),
  KEY `filiere_actif_univ` (`annee`),
  CONSTRAINT `filiere_actif_filiere` FOREIGN KEY (`filiere`) REFERENCES `filiere` (`filiereID`),
  CONSTRAINT `filiere_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `filiere_actif`
--

LOCK TABLES `filiere_actif` WRITE;
/*!40000 ALTER TABLE `filiere_actif` DISABLE KEYS */;
INSERT INTO `filiere_actif` VALUES (1,1,3,1),(2,2,3,1),(3,3,3,1),(4,4,3,1),(5,5,3,1),(6,1,2,1),(7,2,2,1),(8,3,2,1),(9,4,2,1),(10,5,2,1),(11,1,1,1),(12,2,1,1),(13,3,1,1),(14,4,1,1),(15,5,1,0);
/*!40000 ALTER TABLE `filiere_actif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade`
--

DROP TABLE IF EXISTS `grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grade` (
  `gradeID` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `designation` varchar(200) NOT NULL,
  `cours` tinyint(1) NOT NULL,
  `TD` tinyint(1) NOT NULL,
  `TP` tinyint(1) NOT NULL,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`gradeID`),
  KEY `annee` (`annee`),
  CONSTRAINT `grade_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade`
--

LOCK TABLES `grade` WRITE;
/*!40000 ALTER TABLE `grade` DISABLE KEYS */;
INSERT INTO `grade` VALUES (1,'PES','Prof. de l\'enseignement supérieur grade A',1,1,1,1),(2,'PA','Prof. de l\'enseignement supérieur assistant grade A',1,1,1,1),(3,'PH','Professeur d\'habilité',1,1,1,1),(4,'PHD','Doctorant',0,0,1,2);
/*!40000 ALTER TABLE `grade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade_actif`
--

DROP TABLE IF EXISTS `grade_actif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grade_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `chargeHrs` int(11) NOT NULL DEFAULT '0',
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `grade_actif_univ` (`annee`),
  KEY `grade_actif_grade` (`grade`),
  CONSTRAINT `grade_actif_grade` FOREIGN KEY (`grade`) REFERENCES `grade` (`gradeID`),
  CONSTRAINT `grade_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_actif`
--

LOCK TABLES `grade_actif` WRITE;
/*!40000 ALTER TABLE `grade_actif` DISABLE KEYS */;
INSERT INTO `grade_actif` VALUES (1,2,2,100,1),(2,1,2,2001,1),(3,3,2,100,1),(4,4,2,50,1),(5,2,1,200,1),(7,1,1,100,1),(8,3,1,200,0),(9,4,1,50,1),(10,2,3,200,1),(11,1,3,150,1),(12,3,3,80,1),(13,4,3,40,1);
/*!40000 ALTER TABLE `grade_actif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module` (
  `code` varchar(100) NOT NULL,
  `designation` varchar(200) NOT NULL,
  `semestre` int(11) NOT NULL,
  `moduleID` int(11) NOT NULL AUTO_INCREMENT,
  `filiereID` int(11) NOT NULL,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`moduleID`),
  KEY `filiereID` (`filiereID`),
  KEY `annee` (`annee`),
  CONSTRAINT `FK_Module_Filiere` FOREIGN KEY (`filiereID`) REFERENCES `filiere` (`filiereID`),
  CONSTRAINT `module_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module`
--

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` VALUES ('I101','Architecture / système d\'exploitation',1,1,1,1),('I102','Programmation et algorithmique',3,2,1,1),('I201','Compilation',4,3,3,1),('I202','Structures de données',4,4,3,1),('I301','Architecture II & Systèmes d’exploitation II',5,5,3,1),('I302','Réseaux',5,6,3,1),('I303','Bases de données & Systèmes d’information',5,7,3,1),('I304','Concepts et Programmation orientée objet/ Interface Homme Machine',5,8,3,1),('I305','Génie logiciel & Analyse orienté objet en UML',6,9,3,1),('I306','Techniques du Web & Multimédia',6,10,3,1),('I402','securite Informatique',2,11,4,1),('MTH105','Algebra/Linear ',2,14,1,1),('I-301','Intelligence Artificielle',1,15,4,1),('kkk','hhh',3,16,1,3),('t2','test02',3,17,1,3);
/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_actif`
--

DROP TABLE IF EXISTS `module_actif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `module_actif_annee` (`annee`),
  KEY `module_actif_module` (`module`),
  CONSTRAINT `module_actif_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `module_actif_module` FOREIGN KEY (`module`) REFERENCES `module` (`moduleID`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_actif`
--

LOCK TABLES `module_actif` WRITE;
/*!40000 ALTER TABLE `module_actif` DISABLE KEYS */;
INSERT INTO `module_actif` VALUES (1,1,3,1),(2,2,3,1),(3,3,3,1),(4,4,3,1),(5,5,3,0),(6,6,3,1),(7,7,3,1),(8,8,3,1),(9,9,3,1),(10,10,3,1),(11,11,3,1),(12,1,2,1),(13,5,2,0),(14,2,2,1),(15,3,2,1),(16,4,2,1),(17,6,2,1),(18,7,2,1),(19,8,2,1),(20,9,2,1),(21,10,2,1),(22,11,2,1),(23,1,1,1),(24,2,1,1),(25,3,1,1),(26,4,1,1),(27,5,1,1),(28,6,1,1),(29,7,1,1),(30,8,1,1),(31,9,1,1),(32,10,1,1),(33,11,1,1),(34,14,1,1),(35,15,3,1),(36,14,2,0),(37,16,3,1),(38,17,3,1);
/*!40000 ALTER TABLE `module_actif` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_details`
--

DROP TABLE IF EXISTS `module_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_details` (
  `periode` int(11) NOT NULL,
  `moduleID` int(11) NOT NULL,
  `annee_UniversitaireID` int(11) NOT NULL,
  `module_DetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `grp_td` int(11) NOT NULL,
  `grp_tp` int(11) NOT NULL,
  PRIMARY KEY (`module_DetailsID`),
  KEY `moduleID` (`moduleID`),
  KEY `annee_UniversitaireID` (`annee_UniversitaireID`),
  CONSTRAINT `FK_Module_Details_Annee_Universitaire` FOREIGN KEY (`annee_UniversitaireID`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  CONSTRAINT `FK_Module_Details_Module` FOREIGN KEY (`moduleID`) REFERENCES `module` (`moduleID`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_details`
--

LOCK TABLES `module_details` WRITE;
/*!40000 ALTER TABLE `module_details` DISABLE KEYS */;
INSERT INTO `module_details` VALUES (2,9,1,1,2,2),(1,1,1,3,1,2),(1,3,1,4,4,1),(2,3,1,5,1,2),(1,4,1,8,1,2),(2,10,1,9,1,2),(1,5,1,10,1,2),(1,10,1,11,2,2),(1,2,2,12,3,3),(1,9,1,13,2,5),(2,1,1,14,5,5),(2,11,1,15,5,6),(1,11,1,16,5,6),(1,6,1,18,2,3),(2,7,1,20,4,6),(1,2,1,22,4,5),(1,3,2,23,2,4),(1,1,2,24,2,2),(2,11,2,25,5,6),(1,1,3,26,2,2),(1,6,3,27,1,2),(1,10,3,28,1,2),(2,7,3,29,2,3),(2,4,2,30,2,2),(1,9,3,38,2,3);
/*!40000 ALTER TABLE `module_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `pass` char(128) NOT NULL,
  `email` varchar(100) NOT NULL,
  `salt` char(128) DEFAULT NULL,
  `enseignantID` int(11) DEFAULT NULL,
  `access` enum('chef departement','college','enseignant','doyen') NOT NULL DEFAULT 'enseignant',
  `adminID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `FK_User_Enseignant` (`enseignantID`),
  KEY `user_admin` (`adminID`),
  CONSTRAINT `FK_User_Enseignant` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`),
  CONSTRAINT `user_admin` FOREIGN KEY (`adminID`) REFERENCES `admin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','9c4f4b71f4fcac9a3b451a3134d85fc515d592cffa05e690b8abd61b003931b6ef0a36cdf09db4f8918a01f984eb9124a00c08489b40f0397a0342eae9b1e2a7','rachid.benabbou@fst-usmba.ac.ma','5e5e94de4155c6811047e62f7397f8c4008db40dd87453c46debe26ed32b7e36dafe54d181f41cf85bb9157566f4a6a94a04060684a1846b392994070616dee7',1,'chef departement',NULL),(5,'zahi','3eb5f7c8788048cfe6846454ff3a00302ffccc2d4c31714ed9cb69ac19fa024bd0cf10024d88e35535b473195282c1ce73670595ca9d8289dc0a244edbb2a55e','azzeddine.zahi@fst-usmba.ac.ma','6e5768ea9a552a4c5a8e88c6bb85395272619349c46833a148cc7f654fc709c5d75eed826224ab03de4d9cd234a5b584ef0a4eb4e26ac59fba0fdb6b40f05162',2,'college',NULL),(9,'ahlam','664cac20318ea1e67d0d434a6fd08211f3eb26d3a402dd5658b59d54da678e3c9f449d8cd243591f2c093413858de71bc401274e6d4e0f96d1d718a79ab3786e','ahlame.begdouri@fst-usmba.ac.ma','f177ff7a8ea40a247c8a6e51ff24878a52baefd89327d7748e31ba7cba81a023302fb53bf159097ca355659c93fe45633c16f10a501186133ec437dd404beeb0',4,'enseignant',NULL),(11,'doyen','f972975833fff41a8df2d5c293d26f830b122c307e90fada05772ca6090b9c4ec967f3bfc5981f1ee67bcb2fabd3255703594dc0b3b40d35b127857eddcc7983','Mohcine.ZOUAK@fst-usmba.ac.ma','9a2a2812767e27e911f5a2650fc6760deacb87b5f8debfcaf06832e090dba775e3a7f1ed66a60ffbff78463c89ad39c7329979c40574334bac39baeb6af1f141',NULL,'doyen',1),(12,'samir','6832b44877f49fbc1d5806c6495bec32fafa472686d44eb7006b0379be0ce189254eb45c04e6ed634edc326f2df460e41114559b0768cf3b933e669693147313','samir.belfkih@fst-usmba.ac.ma','001c56dd11d45999dea465fe065322458422d85963f8885c265416f37d6796e5c2c3ce445496ac6d297311b63f1a17cee2a912a993f074e54134808ecac6f7e6',3,'enseignant',NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `vacataire`
--

DROP TABLE IF EXISTS `vacataire`;
/*!50001 DROP VIEW IF EXISTS `vacataire`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vacataire` (
  `email` varchar(50),
  `grade` int(50),
  `nom` varchar(50),
  `prenom` varchar(50),
  `enseignantID` int(11),
  `departementID` int(11),
  `vacataire` tinyint(1),
  `annee` int(11)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vacataire`
--

/*!50001 DROP TABLE IF EXISTS `vacataire`*/;
/*!50001 DROP VIEW IF EXISTS `vacataire`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vacataire` AS select `enseignant`.`email` AS `email`,`enseignant`.`grade` AS `grade`,`enseignant`.`nom` AS `nom`,`enseignant`.`prenom` AS `prenom`,`enseignant`.`enseignantID` AS `enseignantID`,`enseignant`.`departementID` AS `departementID`,`enseignant`.`vacataire` AS `vacataire`,`enseignant`.`annee` AS `annee` from `enseignant` where (`enseignant`.`vacataire` = 1) */
/*!50002 WITH CASCADED CHECK OPTION */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-26  9:24:42
