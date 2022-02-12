--
-- Banco de dados: `geidb`
--
CREATE DATABASE IF NOT EXISTS `geidb_v2` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `geidb_v2`;
-- --------------------------------------------------------

--
-- Estrutura da tabela `avaria_reparacao`
--

CREATE TABLE `avaria_reparacao` (
  `id` int NOT NULL,
  `nomeequi` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sala` varchar(255) NOT NULL,
  `autoravaria` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `dataavaria` date NOT NULL,
  `avaria` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `imgavaria` longblob,
  `video` longblob,
  `datareparacao` date DEFAULT NULL,
  `reparacao` longtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `rep_efectuada_por` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ano_letivo` varchar(20) NOT NULL,
  `periodo` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `chat_message`
--

CREATE TABLE `chat_message` (
  `chat_message_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `from_user_id` int NOT NULL,
  `chat_message` text CHARACTER SET latin1 NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Estrutura da tabela `equipamento`
--

CREATE TABLE `equipamento` (
  `id` int NOT NULL,
  `nomeequi` varchar(50) NOT NULL,
  `numserie` varchar(50) DEFAULT NULL,
  `sala` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `marca_modelo` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tipo` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `processador` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `memoria` varchar(10) DEFAULT NULL,
  `disco` varchar(10) DEFAULT NULL,
  `placagrafica` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `placasom` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `placarede` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `monitor` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `teclado` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `rato` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `colunas` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `cd_dvd` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `dominio` varchar(50) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `mascara_rede` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `gateway` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `dns_principal` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `dns_alternativo` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sala_temp` varchar(255) DEFAULT NULL,
  `data_compra` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Estrutura da tabela `logotipo`
--

CREATE TABLE `logotipo` (
  `id` int NOT NULL,
  `logotipo` blob NOT NULL,
  `nomeescola` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `site` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `logotipo`
--

INSERT INTO `logotipo` (`id`, `logotipo`, `nomeescola`, `site`) VALUES
(29, '', 'AE ....', 'http://www.escola.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `manutencao`
--

CREATE TABLE `manutencao` (
  `codigo` int NOT NULL,
  `nomeequi` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sala` varchar(200) NOT NULL,
  `data_manutencao` date NOT NULL,
  `descricao` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `pessoa` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Estrutura da tabela `periodos`
--

CREATE TABLE `periodos` (
  `id` int NOT NULL,
  `ano_lectivo` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `num_periodo` int NOT NULL DEFAULT '0',
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `periodos`
--

INSERT INTO `periodos` (`id`, `ano_lectivo`, `num_periodo`, `data_inicio`, `data_fim`) VALUES
(73, '2020/2021', 1, '2020-08-01', '2020-12-31'),
(74, '2020/2021', 2, '2021-01-01', '2021-04-05'),
(75, '2020/2021', 3, '2021-04-06', '2021-08-31');

-- --------------------------------------------------------

--
-- Estrutura da tabela `salas`
--

CREATE TABLE `salas` (
  `id` int NOT NULL,
  `nome` varchar(50) NOT NULL,
  `localizacao` varchar(50) DEFAULT NULL,
  `departamento` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Estrutura da tabela `tipos_equipamento`
--

CREATE TABLE `tipos_equipamento` (
  `id` int NOT NULL,
  `nome` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
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
(23, 'Tablet');

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tipo` int NOT NULL DEFAULT '2',
  `pass` blob NOT NULL,
  `sessao_ativa` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `tipo`, `pass`, `sessao_ativa`) VALUES
(1, 'User', 'userteste@escola.pt', 2, 0x6c19749f3daae859edb32b15a35339cb, 0),
(2, 'Administrador', 'adminteste@escola.pt', 1, 0x6d6a2dba8d50543f2c0ef186bca66f0e, 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `avaria_reparacao`
--
ALTER TABLE `avaria_reparacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `chat_message`
--
ALTER TABLE `chat_message`
  ADD PRIMARY KEY (`chat_message_id`);

--
-- Índices para tabela `equipamento`
--
ALTER TABLE `equipamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `logotipo`
--
ALTER TABLE `logotipo`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `manutencao`
--
ALTER TABLE `manutencao`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices para tabela `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaria_reparacao`
--
ALTER TABLE `avaria_reparacao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- AUTO_INCREMENT de tabela `chat_message`
--
ALTER TABLE `chat_message`
  MODIFY `chat_message_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `equipamento`
--
ALTER TABLE `equipamento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT de tabela `logotipo`
--
ALTER TABLE `logotipo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `manutencao`
--
ALTER TABLE `manutencao`
  MODIFY `codigo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1072;

--
-- AUTO_INCREMENT de tabela `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de tabela `salas`
--
ALTER TABLE `salas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=329;
COMMIT;

