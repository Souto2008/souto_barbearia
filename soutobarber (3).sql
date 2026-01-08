-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08-Jan-2026 às 22:49
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `soutobarber`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `contactos`
--

CREATE TABLE `contactos` (
  `id` int(11) NOT NULL,
  `nome` varchar(120) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `assunto` varchar(150) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `enviado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `marcacoes`
--

CREATE TABLE `marcacoes` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) DEFAULT NULL,
  `servico_id` int(11) DEFAULT NULL,
  `data_hora_inicio` datetime DEFAULT NULL,
  `data_hora_fim` datetime DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendente',
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `nome` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `marcacoes`
--

INSERT INTO `marcacoes` (`id`, `utilizador_id`, `servico_id`, `data_hora_inicio`, `data_hora_fim`, `estado`, `criado_em`, `nome`) VALUES
(1, 6, 11, '2026-01-16 11:30:00', '2026-01-16 12:20:00', 'cancelada', '2026-01-06 16:54:19', 'Gonçalo Souto'),
(2, 6, 4, '2026-01-14 12:15:00', '2026-01-14 12:55:00', 'cancelada', '2026-01-06 16:58:09', 'Gonçalo Souto'),
(3, 6, 5, '2026-01-23 14:30:00', '2026-01-23 14:45:00', 'cancelada', '2026-01-06 17:53:31', 'Gonçalo Souto');

--
-- Acionadores `marcacoes`
--
DELIMITER $$
CREATE TRIGGER `insert_nome_marcacoes` BEFORE INSERT ON `marcacoes` FOR EACH ROW BEGIN
    DECLARE nomeUser VARCHAR(255);

    SELECT nome INTO nomeUser
    FROM utilizadores
    WHERE id = NEW.utilizador_id;

    SET NEW.nome = nomeUser;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `assunto` varchar(200) DEFAULT NULL,
  `mensagem` text DEFAULT NULL,
  `enviado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `texto` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `servicos`
--

CREATE TABLE `servicos` (
  `servico_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `duracao_minutos` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `servicos`
--

INSERT INTO `servicos` (`servico_id`, `nome`, `descricao`, `duracao_minutos`, `preco`, `ativo`) VALUES
(1, 'Corte com único pente', 'Corte de cabelo feito apenas com um pente e máquina.', 15, 6.00, 1),
(2, 'Corte social', 'Corte de cabelo social clássico.', 25, 8.00, 1),
(3, 'Corte clássico com tesoura', 'Corte de cabelo clássico realizado com tesoura.', 35, 9.00, 1),
(4, 'Corte em degradé', 'Corte de cabelo com efeito degradê (fade).', 40, 10.00, 1),
(5, 'Barba com único pente', 'Aparar barba com máquina, utilizando apenas um pente.', 15, 4.00, 1),
(6, 'Contornos com máquina', 'Aparar e definir os contornos da barba com máquina.', 20, 5.00, 1),
(7, 'Corte aparado com navalha e toalha', 'Aparar e fazer a barba com navalha, incluindo toalha quente/fria.', 35, 7.00, 1),
(8, 'Corte tradicional navalhado', 'Barbear completo e tradicional com navalha.', 35, 8.00, 1),
(9, 'Combo: Corte ú. pente + Barba ú. pente', 'Corte de cabelo com único pente e Barba com único pente.', 40, 8.00, 1),
(10, 'Combo: Corte social + Barba com navalha', 'Corte de cabelo social e Barba com navalha.', 50, 11.00, 1),
(11, 'Combo: Corte degradé + Barba com máquina', 'Corte de cabelo em degradê e Contornos da barba com máquina.', 50, 12.00, 1),
(12, 'Combo: Corte degradé + Barba com navalha', 'Corte de cabelo em degradê e Barba com navalha.', 55, 13.00, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL DEFAULT 'cliente',
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `password_hash`, `tipo`, `telefone`) VALUES
(5, 'André Souto', 'andregsouto99@gmail.com', '$2y$10$0b3KNIhvw0bhg7VrhOtN4eyae/IkCaWdMnIZZh0cfPQdMGj2BqMCK', 'barbeiro', '925009222'),
(6, 'Gonçalo Souto', 'goncalosouto17@gmail.com', '$2y$10$MTNJ1IhMPtSt9o6dBKNp6.Z7c7zG22hIHQNkSqZaaa1Oy.CmxMS8a', 'cliente', '913854787'),
(9, 'Joao', 'joaomartins@gmail.com', '$2y$10$/AaSRiEtVDSTuTVusdbeYOufKYASowZu35hgTSDwh/O8qBHL3jsGe', 'cliente', '912567876'),
(10, 'Alex Cruz', 'a22066@eshm.edu.pt', '$2y$10$r0OtdgXGxubx4OMFnrEZKuCapszfWRmUIxCkQXAbAXRlvBpq.5AJ2', 'cliente', '967553201');

-- --------------------------------------------------------

--
-- Estrutura da tabela `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `usado` tinyint(1) DEFAULT 0,
  `data_expiracao` date NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para vista `v_marcacoes`
-- (Veja abaixo para a view atual)
--
CREATE TABLE `v_marcacoes` (
);

-- --------------------------------------------------------

--
-- Estrutura para vista `v_marcacoes`
--
DROP TABLE IF EXISTS `v_marcacoes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_marcacoes`  AS SELECT `m`.`id` AS `id`, `m`.`utilizador_id` AS `utilizador_id`, `m`.`data` AS `data`, `m`.`hora` AS `hora`, `m`.`criado_em` AS `criado_em`, `u`.`nome` AS `nome_utilizador` FROM (`marcacoes` `m` join `utilizadores` `u` on(`m`.`utilizador_id` = `u`.`id`)) ;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `marcacoes`
--
ALTER TABLE `marcacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_marcacoes_utilizador` (`utilizador_id`);

--
-- Índices para tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilizador_id` (`utilizador_id`);

--
-- Índices para tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilizador_id` (`utilizador_id`);

--
-- Índices para tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`servico_id`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `utilizador_id` (`utilizador_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `marcacoes`
--
ALTER TABLE `marcacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `servico_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `marcacoes`
--
ALTER TABLE `marcacoes`
  ADD CONSTRAINT `fk_marcacoes_utilizador` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `marcacoes_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`);

--
-- Limitadores para a tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`);

--
-- Limitadores para a tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`);

--
-- Limitadores para a tabela `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `vouchers_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
