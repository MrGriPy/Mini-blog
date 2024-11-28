-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 28 nov. 2024 à 16:19
-- Version du serveur : 10.6.19-MariaDB
-- Version de PHP : 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tvidal_mini_blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `billets`
--

CREATE TABLE `billets` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `contenu` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date_post` datetime DEFAULT current_timestamp(),
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `billets`
--

INSERT INTO `billets` (`id`, `titre`, `contenu`, `date_post`, `id_utilisateur`) VALUES
(3, 'test', 'test', '2024-10-15 22:54:51', 1),
(5, 'test 3', 'après', '2024-10-15 22:57:01', 1),
(7, 'test 5', 'adieu', '2024-10-15 22:57:40', 1),
(8, 'test 18', 'oui', '2024-10-16 21:23:33', 1),
(9, 'Test 666', 'Que penseriez-vous d\'un post si long que j\'en perds tout mon langage français, en vrai là je brode t\'as vu, mais je pense honnêtement que c\'est nécessaire pour prouver que le code fonctionne et que la personne qui lit ceci perd vraiment son temps.', '2024-10-22 09:05:09', 1);

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL,
  `contenu` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `date_post` datetime DEFAULT current_timestamp(),
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_billet` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`id`, `contenu`, `date_post`, `id_utilisateur`, `id_billet`) VALUES
(8, 'lolo', '2024-10-16 19:18:20', 1, 7),
(10, 'test', '2024-10-22 08:23:27', 11, 8),
(11, 'toito', '2024-10-22 08:23:46', 11, 8),
(12, 'test', '2024-10-22 09:14:05', 1, 9),
(13, 'oui', '2024-10-22 09:14:51', 1, 9),
(14, 'teesst', '2024-10-22 09:47:32', 1, 5),
(15, 'comment ça mon reuf ?', '2024-10-22 09:50:08', 1, 8),
(16, 'comment ça mon reuf ?', '2024-10-22 09:52:00', 1, 8),
(17, 'comment ça mon reuf ?', '2024-10-22 09:52:02', 1, 8),
(18, 'Mais nan', '2024-10-22 09:52:50', 1, 7),
(19, 'test', '2024-10-22 09:55:40', 1, 7),
(20, 'test jpp', '2024-11-04 08:20:38', 1, 9);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `login` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `photo_profil` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `login`, `password`, `photo_profil`, `date_inscription`) VALUES
(1, 'thomas', '$2y$10$V4T.X101F6AxAbrLuzxku.VeT.eI7MgC.6Pk2FIo2I7u4EdLNuyKG', '67101491bb939-moi 2.jpg', '2024-10-14 14:30:50'),
(10, 'franck vidal', '$2y$10$bnefOASdAIywnRans.nxh.dJHJ5dWdy5OmVlEhkk2S2PnvFVJjMtW', NULL, '2024-10-16 21:25:52'),
(11, 'toto', '', '6717455a57bef-Capture d\'écran 2024-06-05 141326.png', '2024-10-22 08:22:13');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `billets`
--
ALTER TABLE `billets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `commentaires_ibfk_2` (`id_billet`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `billets`
--
ALTER TABLE `billets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `billets`
--
ALTER TABLE `billets`
  ADD CONSTRAINT `billets_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `commentaires_ibfk_2` FOREIGN KEY (`id_billet`) REFERENCES `billets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
