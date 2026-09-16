-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql304.infinityfree.com
-- Tempo de geração: 16/09/2026 às 08:15
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
-- Banco de dados: `if0_42268756_abs_control`
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
(0, 'admin', '098123');

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
(2, '1andar', 10),
(3, '2andar', 10),
(4, '3andar', 30);

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
(0, '0', 3, '2026-09-14 07:03:43'),
(0, '0', 3, '2026-09-14 07:11:50'),
(0, '1', 3, '2026-09-14 07:27:30'),
(0, '0', 2, '2026-09-14 07:27:46'),
(0, '3', 9, '2026-09-14 07:29:23'),
(0, '0', 1, '2026-09-14 07:35:58'),
(0, '1', 3, '2026-09-15 04:10:15'),
(0, '2', 5, '2026-09-15 04:10:32'),
(0, '0', 4, '2026-09-15 04:51:13'),
(0, '1', 4, '2026-09-15 04:52:42'),
(0, '0', 2, '2026-09-15 05:52:23');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
