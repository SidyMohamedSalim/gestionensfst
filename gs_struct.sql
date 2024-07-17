-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 26 mars 2024 à 12:19
-- Version du serveur : 5.7.40
-- Version de PHP : 8.0.26

START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gs`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fonction` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `affectation`
--

DROP TABLE IF EXISTS `affectation`;
CREATE TABLE IF NOT EXISTS `affectation` (
  `enseignantID` int(11) NOT NULL,
  `element_Module_DetailsID` int(11) NOT NULL,
  `annee_UniversitaireID` int(11) NOT NULL,
  `nature` varchar(50) NOT NULL,
  `affectationID` int(11) NOT NULL AUTO_INCREMENT,
  `groups` int(11) NOT NULL,
  `auto` tinyint(4) NOT NULL DEFAULT '0',
  `partage` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`affectationID`),
  KEY `enseignantID` (`enseignantID`),
  KEY `element_Module_DetailsID` (`element_Module_DetailsID`),
  KEY `annee_UniversitaireID` (`annee_UniversitaireID`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `affectation_partage`
--

DROP TABLE IF EXISTS `affectation_partage`;
CREATE TABLE IF NOT EXISTS `affectation_partage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affectationID` int(11) NOT NULL,
  `enseignantID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affectationID` (`affectationID`),
  KEY `enseignantID` (`enseignantID`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `annee_universitaire`
--

DROP TABLE IF EXISTS `annee_universitaire`;
CREATE TABLE IF NOT EXISTS `annee_universitaire` (
  `annee_univ` varchar(50) NOT NULL,
  `annee_UniversitaireID` int(11) NOT NULL AUTO_INCREMENT,
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`annee_UniversitaireID`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
CREATE TABLE IF NOT EXISTS `configuration` (
  `configID` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `annee_courrante` int(11) NOT NULL,
  `elementParPage` enum('10','25','50','100') NOT NULL DEFAULT '10',
  `theme` varchar(15) NOT NULL DEFAULT 'classic',
  PRIMARY KEY (`configID`),
  KEY `FK_configuration_AnneeUniv` (`annee_courrante`),
  KEY `FK_configuration_user` (`user`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `configuration_globale`
--

DROP TABLE IF EXISTS `configuration_globale`;
CREATE TABLE IF NOT EXISTS `configuration_globale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `param` varchar(50) NOT NULL,
  `valeur` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `cycle`
--

DROP TABLE IF EXISTS `cycle`;
CREATE TABLE IF NOT EXISTS `cycle` (
  `designation` varchar(50) NOT NULL,
  `nb_semestres` int(11) DEFAULT NULL,
  `cycleID` int(11) NOT NULL AUTO_INCREMENT,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`cycleID`),
  KEY `annee` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `cycle_actif`
--

DROP TABLE IF EXISTS `cycle_actif`;
CREATE TABLE IF NOT EXISTS `cycle_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cycle` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cycle_actif_univ` (`annee`),
  KEY `cycle_actif_cycle` (`cycle`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `designation` varchar(50) NOT NULL,
  `chef_enseignantID` int(11) DEFAULT NULL,
  `departementID` int(11) NOT NULL AUTO_INCREMENT,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`departementID`),
  KEY `chef_enseignantID` (`chef_enseignantID`),
  KEY `annee` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `departement_actif`
--

DROP TABLE IF EXISTS `departement_actif`;
CREATE TABLE IF NOT EXISTS `departement_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departement` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `dept_actif_univ` (`annee`),
  KEY `dept_actif_dept` (`departement`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `departement_chef`
--

DROP TABLE IF EXISTS `departement_chef`;
CREATE TABLE IF NOT EXISTS `departement_chef` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `departement` int(11) NOT NULL,
  `enseignant` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dept_chef` (`enseignant`),
  KEY `dept_chef_annee` (`annee`),
  KEY `dept_chef_dept` (`departement`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `element_module`
--

DROP TABLE IF EXISTS `element_module`;
CREATE TABLE IF NOT EXISTS `element_module` (
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
  KEY `annee` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `element_module_actif`
--

DROP TABLE IF EXISTS `element_module_actif`;
CREATE TABLE IF NOT EXISTS `element_module_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `element_module` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `EM_actif_annee` (`annee`),
  KEY `EM_actif_EM` (`element_module`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `element_module_details`
--

DROP TABLE IF EXISTS `element_module_details`;
CREATE TABLE IF NOT EXISTS `element_module_details` (
  `grp_cours` int(11) NOT NULL DEFAULT '1',
  `grp_td` int(11) DEFAULT NULL,
  `grp_tp` int(11) DEFAULT NULL,
  `element_ModuleID` int(11) NOT NULL,
  `element_Module_DetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `module_DetailsID` int(11) NOT NULL,
  PRIMARY KEY (`element_Module_DetailsID`),
  KEY `element_ModuleID` (`element_ModuleID`),
  KEY `module_DetailsID` (`module_DetailsID`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `email_template`
--

DROP TABLE IF EXISTS `email_template`;
CREATE TABLE IF NOT EXISTS `email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `corp` text NOT NULL,
  `variables` text,
  PRIMARY KEY (`id`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `enseignant`
--

DROP TABLE IF EXISTS `enseignant`;
CREATE TABLE IF NOT EXISTS `enseignant` (
  `email` varchar(50) DEFAULT NULL,
  `grade` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `enseignantID` int(11) NOT NULL AUTO_INCREMENT,
  `departementID` int(11) DEFAULT NULL,
  `vacataire` tinyint(1) NOT NULL DEFAULT '0',
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`enseignantID`),
  KEY `departementID` (`departementID`),
  KEY `FK_Enseignant_Grade` (`grade`),
  KEY `FK_Enseignant_Annee` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `enseignant_actif`
--

DROP TABLE IF EXISTS `enseignant_actif`;
CREATE TABLE IF NOT EXISTS `enseignant_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enseignant` int(11) NOT NULL,
  `grade` int(11) DEFAULT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `prof_actif` (`enseignant`),
  KEY `prof_actif_annee` (`annee`),
  KEY `prof_actif_grade` (`grade`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `fiche_souhait`
--

DROP TABLE IF EXISTS `fiche_souhait`;
CREATE TABLE IF NOT EXISTS `fiche_souhait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annee_universitaire` int(11) NOT NULL,
  `departement` int(11) NOT NULL,
  `debut` date NOT NULL,
  `fin` date NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `annee_universitaire` (`annee_universitaire`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `fiche_souhait_details`
--

DROP TABLE IF EXISTS `fiche_souhait_details`;
CREATE TABLE IF NOT EXISTS `fiche_souhait_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiche` int(11) NOT NULL,
  `enseignantID` int(11) NOT NULL,
  `element_Module_DetailsID` int(11) NOT NULL,
  `nature` varchar(50) NOT NULL,
  `groups` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fiche` (`fiche`),
  KEY `prof` (`enseignantID`),
  KEY `elem_detail` (`element_Module_DetailsID`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `fiche_souhait_valid`
--

DROP TABLE IF EXISTS `fiche_souhait_valid`;
CREATE TABLE IF NOT EXISTS `fiche_souhait_valid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fiche` int(11) NOT NULL,
  `enseignant` int(11) NOT NULL,
  `valid` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `prof_fiche` (`enseignant`),
  KEY `fiche_valid` (`fiche`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
CREATE TABLE IF NOT EXISTS `filiere` (
  `designation` varchar(200) NOT NULL,
  `filiereID` int(11) NOT NULL AUTO_INCREMENT,
  `cycleID` int(11) NOT NULL,
  `departementID` int(11) DEFAULT NULL,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`filiereID`),
  KEY `cycleID` (`cycleID`),
  KEY `departementID` (`departementID`),
  KEY `annee` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `filiere_actif`
--

DROP TABLE IF EXISTS `filiere_actif`;
CREATE TABLE IF NOT EXISTS `filiere_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filiere` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filiere_actif_filiere` (`filiere`),
  KEY `filiere_actif_univ` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `grade`
--

DROP TABLE IF EXISTS `grade`;
CREATE TABLE IF NOT EXISTS `grade` (
  `gradeID` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `designation` varchar(200) NOT NULL,
  `cours` tinyint(1) NOT NULL,
  `TD` tinyint(1) NOT NULL,
  `TP` tinyint(1) NOT NULL,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`gradeID`),
  KEY `annee` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `grade_actif`
--

DROP TABLE IF EXISTS `grade_actif`;
CREATE TABLE IF NOT EXISTS `grade_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `chargeHrs` int(11) NOT NULL DEFAULT '0',
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `grade_actif_univ` (`annee`),
  KEY `grade_actif_grade` (`grade`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Structure de la table `module`
--

DROP TABLE IF EXISTS `module`;
CREATE TABLE IF NOT EXISTS `module` (
  `code` varchar(100) NOT NULL,
  `designation` varchar(200) NOT NULL,
  `semestre` int(11) NOT NULL,
  `moduleID` int(11) NOT NULL AUTO_INCREMENT,
  `filiereID` int(11) NOT NULL,
  `annee` int(11) DEFAULT NULL,
  PRIMARY KEY (`moduleID`),
  KEY `filiereID` (`filiereID`),
  KEY `annee` (`annee`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `module_actif`
--

DROP TABLE IF EXISTS `module_actif`;
CREATE TABLE IF NOT EXISTS `module_actif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `actif` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `module_actif_annee` (`annee`),
  KEY `module_actif_module` (`module`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `module_details`
--

DROP TABLE IF EXISTS `module_details`;
CREATE TABLE IF NOT EXISTS `module_details` (
  `periode` int(11) NOT NULL,
  `moduleID` int(11) NOT NULL,
  `annee_UniversitaireID` int(11) NOT NULL,
  `module_DetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `grp_cours` int(11) NOT NULL DEFAULT '1',
  `grp_td` int(11) NOT NULL,
  `grp_tp` int(11) NOT NULL,
  PRIMARY KEY (`module_DetailsID`),
  KEY `moduleID` (`moduleID`),
  KEY `annee_UniversitaireID` (`annee_UniversitaireID`)
) ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
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
  KEY `user_admin` (`adminID`)
) ;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `vacataire`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `vacataire`;
CREATE TABLE IF NOT EXISTS `vacataire` (
`email` varchar(50)
,`grade` int(11)
,`nom` varchar(50)
,`prenom` varchar(50)
,`enseignantID` int(11)
,`departementID` int(11)
,`vacataire` tinyint(1)
,`annee` int(11)
);

-- --------------------------------------------------------

--
-- Structure de la vue `vacataire`
--
DROP TABLE IF EXISTS `vacataire`;

DROP VIEW IF EXISTS `vacataire`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vacataire`  AS SELECT `enseignant`.`email` AS `email`, `enseignant`.`grade` AS `grade`, `enseignant`.`nom` AS `nom`, `enseignant`.`prenom` AS `prenom`, `enseignant`.`enseignantID` AS `enseignantID`, `enseignant`.`departementID` AS `departementID`, `enseignant`.`vacataire` AS `vacataire`, `enseignant`.`annee` AS `annee` FROM `enseignant` WHERE (`enseignant`.`vacataire` = 1) WITH CASCADED CHECK OPTION  ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affectation`
--
ALTER TABLE `affectation`
  ADD CONSTRAINT `Annee_Universitaire` FOREIGN KEY (`annee_UniversitaireID`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  ADD CONSTRAINT `Element_Module_Details` FOREIGN KEY (`element_Module_DetailsID`) REFERENCES `element_module_details` (`element_Module_DetailsID`),
  ADD CONSTRAINT `Enseignant` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`);

--
-- Contraintes pour la table `affectation_partage`
--
ALTER TABLE `affectation_partage`
  ADD CONSTRAINT `FK_aff_partage_aff_id` FOREIGN KEY (`affectationID`) REFERENCES `affectation` (`affectationID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_aff_partage_ens_id` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `configuration`
--
ALTER TABLE `configuration`
  ADD CONSTRAINT `FK_configuration_AnneeUniv` FOREIGN KEY (`annee_courrante`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  ADD CONSTRAINT `FK_configuration_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `cycle`
--
ALTER TABLE `cycle`
  ADD CONSTRAINT `cycle_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `cycle_actif`
--
ALTER TABLE `cycle_actif`
  ADD CONSTRAINT `cycle_actif_cycle` FOREIGN KEY (`cycle`) REFERENCES `cycle` (`cycleID`),
  ADD CONSTRAINT `cycle_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `departement`
--
ALTER TABLE `departement`
  ADD CONSTRAINT `FK_Departement_Enseignant` FOREIGN KEY (`chef_enseignantID`) REFERENCES `enseignant` (`enseignantID`),
  ADD CONSTRAINT `departement_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `departement_actif`
--
ALTER TABLE `departement_actif`
  ADD CONSTRAINT `dept_actif_dept` FOREIGN KEY (`departement`) REFERENCES `departement` (`departementID`),
  ADD CONSTRAINT `dept_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `departement_chef`
--
ALTER TABLE `departement_chef`
  ADD CONSTRAINT `dept_chef` FOREIGN KEY (`enseignant`) REFERENCES `enseignant` (`enseignantID`),
  ADD CONSTRAINT `dept_chef_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  ADD CONSTRAINT `dept_chef_dept` FOREIGN KEY (`departement`) REFERENCES `departement` (`departementID`);

--
-- Contraintes pour la table `element_module`
--
ALTER TABLE `element_module`
  ADD CONSTRAINT `FK_Element_Module_Departement` FOREIGN KEY (`departementID`) REFERENCES `departement` (`departementID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_Element_Module_Module` FOREIGN KEY (`moduleID`) REFERENCES `module` (`moduleID`),
  ADD CONSTRAINT `element_module_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `element_module_actif`
--
ALTER TABLE `element_module_actif`
  ADD CONSTRAINT `EM_actif_EM` FOREIGN KEY (`element_module`) REFERENCES `element_module` (`element_ModuleID`),
  ADD CONSTRAINT `EM_actif_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `element_module_details`
--
ALTER TABLE `element_module_details`
  ADD CONSTRAINT `FK_Element_Module_Details_Element_Module` FOREIGN KEY (`element_ModuleID`) REFERENCES `element_module` (`element_ModuleID`),
  ADD CONSTRAINT `FK_Element_Module_Details_Module_Details` FOREIGN KEY (`module_DetailsID`) REFERENCES `module_details` (`module_DetailsID`);

--
-- Contraintes pour la table `enseignant`
--
ALTER TABLE `enseignant`
  ADD CONSTRAINT `FK_Enseignant_Annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  ADD CONSTRAINT `FK_Enseignant_Departement` FOREIGN KEY (`departementID`) REFERENCES `departement` (`departementID`),
  ADD CONSTRAINT `FK_Enseignant_Grade` FOREIGN KEY (`grade`) REFERENCES `grade` (`gradeID`);

--
-- Contraintes pour la table `enseignant_actif`
--
ALTER TABLE `enseignant_actif`
  ADD CONSTRAINT `prof_actif` FOREIGN KEY (`enseignant`) REFERENCES `enseignant` (`enseignantID`),
  ADD CONSTRAINT `prof_actif_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  ADD CONSTRAINT `prof_actif_grade` FOREIGN KEY (`grade`) REFERENCES `grade` (`gradeID`);

--
-- Contraintes pour la table `fiche_souhait`
--
ALTER TABLE `fiche_souhait`
  ADD CONSTRAINT `annee_univ` FOREIGN KEY (`annee_universitaire`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `fiche_souhait_details`
--
ALTER TABLE `fiche_souhait_details`
  ADD CONSTRAINT `elem_detail` FOREIGN KEY (`element_Module_DetailsID`) REFERENCES `element_module_details` (`element_Module_DetailsID`),
  ADD CONSTRAINT `fiche` FOREIGN KEY (`fiche`) REFERENCES `fiche_souhait` (`id`),
  ADD CONSTRAINT `prof` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`);

--
-- Contraintes pour la table `fiche_souhait_valid`
--
ALTER TABLE `fiche_souhait_valid`
  ADD CONSTRAINT `fiche_valid` FOREIGN KEY (`fiche`) REFERENCES `fiche_souhait` (`id`),
  ADD CONSTRAINT `prof_fiche` FOREIGN KEY (`enseignant`) REFERENCES `enseignant` (`enseignantID`);

--
-- Contraintes pour la table `filiere_actif`
--
ALTER TABLE `filiere_actif`
  ADD CONSTRAINT `filiere_actif_filiere` FOREIGN KEY (`filiere`) REFERENCES `filiere` (`filiereID`),
  ADD CONSTRAINT `filiere_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `grade`
--
ALTER TABLE `grade`
  ADD CONSTRAINT `grade_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `grade_actif`
--
ALTER TABLE `grade_actif`
  ADD CONSTRAINT `grade_actif_grade` FOREIGN KEY (`grade`) REFERENCES `grade` (`gradeID`),
  ADD CONSTRAINT `grade_actif_univ` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `FK_Module_Filiere` FOREIGN KEY (`filiereID`) REFERENCES `filiere` (`filiereID`),
  ADD CONSTRAINT `module_ibfk_1` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`);

--
-- Contraintes pour la table `module_actif`
--
ALTER TABLE `module_actif`
  ADD CONSTRAINT `module_actif_annee` FOREIGN KEY (`annee`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  ADD CONSTRAINT `module_actif_module` FOREIGN KEY (`module`) REFERENCES `module` (`moduleID`);

--
-- Contraintes pour la table `module_details`
--
ALTER TABLE `module_details`
  ADD CONSTRAINT `FK_Module_Details_Annee_Universitaire` FOREIGN KEY (`annee_UniversitaireID`) REFERENCES `annee_universitaire` (`annee_UniversitaireID`),
  ADD CONSTRAINT `FK_Module_Details_Module` FOREIGN KEY (`moduleID`) REFERENCES `module` (`moduleID`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_User_Enseignant` FOREIGN KEY (`enseignantID`) REFERENCES `enseignant` (`enseignantID`),
  ADD CONSTRAINT `user_admin` FOREIGN KEY (`adminID`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
