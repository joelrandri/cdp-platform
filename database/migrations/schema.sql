-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 03 nov. 2026 à 15:12
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cdp_test_4000`
--

-- --------------------------------------------------------

--
-- Structure de la table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_customers_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `created_at`, `updated_at`) VALUES
(1, 'julien', 'julien@test.com', '2026-11-02 06:48:03', '2026-11-02 06:48:03'),
(2, 'Miora', 'miora@test.com', '2026-11-02 06:48:03', '2026-11-02 06:48:03'),
(3, 'Marcus Milaire', 'marcus@test.com', '2026-11-02 06:48:03', '2026-11-02 06:48:03'),
(4, 'Felana Rakoto', 'felana@test.com', '2026-11-02 06:48:03', '2026-11-02 06:48:03'),
(5, 'Tojo soa', 'tojo@test.com', '2026-11-02 06:48:03', '2026-11-02 06:48:03'),
(6, 'Jean Dupont', 'jean@example.com', '2026-11-02 19:08:06', '2026-11-03 07:07:31');

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` int UNSIGNED NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_events_customer_created` (`customer_id`,`created_at`),
  KEY `idx_events_customer_type` (`customer_id`,`event_type`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `customer_id`, `event_type`, `created_at`) VALUES
(1, 1, 'page_view', '2026-11-02 06:48:03'),
(2, 1, 'purchase', '2026-11-02 06:48:03'),
(3, 2, 'product_view', '2026-11-02 06:48:03'),
(4, 3, 'add_to_cart', '2026-11-02 06:48:03'),
(5, 4, 'purchase', '2026-11-02 06:48:03'),
(6, 5, 'page_view', '2026-11-02 06:48:03'),
(7, 6, 'purchase', '2026-11-02 19:08:06'),
(8, 6, 'purchase', '2026-11-02 19:11:52'),
(9, 6, 'purchase', '2026-11-02 19:15:40'),
(10, 6, 'purchase', '2026-11-02 19:15:51'),
(11, 6, 'purchase', '2026-11-02 19:16:15'),
(12, 6, 'purchase', '2026-11-03 07:07:31');

-- --------------------------------------------------------

--
-- Structure de la table `event_properties`
--

DROP TABLE IF EXISTS `event_properties`;
CREATE TABLE IF NOT EXISTS `event_properties` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` int UNSIGNED NOT NULL,
  `property_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_event_properties_event_id` (`event_id`),
  KEY `idx_event_properties_name` (`property_name`),
  KEY `idx_properties_name_event` (`property_name`,`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `event_properties`
--

INSERT INTO `event_properties` (`id`, `event_id`, `property_name`, `property_value`, `created_at`) VALUES
(1, 1, 'page_url', '/products', '2026-11-02 06:48:03'),
(2, 1, 'page_title', 'Liste des produits', '2026-11-02 06:48:03'),
(3, 2, 'product_name', 'Ordinateur portable', '2026-11-02 06:48:03'),
(4, 2, 'price', '1200', '2026-11-02 06:48:03'),
(5, 2, 'currency', 'EUR', '2026-11-02 06:48:03'),
(6, 2, 'quantity', '1', '2026-11-02 06:48:03'),
(7, 3, 'product_name', 'Smartphone', '2026-11-02 06:48:03'),
(8, 3, 'category', 'Électronique', '2026-11-02 06:48:03'),
(9, 4, 'product_name', 'Casque audio', '2026-11-02 06:48:03'),
(10, 4, 'quantity', '2', '2026-11-02 06:48:03'),
(11, 5, 'product_name', 'Clavier', '2026-11-02 06:48:03'),
(12, 5, 'price', '80', '2026-11-02 06:48:03'),
(13, 5, 'currency', 'EUR', '2026-11-02 06:48:03'),
(14, 5, 'quantity', '1', '2026-11-02 06:48:03'),
(15, 6, 'page_url', '/about', '2026-11-02 06:48:03'),
(16, 6, 'page_title', 'À propos', '2026-11-02 06:48:03'),
(17, 7, 'amount', '150', '2026-11-02 19:08:06'),
(18, 7, 'currency', 'EUR', '2026-11-02 19:08:06'),
(19, 7, 'product_id', '25', '2026-11-02 19:08:06'),
(20, 8, 'amount', '150', '2026-11-02 19:11:52'),
(21, 8, 'currency', 'EUR', '2026-11-02 19:11:52'),
(22, 8, 'product_id', '25', '2026-11-02 19:11:52'),
(23, 9, 'amount', '[150,12]', '2026-11-02 19:15:40'),
(24, 9, 'currency', 'EUR', '2026-11-02 19:15:40'),
(25, 9, 'product_id', '25', '2026-11-02 19:15:40'),
(26, 10, 'amount', '[150,12]', '2026-11-02 19:15:51'),
(27, 10, 'currency', 'EUR', '2026-11-02 19:15:51'),
(28, 10, 'product_id', '25', '2026-11-02 19:15:51'),
(29, 11, 'amount', '[150,12]', '2026-11-02 19:16:15'),
(30, 11, 'currency', 'EUR', '2026-11-02 19:16:15'),
(31, 11, 'product_id', '25', '2026-11-02 19:16:15'),
(32, 12, 'amount', '150', '2026-11-03 07:07:31'),
(33, 12, 'currency', 'EUR', '2026-11-03 07:07:31'),
(34, 12, 'product_id', '25', '2026-11-03 07:07:31');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_events_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `event_properties`
--
ALTER TABLE `event_properties`
  ADD CONSTRAINT `fk_event_properties_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
