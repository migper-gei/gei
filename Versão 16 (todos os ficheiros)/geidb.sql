

--
-- Banco de dados: `gei_escolas_instituicoes`
--
create database gei_escolas_instituicoes;
-- --------------------------------------------------------

use gei_escolas_instituicoes;

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



INSERT INTO settingsbd (codigo, nome_esc_inst, email, contato, nomebd, serverbd) 
VALUES
(123456, 'AE escola...', 'escola@gmail.com', 123456789, 'geidb', 'localhost');



--------------------------------------------------------------------------------------------------------

create database geidb;
use geidb;

--
-- Table structure for table `escolas`
--

DROP TABLE IF EXISTS `escolas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `escolas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome_escola` varchar(100) NOT NULL,
  `morada` varchar(255) DEFAULT NULL,
  `codigopostal` varchar(8) DEFAULT NULL,
  `localidade` varchar(100) DEFAULT NULL,
  `telefone` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `escolas`
--

LOCK TABLES `escolas` WRITE;
/*!40000 ALTER TABLE `escolas` DISABLE KEYS */;
INSERT INTO `escolas` VALUES (1,'AE abc','rua 123','2222-223','Leiria',212333333);
/*!40000 ALTER TABLE `escolas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logotipo`
--

DROP TABLE IF EXISTS `logotipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logotipo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logotipo` blob,
  `nomeescola` varchar(250) NOT NULL,
  `site` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logotipo`
--

LOCK TABLES `logotipo` WRITE;
/*!40000 ALTER TABLE `logotipo` DISABLE KEYS */;
INSERT INTO `logotipo` VALUES (1,NULL,'AE abc','www.ae.pt');
/*!40000 ALTER TABLE `logotipo` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `salas`
--

DROP TABLE IF EXISTS `salas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `localizacao` varchar(50) DEFAULT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `id_escola` int NOT NULL,
  `equip_requisitavel` varchar(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_escola_salas_idx` (`id_escola`),
  CONSTRAINT `fk_id_escola_salas` FOREIGN KEY (`id_escola`) REFERENCES `escolas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salas`
--

LOCK TABLES `salas` WRITE;
/*!40000 ALTER TABLE `salas` DISABLE KEYS */;
/*!40000 ALTER TABLE `salas` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `equipamento`
--

DROP TABLE IF EXISTS `equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `nif_pessoa` int DEFAULT NULL,
  `num_rma` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_sala_equipamento_idx` (`id_sala`),
  CONSTRAINT `fk_id_sala_equipamento` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamento`
--

LOCK TABLES `equipamento` WRITE;
/*!40000 ALTER TABLE `equipamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipamento` ENABLE KEYS */;
UNLOCK TABLES;




DROP TABLE IF EXISTS `avarias_reparacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avarias_reparacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_equi` int NOT NULL,
  `id_sala` int NOT NULL,
  `id_escola` int NOT NULL,
  `autoravaria` varchar(255) NOT NULL,
  `dataavaria` date NOT NULL,
  `avaria` longtext NOT NULL,
  `imgavaria` longblob,
  `video` longblob,
  `datareparacao` date DEFAULT NULL,
  `reparacao` longtext,
  `rep_efectuada_por` varchar(255) DEFAULT NULL,
  `problema_resolvido` varchar(20) DEFAULT NULL,
  `ano_letivo` varchar(20) NOT NULL,
  `periodo` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_equi_avarep_idx` (`id_equi`),
  KEY `fk_id_sala_avarep_idx` (`id_sala`),
  KEY `fk_id_escola_avarep_idx` (`id_escola`),
  CONSTRAINT `fk_id_equi_avarep` FOREIGN KEY (`id_equi`) REFERENCES `equipamento` (`id`),
  CONSTRAINT `fk_id_escola_avarep` FOREIGN KEY (`id_escola`) REFERENCES `escolas` (`id`),
  CONSTRAINT `fk_id_sala_avarep` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avarias_reparacoes`
--

