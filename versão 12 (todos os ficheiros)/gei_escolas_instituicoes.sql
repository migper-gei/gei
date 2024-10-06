-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06-Out-2024 às 11:24
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gei_escolas_instituicoes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `settingsbd`
--

CREATE TABLE `settingsbd` (
  `codigo` int(9) NOT NULL,
  `nome_esc_inst` varchar(200) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contato` int(9) NOT NULL,
  `nomebd` varchar(50) NOT NULL,
  `serverbd` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `settingsbd`
--

INSERT INTO `settingsbd` (`codigo`, `nome_esc_inst`, `email`, `contato`, `nomebd`, `serverbd`) VALUES
(123456, 'AE escola...', 'migarper@gmail.com', 123456789, 'geidb', 'localhost');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `settingsbd`
--
ALTER TABLE `settingsbd`
  ADD PRIMARY KEY (`codigo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
