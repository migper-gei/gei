

CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_user` varchar(255) NOT NULL,
  `pass` blob NOT NULL,
  `email_smtp` varchar(255) NOT NULL,
  `email_smtpport` int NOT NULL,
  `nome_app` varchar(255) NOT NULL,
  `sessao_timeout` int NOT NULL,
  `tempoduracaopass` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