LOCK TABLES `avarias_reparacoes` WRITE;
/*!40000 ALTER TABLE `avarias_reparacoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `avarias_reparacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_message`
--

DROP TABLE IF EXISTS `chat_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_message` (
  `chat_message_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `from_user_id` int NOT NULL,
  `chat_message` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int NOT NULL,
  PRIMARY KEY (`chat_message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_message`
--

LOCK TABLES `chat_message` WRITE;
/*!40000 ALTER TABLE `chat_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equip_requisitado`
--

DROP TABLE IF EXISTS `equip_requisitado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equip_requisitado` (
  `id_req` int NOT NULL,
  `id_equip` int NOT NULL,
  PRIMARY KEY (`id_req`,`id_equip`),
  KEY `fk_id_equi_requisitado_idx` (`id_equip`),
  CONSTRAINT `fk_id_equi_requisitado` FOREIGN KEY (`id_equip`) REFERENCES `equipamento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equip_requisitado`
--

LOCK TABLES `equip_requisitado` WRITE;
/*!40000 ALTER TABLE `equip_requisitado` DISABLE KEYS */;
/*!40000 ALTER TABLE `equip_requisitado` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `manutencao`
--

DROP TABLE IF EXISTS `manutencao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manutencao` (
  `codigo` int NOT NULL AUTO_INCREMENT,
  `id_equi` int NOT NULL,
  `data_manutencao` date NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `pessoa` varchar(50) NOT NULL,
  `observacoes` varchar(255) NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `fk_id_equi_manutencao_idx` (`id_equi`),
  CONSTRAINT `fk_id_equi_manutencao` FOREIGN KEY (`id_equi`) REFERENCES `equipamento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manutencao`
--

LOCK TABLES `manutencao` WRITE;
/*!40000 ALTER TABLE `manutencao` DISABLE KEYS */;
/*!40000 ALTER TABLE `manutencao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outro_equipamento`
--

DROP TABLE IF EXISTS `outro_equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outro_equipamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_sala` int NOT NULL,
  `nomeoutro` varchar(255) NOT NULL,
  `qta` int NOT NULL,
  `observacoes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_sala_oe_idx` (`id_sala`),
  CONSTRAINT `fk_id_sala_oe` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outro_equipamento`
--

LOCK TABLES `outro_equipamento` WRITE;
/*!40000 ALTER TABLE `outro_equipamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `outro_equipamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `periodos`
--

DROP TABLE IF EXISTS `periodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `periodos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ano_lectivo` varchar(20) NOT NULL,
  `num_periodo` int NOT NULL DEFAULT '0',
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `periodos`
--

LOCK TABLES `periodos` WRITE;
/*!40000 ALTER TABLE `periodos` DISABLE KEYS */;
/*!40000 ALTER TABLE `periodos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requisicao`
--

DROP TABLE IF EXISTS `requisicao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requisicao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_util` varchar(100) NOT NULL,
  `datarequi` date DEFAULT NULL,
  `datautil` date NOT NULL,
  `horainicio` time NOT NULL,
  `horafim` time NOT NULL,
  `id_sala` int NOT NULL,
  `dataentrega` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_sala_requisicao_idx` (`id_sala`),
  CONSTRAINT `fk_id_sala_requisicao` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requisicao`
--

LOCK TABLES `requisicao` WRITE;
/*!40000 ALTER TABLE `requisicao` DISABLE KEYS */;
/*!40000 ALTER TABLE `requisicao` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email_user` varchar(255) NOT NULL,
  `pass` blob NOT NULL,
  `email_smtp` varchar(255) NOT NULL,
  `email_smtpport` int NOT NULL,
  `nome_app` varchar(255) NOT NULL,
  `sessao_timeout` int NOT NULL,
  `tempoduracaopass` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarefas`
--

DROP TABLE IF EXISTS `tarefas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarefas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_escola` int NOT NULL,
  `id_sala` int NOT NULL,
  `descricao` longtext NOT NULL,
  `urgencia` varchar(10) NOT NULL,
  `criado_por` varchar(100) NOT NULL,
  `data_criacao` date NOT NULL,
  `concluido_por` varchar(100) DEFAULT NULL,
  `data_conclusao` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_id_escola_tarefas_idx` (`id_escola`),
  KEY `fk_id_sala_tarefas_idx` (`id_sala`),
  CONSTRAINT `fk_id_escola_tarefas` FOREIGN KEY (`id_escola`) REFERENCES `escolas` (`id`),
  CONSTRAINT `fk_id_sala_tarefas` FOREIGN KEY (`id_sala`) REFERENCES `salas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarefas`
--

LOCK TABLES `tarefas` WRITE;
/*!40000 ALTER TABLE `tarefas` DISABLE KEYS */;
/*!40000 ALTER TABLE `tarefas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_equipamento`
--

DROP TABLE IF EXISTS `tipos_equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_equipamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_equipamento`
--

LOCK TABLES `tipos_equipamento` WRITE;
/*!40000 ALTER TABLE `tipos_equipamento` DISABLE KEYS */;
INSERT INTO `tipos_equipamento` VALUES (1,'PC'),(2,'Portátil'),(3,'Impressora de rede'),(4,'Impressora local (USB)'),(5,'Videoprojector'),(6,'Router'),(7,'Quadro Interactivo'),(8,'Switch'),(9,'Access Point'),(10,'Repetidor'),(11,'Scanner'),(12,'UPS'),(13,'Tablet'),(14,'Placas Arduino '),(15,'NAS'),(16,'Máquina fotográfica');
/*!40000 ALTER TABLE `tipos_equipamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_manutencao`
--

DROP TABLE IF EXISTS `tipos_manutencao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_manutencao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_manutencao`
--

LOCK TABLES `tipos_manutencao` WRITE;
/*!40000 ALTER TABLE `tipos_manutencao` DISABLE KEYS */;
INSERT INTO `tipos_manutencao` VALUES (1,'Formatação'),(2,'Eliminação de contas / ficheiros / virus'),(3,'Eliminação adware / spyware'),(4,'Instalação / atualização software'),(5,'Instalação / atualização hardware'),(6,'Limpeza rato'),(7,'Limpeza teclado'),(8,'Limpeza interior');
/*!40000 ALTER TABLE `tipos_manutencao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilizadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tipo` int NOT NULL DEFAULT '2' COMMENT '1-adm  2-geral  3-reparador  4- funcionário',
  `pass` blob NOT NULL,
  `sessao_ativa` int NOT NULL DEFAULT '0',
  `dataalteracaopass` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizadores`
--

LOCK TABLES `utilizadores` WRITE;
/*!40000 ALTER TABLE `utilizadores` DISABLE KEYS */;
INSERT INTO `utilizadores` (`id`, `nome`, `email`, `tipo`, `pass`, `sessao_ativa`) VALUES
(1, 'Administrador', 'admin@escola.pt', 1,AES_ENCRYPT('Admin+123abc', 'secret'), 0);
/*!40000 ALTER TABLE `utilizadores` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

