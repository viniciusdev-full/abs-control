-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql304.infinityfree.com
-- Tempo de geração: 06/10/2026 às 09:02
-- Versão do servidor: 11.4.13-MariaDB
-- Versão do PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `if0_42268767_test`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `senha` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `admin`
--

INSERT INTO `admin` (`id`, `nome`, `senha`) VALUES
(1, 'admin', '123admin123'),
(0, 'jj', '123'),
(0, 'admin', '098123'),
(0, 'admin', '098123'),
(0, 'admin', '123');

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `andar` varchar(50) DEFAULT NULL,
  `qtd` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`id`, `andar`, `qtd`) VALUES
(1, 'terreo', 10),
(2, '1andar', 6),
(3, '2andar', 2),
(4, '3andar', 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_uso`
--

CREATE TABLE `historico_uso` (
  `id` int(11) NOT NULL,
  `andar` varchar(30) NOT NULL,
  `estoque` int(11) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `historico_uso`
--

INSERT INTO `historico_uso` (`id`, `andar`, `estoque`, `data`) VALUES
(1, '3', 2, '2026-10-06 05:17:40'),
(2, '2', 1, '2026-10-06 05:20:30'),
(3, '1', 4, '2026-10-06 05:21:14'),
(4, '1', 2, '2026-10-06 05:24:42'),
(5, '1', 4, '2026-10-06 05:25:46'),
(6, '1', 2, '2026-10-06 05:37:56'),
(7, '1', 1, '2026-10-06 05:39:28'),
(8, '1', 1, '2026-10-06 05:40:19');

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico_uso`
--
ALTER TABLE `historico_uso`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `historico_uso`
--
ALTER TABLE `historico_uso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
