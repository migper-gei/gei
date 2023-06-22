--
-- Banco de dados: `geidb`
--

-- --------------------------------------------------------

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
-- Índices para tabela `outro_equipamento`
--
ALTER TABLE `outro_equipamento`
  ADD PRIMARY KEY (`id`);



--
-- AUTO_INCREMENT de tabela `outro_equipamento`
--
ALTER TABLE `outro_equipamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;
