-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 13/10/2026 às 19:33
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

--
-- Despejando dados para a tabela `autor`
--

INSERT INTO `autor` (`id`, `nome`, `pseudonimo`, `data_nascimento`, `data_falecimento`, `nacionalidade`, `biografia`, `status`, `imagem`, `data_criacao`, `data_alteracao`) VALUES
(13, 'Edgar Allan Poe', NULL, '1809-01-19', '1849-10-07', 'Norte Americano', 'Edgar Allan Poe was an American writer, poet, editor and literary critic, considered part of the American Romantic Movement. Best known for his tales of mystery and the macabre, Poe was one of the earliest American practitioners of the short story and is considered the inventor of the detective-fiction genre. He is further credited with contributing to the emerging genre of science fiction. He was the first well-known American writer to try to earn a living through writing alone, resulting in a financially difficult life and career.', 1, 'edgar_allan_poe_circa_1849_restored_squared_off-17919094375157.jpg', '2026-10-13 16:37:17', NULL),
(14, 'Agatha Christie', NULL, '1890-09-15', '1976-01-12', 'Inglesa', 'Agatha Mary Clarissa Miller was born in Torquay, Devon, in the United Kingdom, the daughter of a wealthy American stockbroker. Her father died when she was eleven years old. Her mother taught her at home, encouraging her to write at a very young age. At the age of 16, she went to Mrs. Dryden\'s finishing school in Paris to study singing and piano. In 1914, at age 24, she married Colonel Archibald Christie, an aviator in the Royal Flying Corps. While he went away to war, she worked as a nurse and wrote her first novel, The Mysterious Affair at Styles (1920), which wasn\'t published until four years later. When her husband came back from the war, they had a daughter. In 1928 she divorced her husband, who had been having an affair. In 1930, she married Sir Max Mallowan, an archaeologist and a Catholic. She was happy in the early years of her second marriage, and did not divorce her husband despite his many affairs. She travelled with her husband\'s job, and set several of her novels set in the Middle East. Most of her other novels were set in a fictionalized Devon, where she was born.\r\n\r\nAgatha Christie is credited with developing the \"cozy style\" of mystery, which became popular in, and ultimately defined, the Golden Age of fiction in England in the 1920s and \'30s, an age of which she is considered to have been Queen. In all, she wrote over 66 novels, numerous short stories and screenplays, and a series of romantic novels using the pen name Mary Westmacott. She was the single most popular mystery writer of all time. In 1971 she was made a Dame Commander of the Order of the British Empire.', 1, 'images-17919097586392.jpg', '2026-10-13 16:42:38', NULL),
(15, 'Arthur Conan Doyle', NULL, '1859-05-22', '1930-07-07', 'Escocês', 'Sir Arthur Ignatius Conan Doyle KStJ, DL (22 May 1859 – 7 July 1930) was a Scottish writer and physician, most noted for creating the fictional detective Sherlock Holmes and writing stories about him which are generally considered milestones in the field of crime fiction.\r\n\r\nHe is also known for writing the fictional adventures of a second character he invented, Professor Challenger, and for popularising the mystery of the Mary Celeste. He was a prolific writer whose other works include fantasy and science fiction stories, plays, romances, poetry, non-fiction and historical novels.<sup>[1][1]</sup>\r\n\r\n\r\n  [1]: https://en.wikipedia.org/wiki/Arthur_Conan_Doyle', 1, 'arthurconandoyle300412bb0eb-17919098514821.jpg', '2026-10-13 16:44:11', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `autor_genero`
--

CREATE TABLE `autor_genero` (
  `autor` int(11) UNSIGNED NOT NULL,
  `genero` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `autor_genero`
--

INSERT INTO `autor_genero` (`autor`, `genero`) VALUES
(13, 9),
(13, 12),
(13, 8),
(13, 11),
(14, 10),
(14, 12),
(15, 10),
(15, 12);

-- --------------------------------------------------------

--
-- Estrutura para tabela `genero`
--

CREATE TABLE `genero` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'Identificador do gênero',
  `genero_pai` int(11) UNSIGNED DEFAULT NULL COMMENT 'Identificador do gênero pai para o subgênero',
  `nome` varchar(255) NOT NULL COMMENT 'Nome do gênero',
  `descricao` text DEFAULT NULL COMMENT 'Descrição do gênero',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de criação do gênero',
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Data de alteração do gênero'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Gêneros literários';

--
-- Despejando dados para a tabela `genero`
--

INSERT INTO `genero` (`id`, `genero_pai`, `nome`, `descricao`, `data_criacao`, `data_alteracao`) VALUES
(1, NULL, 'Ficção', 'Narrativas criadas pela imaginação do autor, podendo conter elementos baseados em aspectos da realidade, mas essencialmente inventadas.', '2026-10-07 21:04:10', NULL),
(2, 1, 'Ficção Científica', 'Histórias fundamentadas em avanços científicos, tecnológicos, exploração espacial, viagens no tempo e cenários futuristas.', '2026-10-07 21:04:10', NULL),
(3, 2, 'Cyberpunk', 'Subgênero de ficção científica focado em uma combinação de alta tecnologia e baixa qualidade de vida (high tech, low life).', '2026-10-07 21:04:10', NULL),
(4, 2, 'Space Opera', 'Aventuras épicas ambientadas no espaço sideral, envolvendo impérios galácticos, batalhas e exploração de planetas.', '2026-10-07 21:04:10', NULL),
(5, 1, 'Fantasia', 'Narrativas que incluem elementos mágicos, criaturas sobrenaturais, mundos inventados e leis físicas próprias.', '2026-10-07 21:04:10', NULL),
(6, 5, 'Alta Fantasia', 'Histórias ambientadas em um mundo inteiramente fictício (secundário), com foco em grandes batalhas entre o bem e o mal.', '2026-10-07 21:04:10', NULL),
(7, 5, 'Fantasia Urbana', 'Narrativas onde a magia e elementos fantásticos coexistem em uma cidade moderna e contemporânea.', '2026-10-07 21:04:10', NULL),
(8, 1, 'Terror / Horror', 'Obras projetadas para provocar sentimentos de medo, suspense, repulsa ou pavor no leitor.', '2026-10-07 21:04:10', NULL),
(9, 8, 'Horror Cósmico', 'Foco no desamparo humano diante de forças divinas, alienígenas ou incompreensíveis para a mente humana.', '2026-10-07 21:04:10', NULL),
(10, 1, 'Mistério e Policial', 'Tramas focadas na resolução de um crime, mistério ou segredo por um detetive, investigador ou amador.', '2026-10-07 21:04:10', NULL),
(11, 10, 'Thriller Psicológico', 'Histórias de mistério que enfatizam o estado mental e emocional instável dos personagens.', '2026-10-07 21:04:10', NULL),
(12, 1, 'Romance', 'Narrativas focadas nas relações amorosas e no desenvolvimento afetivo entre os personagens centrais.', '2026-10-07 21:04:10', NULL),
(13, 12, 'Romance Histórico', 'Tramas amorosas ambientadas em um período histórico específico do passado, integrando fatos ou costumes reais.', '2026-10-07 21:04:10', NULL),
(14, NULL, 'Não Ficção', 'Obras baseadas inteiramente em fatos reais, análises, documentos ou memórias.', '2026-10-07 21:04:10', NULL),
(15, 14, 'Biografia e Autobiografia', 'Relato detalhado da vida de uma pessoa, escrito por terceiros ou pela própria pessoa.', '2026-10-07 21:04:10', NULL),
(16, 14, 'Ensaios e Filosofia', 'Textos reflexivos que discutem ideias, conceitos sociais, políticos, morais ou existenciais.', '2026-10-07 21:04:10', NULL),
(17, 14, 'História', 'Análise documental e narrativa de eventos, sociedades e figuras do passado.', '2026-10-07 21:04:10', NULL),
(18, NULL, 'Poesia', 'Texto estruturado em versos, estrofes e ritmo, priorizando a estética da linguagem e a expressão de sentimentos.', '2026-10-07 21:04:10', NULL),
(19, 18, 'Lírica', 'Poesia focada na expressão íntima de sentimentos, subjetividade e emoções pessoais.', '2026-10-07 21:04:10', NULL),
(20, 18, 'Épica', 'Poesia narrativa que narra grandes feitos de heróis ou eventos de importância histórica e mitológica.', '2026-10-07 21:04:10', NULL),
(21, NULL, 'Dramaturgia', 'Textos escritos especificamente para serem encenados no teatro ou adaptados para performances.', '2026-10-07 21:04:10', NULL),
(22, 21, 'Tragédia', 'Peça dramática que aborda o sofrimento humano e conflitos intensos, geralmente culminando em um final fatídico.', '2026-10-07 21:04:10', NULL),
(23, 21, 'Comédia', 'Texto dramático com tom satírico, bem-humorado ou absurdo, destinado a provocar o riso.', '2026-10-07 21:04:10', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro`
--

CREATE TABLE `livro` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `ano_publicacao` smallint(5) UNSIGNED DEFAULT NULL,
  `quantidade_disponivel` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `imagem` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de criação do autor',
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Data de alteração do autor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `livro`
--

INSERT INTO `livro` (`id`, `titulo`, `isbn`, `ano_publicacao`, `quantidade_disponivel`, `status`, `imagem`, `data_criacao`, `data_alteracao`) VALUES
(3, 'O Gato Preto', '9798749663044', 2021, 1, 1, '710he3baftl._sy466_-1791909655386.jpg', '2026-10-13 16:40:55', NULL),
(4, 'Assassinato no Expresso do Oriente', '9788520912447', 2002, 1, 1, 'images-17919099385357.jpg', '2026-10-13 16:45:38', NULL),
(5, 'Um Estudo em Vermelho', '8525408115', 1998, 1, 1, 'capa13201532-17919102287693.jpg', '2026-10-13 16:50:29', NULL),
(6, 'A Queda da Casa de Usher', '9788533908611', 1839, 1, 1, 'images-17919117877508.jpg', '2026-10-13 17:16:27', '2026-10-13 17:32:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro_autor`
--

CREATE TABLE `livro_autor` (
  `livro` int(10) UNSIGNED NOT NULL,
  `autor` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `livro_autor`
--

INSERT INTO `livro_autor` (`livro`, `autor`) VALUES
(5, 15),
(6, 13),
(4, 14),
(3, 13);

-- --------------------------------------------------------

--
-- Estrutura para tabela `livro_genero`
--

CREATE TABLE `livro_genero` (
  `livro` int(10) UNSIGNED NOT NULL,
  `genero` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `livro_genero`
--

INSERT INTO `livro_genero` (`livro`, `genero`) VALUES
(5, 10),
(6, 8),
(4, 10),
(3, 8);

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
-- Índices de tabela `livro`
--
ALTER TABLE `livro`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `livro_autor`
--
ALTER TABLE `livro_autor`
  ADD KEY `fk_livro_autor_livro1_idx` (`livro`),
  ADD KEY `fk_livro_autor_autor1_idx` (`autor`);

--
-- Índices de tabela `livro_genero`
--
ALTER TABLE `livro_genero`
  ADD KEY `fk_livro_genero_livro1_idx` (`livro`),
  ADD KEY `fk_livro_genero_genero1_idx` (`genero`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `autor`
--
ALTER TABLE `autor`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador do autor', AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `genero`
--
ALTER TABLE `genero`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Identificador do gênero', AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `livro`
--
ALTER TABLE `livro`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `autor_genero`
--
ALTER TABLE `autor_genero`
  ADD CONSTRAINT `fk_autor_genero_autor1` FOREIGN KEY (`autor`) REFERENCES `autor` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_autor_genero_genero1` FOREIGN KEY (`genero`) REFERENCES `genero` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Restrições para tabelas `genero`
--
ALTER TABLE `genero`
  ADD CONSTRAINT `fk_genero_genero` FOREIGN KEY (`genero_pai`) REFERENCES `genero` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Restrições para tabelas `livro_autor`
--
ALTER TABLE `livro_autor`
  ADD CONSTRAINT `fk_livro_autor_autor1` FOREIGN KEY (`autor`) REFERENCES `autor` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_livro_autor_livro1` FOREIGN KEY (`livro`) REFERENCES `livro` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Restrições para tabelas `livro_genero`
--
ALTER TABLE `livro_genero`
  ADD CONSTRAINT `fk_livro_genero_genero1` FOREIGN KEY (`genero`) REFERENCES `genero` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_livro_genero_livro1` FOREIGN KEY (`livro`) REFERENCES `livro` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
