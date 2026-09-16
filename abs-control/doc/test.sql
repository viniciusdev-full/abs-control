  phpMyAdmin SQL Dump
  version 5.2.1
  https://www.phpmyadmin.net/
 
  Host: 127.0.0.1
  Tempo de geração: 13/09/2026 às 22:41
  Versão do servidor: 10.4.32-MariaDB
  Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

 
  Banco de dados: `test`
 

                              

 
  Estrutura para tabela `admin`
 

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `senha` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

 
  Despejando dados para a tabela `admin`
 

INSERT INTO `admin` (`id`, `nome`, `senha`) VALUES
(1, 'admin', 'admin123');


                              

 
  Estrutura para tabela `estoque`
 

CREATE TABLE `estoque` (
  `id` int(11) NOT NULL,
  `andar` varchar(50) DEFAULT NULL,
  `qtd` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

 
  Despejando dados para a tabela `estoque`
 

INSERT INTO `estoque` (`id`, `andar`, `qtd`) VALUES
(1, 'terreo', 0),
(2, '1°andar', 0),
(3, '2°andar', 0),
(4, '3°andar', 0);

                              

 
  Estrutura para tabela `historico_uso`
 

CREATE TABLE `historico_uso` (
  `id` int(11) NOT NULL,
  `andar` varchar(30) NOT NULL,
  `estoque` int(11) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
