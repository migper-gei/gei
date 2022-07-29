-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23-Maio-2022 às 14:11
-- Versão do servidor: 10.4.22-MariaDB
-- versão do PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE DATABASE IF NOT EXISTS `geidb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `geidb`;
--
--
-- Banco de dados: `geidb`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `avarias_reparacoes`
--

CREATE TABLE `avarias_reparacoes` (
  `id` int(11) NOT NULL,
  `id_equi` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `id_escola` int(11) NOT NULL,
  `autoravaria` varchar(255) NOT NULL,
  `dataavaria` date NOT NULL,
  `avaria` longtext NOT NULL,
  `imgavaria` longblob DEFAULT NULL,
  `video` longblob NOT NULL,
  `datareparacao` date DEFAULT NULL,
  `reparacao` longtext DEFAULT NULL,
  `rep_efectuada_por` varchar(255) DEFAULT NULL,
  `problema_resolvido` varchar(20) DEFAULT NULL,
  `ano_letivo` varchar(20) NOT NULL,
  `periodo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `avarias_reparacoes`
--

--
-- Estrutura da tabela `chat_message`
--

CREATE TABLE `chat_message` (
  `chat_message_id` int(11) NOT NULL,
  `to_user_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `chat_message` text CHARACTER SET latin1 NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `chat_message`
--

--
-- Estrutura da tabela `equipamento`
--

CREATE TABLE `equipamento` (
  `id` int(11) NOT NULL,
  `nomeequi` varchar(50) NOT NULL,
  `numserie` varchar(50) DEFAULT NULL,
  `id_sala` int(11) NOT NULL,
  `marca_modelo` varchar(50) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `processador` varchar(50) DEFAULT NULL,
  `memoria` varchar(10) DEFAULT NULL,
  `disco` varchar(10) DEFAULT NULL,
  `placagrafica` varchar(50) DEFAULT NULL,
  `placasom` varchar(50) DEFAULT NULL,
  `placarede` varchar(50) DEFAULT NULL,
  `monitor` varchar(50) DEFAULT NULL,
  `teclado` varchar(50) DEFAULT NULL,
  `rato` varchar(50) DEFAULT NULL,
  `colunas` varchar(10) DEFAULT NULL,
  `cd_dvd` varchar(10) DEFAULT NULL,
  `dominio` varchar(50) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `mascara_rede` varchar(15) DEFAULT NULL,
  `gateway` varchar(15) DEFAULT NULL,
  `dns_principal` varchar(15) DEFAULT NULL,
  `dns_alternativo` varchar(15) DEFAULT NULL,
  `sala_temp` varchar(255) DEFAULT NULL,
  `data_compra` date DEFAULT NULL,
  `observacoes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `equipamento`
--

--
-- Estrutura da tabela `equip_requisitado`
--

CREATE TABLE `equip_requisitado` (
  `id_req` int(11) NOT NULL,
  `id_equip` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `equip_requisitado`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `escolas`
--

CREATE TABLE `escolas` (
  `id` int(11) NOT NULL,
  `nome_escola` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `escolas`
--

INSERT INTO `escolas` (`id`, `nome_escola`) VALUES
(1, 'AE...........');

-- --------------------------------------------------------

--
-- Estrutura da tabela `logotipo`
--

CREATE TABLE `logotipo` (
  `id` int(11) NOT NULL,
  `logotipo` blob NOT NULL,
  `nomeescola` varchar(250) NOT NULL,
  `site` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `logotipo`
--

INSERT INTO `logotipo` (`id`, `logotipo`, `nomeescola`, `site`) VALUES
(31, '', 'AE...........', 'http://www.escola.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `manutencao`
--

CREATE TABLE `manutencao` (
  `codigo` int(11) NOT NULL,
  `id_equi` int(11) NOT NULL,
  `data_manutencao` date NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `pessoa` varchar(50) NOT NULL,
  `observacoes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `manutencao`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `periodos`
--

CREATE TABLE `periodos` (
  `id` int(11) NOT NULL,
  `ano_lectivo` varchar(20) NOT NULL,
  `num_periodo` int(11) NOT NULL DEFAULT 0,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `periodos`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `requisicao`
--

CREATE TABLE `requisicao` (
  `id` int(11) NOT NULL,
  `email_util` varchar(100) NOT NULL,
  `datarequi` date DEFAULT NULL,
  `datautil` date NOT NULL,
  `horainicio` time NOT NULL,
  `horafim` time NOT NULL,
  `id_sala` int(11) NOT NULL,
  `dataentrega` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `requisicao`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `salas`
--

CREATE TABLE `salas` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `localizacao` varchar(50) DEFAULT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `id_escola` int(11) NOT NULL,
  `equip_requisitavel` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `salas`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `tarefas`
--

CREATE TABLE `tarefas` (
  `id` int(11) NOT NULL,
  `id_escola` int(5) NOT NULL,
  `id_sala` int(5) NOT NULL,
  `descricao` longtext NOT NULL,
  `urgencia` varchar(10) NOT NULL,
  `criado_por` varchar(100) NOT NULL,
  `data_criacao` date NOT NULL,
  `concluido_por` varchar(100) DEFAULT NULL,
  `data_conclusao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tarefas`
--


--
-- Estrutura da tabela `tipos_equipamento`
--

CREATE TABLE `tipos_equipamento` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `tipos_equipamento`
--

INSERT INTO `tipos_equipamento` (`id`, `nome`) VALUES
(1, 'PC'),
(2, 'Portátil'),
(3, 'Impressora de rede'),
(4, 'Impressora local (USB)'),
(5, 'Videoprojector'),
(6, 'Router'),
(7, 'Quadro Interactivo'),
(9, 'Switch'),
(10, 'Acess Point'),
(11, 'Repetidor'),
(13, 'Scanner'),
(14, 'UPS'),
(23, 'Tablet'),
(24, 'Placas Arduino'),
(25, 'tipo');

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipo` int(11) NOT NULL DEFAULT 2,
  `pass` blob NOT NULL,
  `sessao_ativa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `utilizadores`
--


INSERT INTO `utilizadores` (`id`, `nome`, `email`, `tipo`, `pass`, `sessao_ativa`) VALUES
(325, 'Administrador ', 'adminteste@escola.pt', 1, 0x6d6a2dba8d50543f2c0ef186bca66f0e, 0),
(326, 'User', 'userteste@escola.pt', 2, 0x6c19749f3daae859edb32b15a35339cb, 0),
(327, 'User_reparador', 'repar@escola.pt', 3, 0xf42a7653959e8de73e505477c6bc641c, 0),
(328, 'Funcionário', 'func1@escola.pt', 4, 0x5e4a763bb07e23adb84105e8bf0ec3df, 0);


--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `avarias_reparacoes`
--
ALTER TABLE `avarias_reparacoes` ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `equipamento`
--
ALTER TABLE `equipamento`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `equip_requisitado`
--
ALTER TABLE `equip_requisitado`   ADD PRIMARY KEY (`id_req`,`id_equip`);

--
-- Índices para tabela `escolas`
--
ALTER TABLE `escolas`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `logotipo`
--
ALTER TABLE `logotipo`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `manutencao`
--
ALTER TABLE `manutencao`   ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `periodos`
--
ALTER TABLE `periodos`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `requisicao`
--
ALTER TABLE `requisicao`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `salas`
--
ALTER TABLE `salas`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tarefas`
--
ALTER TABLE `tarefas`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`   ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`   ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avarias_reparacoes`
--
ALTER TABLE `avarias_reparacoes` 
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT de tabela `equipamento`
--
ALTER TABLE `equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT de tabela `escolas`
--
ALTER TABLE `escolas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `logotipo`
--
ALTER TABLE `logotipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `manutencao`
--
ALTER TABLE `manutencao`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1123;

--
-- AUTO_INCREMENT de tabela `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT de tabela `requisicao`
--
ALTER TABLE `requisicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `salas`
--
ALTER TABLE `salas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de tabela `tarefas`
--
ALTER TABLE `tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;
COMMIT;