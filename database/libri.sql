-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 14/09/2026 às 14:36
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `libri`
--

CREATE DATABASE libri
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE libri;

-- --------------------------------------------------------

--
-- Estrutura para tabela `autor`
--

CREATE TABLE `autor` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'Identificador do autor',
  `nome` varchar(255) NOT NULL COMMENT 'Nome do autor',
  `pseudonimo` varchar(255) DEFAULT NULL COMMENT 'Pseudônimo do autor',
  `data_nascimento` date DEFAULT NULL COMMENT 'Data de nascimento do autor',
  `data_falecimento` date DEFAULT NULL COMMENT 'Data de falecimento do autor',
  `nacionalidade` varchar(255) DEFAULT NULL COMMENT 'Nacionalidade do autor',
  `biografia` text DEFAULT NULL COMMENT 'Biografia do autor',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status do autor',
  `imagem` varchar(255) DEFAULT NULL COMMENT 'Imagem do autor',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de criação do autor',
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Data de alteração do autor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Tabela de Autores';

-- --------------------------------------------------------

--
-- Estrutura para tabela `autor_genero`
--

CREATE TABLE `autor_genero` (
  `autor` int(11) UNSIGNED NOT NULL,
  `genero` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `genero`
--

CREATE TABLE `genero` (
  `id` int(11) NOT NULL COMMENT 'Identificador do gênero',
  `genero_pai` int(11) DEFAULT NULL COMMENT 'Identificador do gênero pai para o subgênero',
  `nome` varchar(255) NOT NULL COMMENT 'Nome do gênero',
  `descricao` text DEFAULT NULL COMMENT 'Descrição do gênero',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de criação do gênero',
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Data de alteração do gênero'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Gêneros literários';

--
-- Despejando dados para a tabela `genero`
--

INSERT INTO `genero` (`id`, `genero_pai`, `nome`, `descricao`, `data_criacao`, `data_alteracao`) VALUES
(1, NULL, 'Ficção Científica (Sci-Fi)', 'Narrativas baseadas em impactos científicos, tecnológicos ou avanços futuristas.', '2026-09-12 21:40:28', NULL),
(2, NULL, 'Fantasia', 'Histórias que envolvem magia, criaturas míticas e mundos imaginários fora da nossa realidade.', '2026-09-12 21:40:47', NULL),
(3, NULL, 'Romance', 'Narrativas centradas em relacionamentos amorosos e nos conflitos emocionais dos protagonistas.', '2026-09-12 21:41:07', NULL),
(4, NULL, 'Mistério e Thriller', 'Focado em investigação de crimes, enigmas e situações de alto risco com constante tensão.', '2026-09-12 21:41:31', NULL),
(5, NULL, 'Horror e Terror', 'Narrativas projetadas para causar medo, desconforto, tensão ou repulsa no leitor.', '2026-09-12 21:41:56', NULL),
(6, 4, 'Thriller Psicológico', 'Histórias de suspense centradas no estado mental instável ou manipulador dos personagens.', '2026-09-12 21:42:15', NULL),
(7, 4, 'Policial (Procedural)', 'Focado na rotina detalhada e nas técnicas de investigação da polícia ou detetives.', '2026-09-12 21:42:42', NULL),
(8, 1, 'Distopia', 'Sociedades futuras sob regimes totalitários ou cenários pós-colapso socioambiental.', '2026-09-12 22:14:18', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `autor_genero`
--
ALTER TABLE `autor_genero`
  ADD KEY `fk_autor_genero_autor1_idx` (`autor`),
  ADD KEY `fk_autor_genero_genero1_idx` (`genero`);

--
-- Índices de tabela `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_genero_genero_idx` (`genero_pai`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `autor`
--
ALTER TABLE `autor`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador do autor', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `genero`
--
ALTER TABLE `genero`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador do gênero', AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `autor_genero`
--
ALTER TABLE `autor_genero`
  ADD CONSTRAINT `fk_autor_genero_autor1` FOREIGN KEY (`autor`) REFERENCES `autor` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_autor_genero_genero1` FOREIGN KEY (`genero`) REFERENCES `genero` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `genero`
--
ALTER TABLE `genero`
  ADD CONSTRAINT `fk_genero_genero` FOREIGN KEY (`genero_pai`) REFERENCES `genero` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
