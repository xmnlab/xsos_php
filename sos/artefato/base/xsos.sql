CREATE DATABASE xsos;

USE xsos;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Atendente`
--

DROP TABLE IF EXISTS `Atendente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Atendente` (
  `ateId` int(11) NOT NULL AUTO_INCREMENT,
  `ateNome` varchar(60) DEFAULT NULL,
  `ateLogin` varchar(10) DEFAULT NULL,
  `ateSenha` varchar(10) DEFAULT NULL,
  `ateAtivo` int(11) DEFAULT '1',
  `ateGrupo` varchar(10) NOT NULL DEFAULT 'publico',
  PRIMARY KEY (`ateId`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Cliente`
--

DROP TABLE IF EXISTS `Cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cliente` (
  `cliId` double NOT NULL AUTO_INCREMENT,
  `cliNome` varchar(60) DEFAULT NULL,
  `cliCnpj` varchar(19) DEFAULT NULL,
  `cliCpf` varchar(11) DEFAULT NULL,
  `cliRg` varchar(9) DEFAULT NULL,
  `cliOrgaoemissorrg` varchar(10) DEFAULT NULL,
  `cliEndereco` varchar(60) DEFAULT NULL,
  `cliBairro` varchar(25) DEFAULT NULL,
  `cliCidade` varchar(30) DEFAULT NULL,
  `cliUf` char(2) DEFAULT NULL,
  `cliCep` char(8) DEFAULT NULL,
  `cliFone1` varchar(15) DEFAULT NULL,
  `cliFone2` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`cliId`)
) ENGINE=InnoDB AUTO_INCREMENT=10323 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EstadoOs`
--

DROP TABLE IF EXISTS `EstadoOs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EstadoOs` (
  `etoId` int(11) NOT NULL AUTO_INCREMENT,
  `etoNome` varchar(60) NOT NULL,
  PRIMARY KEY (`etoId`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Marca`
--

DROP TABLE IF EXISTS `Marca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Marca` (
  `marId` int(11) NOT NULL AUTO_INCREMENT,
  `marNome` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`marId`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Modelo`
--

DROP TABLE IF EXISTS `Modelo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Modelo` (
  `modId` double NOT NULL AUTO_INCREMENT,
  `modNome` varchar(20) DEFAULT NULL,
  `modMarca` int(11) DEFAULT NULL,
  `modImagem` mediumblob,
  PRIMARY KEY (`modId`),
  KEY `fk_modelo_marca` (`modMarca`),
  CONSTRAINT `fk_modelo_marca` FOREIGN KEY (`modMarca`) REFERENCES `Marca` (`marId`)
) ENGINE=InnoDB AUTO_INCREMENT=1312 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Operadora`
--

DROP TABLE IF EXISTS `Operadora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Operadora` (
  `opeId` int(11) NOT NULL AUTO_INCREMENT,
  `opeNome` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`opeId`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Os`
--

DROP TABLE IF EXISTS `Os`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Os` (
  `osxId` double NOT NULL AUTO_INCREMENT,
  `osxCliente` double DEFAULT NULL,
  `osxNumeroHexadecimalAparelho` varchar(15) DEFAULT NULL,
  `osxNumeroLinhaAparelho` varchar(15) DEFAULT NULL,
  `osxPlanoAparelho` varchar(3) DEFAULT NULL,
  `osxOperadoraAparelho` int(11) DEFAULT NULL,
  `osxModeloAparelho` double DEFAULT NULL,
  `osxDtAbertura` datetime DEFAULT NULL,
  `osxDtEntregaPrevista` datetime DEFAULT NULL,
  `osxDtEntrega` datetime DEFAULT NULL,
  `osxEstado` char(3) DEFAULT NULL,
  `osxAtendente` int(11) DEFAULT NULL,
  `osxObsCliente` text,
  `osxObsTecnica` text,
  `osxDescricaoServico` text,
  `osxValorServico` float(7,2) DEFAULT '0.00',
  `osxEstadoAparelho` text,
  `osxCustoMaterial` float(7,2) DEFAULT '0.00',
  PRIMARY KEY (`osxId`),
  KEY `fk_os_cliente` (`osxCliente`),
  KEY `fk_os_modelo` (`osxModeloAparelho`),
  KEY `fk_os_atendente` (`osxAtendente`),
  KEY `fk_os_operadora` (`osxOperadoraAparelho`),
  CONSTRAINT `fk_os_atendente` FOREIGN KEY (`osxAtendente`) REFERENCES `Atendente` (`ateId`),
  CONSTRAINT `fk_os_cliente` FOREIGN KEY (`osxCliente`) REFERENCES `Cliente` (`cliId`),
  CONSTRAINT `fk_os_modelo` FOREIGN KEY (`osxModeloAparelho`) REFERENCES `Modelo` (`modId`),
  CONSTRAINT `fk_os_operadora` FOREIGN KEY (`osxOperadoraAparelho`) REFERENCES `Operadora` (`opeId`)
) ENGINE=InnoDB AUTO_INCREMENT=14700 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Produto`
--

DROP TABLE IF EXISTS `Produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Produto` (
  `proId` int(11) NOT NULL,
  `proFabricante` int(11) DEFAULT NULL,
  `proTipoProduto` int(11) NOT NULL,
  `proDescricao` varchar(60) NOT NULL,
  `proCodigoBarra` varchar(60) DEFAULT NULL,
  `proQtde` int(11) NOT NULL DEFAULT '0',
  `proQtdeMin` int(11) NOT NULL DEFAULT '0',
  `proValor` float NOT NULL DEFAULT '0',
  `proModelo` int(11) DEFAULT NULL,
  PRIMARY KEY (`proId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProdutoLocalizacao`
--

DROP TABLE IF EXISTS `ProdutoLocalizacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProdutoLocalizacao` (
  `plcProduto` int(11) NOT NULL,
  `plcLocalizacao` int(11) NOT NULL,
  PRIMARY KEY (`plcProduto`,`plcLocalizacao`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-06-05 19:13:30
