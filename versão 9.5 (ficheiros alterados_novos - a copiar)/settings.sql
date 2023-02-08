------------------------------------------------------

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


ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`) USING BTREE;


ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

