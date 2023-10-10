
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
  `video` longblob DEFAULT NULL,
  `datareparacao` date DEFAULT NULL,
  `reparacao` longtext DEFAULT NULL,
  `rep_efectuada_por` varchar(255) DEFAULT NULL,
  `problema_resolvido` varchar(20) DEFAULT NULL,
  `ano_letivo` varchar(20) NOT NULL,
  `periodo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

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



-- --------------------------------------------------------

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
  `observacoes` varchar(255) DEFAULT NULL,
  `escola_digital` varchar(5) NOT NULL DEFAULT 'Não',
  `num_inv_dgest` varchar(50) DEFAULT NULL,
  `fornecedor` varchar(100) DEFAULT NULL,
  `email_fornecedor` varchar(50) DEFAULT NULL,
  `nif_pessoa` int(9) DEFAULT NULL,
  `num_rma` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Estrutura da tabela `equip_requisitado`
--

CREATE TABLE `equip_requisitado` (
  `id_req` int(11) NOT NULL,
  `id_equip` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Estrutura da tabela `escolas`
--

CREATE TABLE `escolas` (
  `id` int(11) NOT NULL,
  `nome_escola` varchar(100) NOT NULL,
  `morada` varchar(255) NOT NULL,
  `codigopostal` varchar(8) NOT NULL,
  `localidade` varchar(100) NOT NULL,
  `telefone` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
-- Estrutura da tabela `outro_equipamento`
--

CREATE TABLE `outro_equipamento` (
  `id` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `nomeoutro` varchar(255) NOT NULL,
  `qta` int(4) NOT NULL,
  `observacoes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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
-- Estrutura da tabela `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `email_user` varchar(255) NOT NULL,
  `pass` blob NOT NULL,
  `email_smtp` varchar(255) NOT NULL,
  `email_smtpport` int(11) NOT NULL,
  `nome_app` varchar(255) NOT NULL,
  `sessao_timeout` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
-- Estrutura da tabela `tipos_equipamento`
--

CREATE TABLE `tipos_equipamento` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Estrutura da tabela `tipos_manutencao`
--

CREATE TABLE `tipos_manutencao` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipo` int(11) NOT NULL DEFAULT 2 COMMENT '1-adm  2-geral  3-reparador  4- funcionário',
  `pass` blob NOT NULL,
  `sessao_ativa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `tipo`, `pass`, `sessao_ativa`) VALUES
(87, 'Administrador', 'geimasterdb@hotmail.com', 1, 0x41579e3d233189c2e2c3af45c10c96c5, 0),
(94, 'mig', 'migarper@gmail.com', 3, 0xcb7ca0cdd6dc32e707fd14e4f71e5417, 0),
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
ALTER TABLE `avarias_reparacoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `equipamento`
--
ALTER TABLE `equipamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `equip_requisitado`
--
ALTER TABLE `equip_requisitado`
  ADD PRIMARY KEY (`id_req`,`id_equip`);

--
-- Índices para tabela `escolas`
--
ALTER TABLE `escolas`
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
-- Índices para tabela `outro_equipamento`
--
ALTER TABLE `outro_equipamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `periodos`
--
ALTER TABLE `periodos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `requisicao`
--
ALTER TABLE `requisicao`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Índices para tabela `tarefas`
--
ALTER TABLE `tarefas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `tipos_manutencao`
--
ALTER TABLE `tipos_manutencao`
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
-- AUTO_INCREMENT de tabela `avarias_reparacoes`
--
ALTER TABLE `avarias_reparacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=408;

--
-- AUTO_INCREMENT de tabela `equipamento`
--
ALTER TABLE `equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;

--
-- AUTO_INCREMENT de tabela `escolas`
--
ALTER TABLE `escolas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `logotipo`
--
ALTER TABLE `logotipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `manutencao`
--
ALTER TABLE `manutencao`
  MODIFY `codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1162;

--
-- AUTO_INCREMENT de tabela `outro_equipamento`
--
ALTER TABLE `outro_equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `periodos`
--
ALTER TABLE `periodos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de tabela `requisicao`
--
ALTER TABLE `requisicao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `salas`
--
ALTER TABLE `salas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de tabela `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tarefas`
--
ALTER TABLE `tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `tipos_equipamento`
--
ALTER TABLE `tipos_equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `tipos_manutencao`
--
ALTER TABLE `tipos_manutencao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=344;
COMMIT;

