
CREATE TABLE `utilizadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipo` int NOT NULL DEFAULT '2' COMMENT '1-adm  2-geral  3-reparador  4- funcionário',
  `pass` blob NOT NULL,
  `sessao_ativa` int NOT NULL DEFAULT '0',
  `dataalteracaopass` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=344 DEFAULT CHARSET=utf8mb3;
