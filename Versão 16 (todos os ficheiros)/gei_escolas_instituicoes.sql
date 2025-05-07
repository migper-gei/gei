
--
-- Banco de dados: `gei_escolas_instituicoes`
--
create database gei_escolas_instituicoes;
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

--
-- Índices para tabela `settingsbd`
--
ALTER TABLE `settingsbd`
  ADD PRIMARY KEY (`codigo`);
COMMIT;

