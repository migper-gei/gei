
--
-- Table structure for table `equipamento`
--

CREATE TABLE `equipamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomeequi` varchar(50) NOT NULL,
  `numserie` varchar(50) DEFAULT NULL,
  `id_sala` int NOT NULL,
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
  `tecladointerface` varchar(45) DEFAULT NULL,
  `rato` varchar(50) DEFAULT NULL,
  `ratointerface` varchar(45) DEFAULT NULL,
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
  `nif_pessoa` varchar(9) DEFAULT NULL,
  `num_rma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_sala_equipamento_idx` (`id_sala`),
  CONSTRAINT `fk_id_sala_equipamento` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=utf8mb3;
