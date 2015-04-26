SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `sigeproc` DEFAULT CHARACTER SET utf8 ;
CREATE SCHEMA IF NOT EXISTS `sigeproc2` DEFAULT CHARACTER SET utf8 ;
USE `sigeproc` ;

-- -----------------------------------------------------
-- Table `sigeproc`.`TipoSetor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`TipoSetor` (
  `idTipoSetor` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idTipoSetor`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Secretaria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Secretaria` (
  `idSecretaria` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `sigla` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`idSecretaria`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC),
  UNIQUE INDEX `sigla_UNIQUE` (`sigla` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`PostoDeTrabalho`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`PostoDeTrabalho` (
  `idPostoDeTrabalho` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `sigla` VARCHAR(10) NOT NULL,
  `colunaDiscriminatoria` VARCHAR(45) NULL,
  PRIMARY KEY (`idPostoDeTrabalho`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Setor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Setor` (
  `idPostoDeTrabalho` INT NOT NULL,
  `arquivo` TINYINT(1) NOT NULL DEFAULT 0,
  `Setor_idSetor` INT NULL,
  `TipoSetor_idTipoSetor` INT NOT NULL,
  `Secretaria_idSecretaria` INT NOT NULL,
  INDEX `fk_Setor_TipoSetor1` (`TipoSetor_idTipoSetor` ASC),
  INDEX `fk_Setor_Secretaria1` (`Secretaria_idSecretaria` ASC),
  INDEX `fk_Setor_PostoDeTrabalho1_idx` (`idPostoDeTrabalho` ASC),
  PRIMARY KEY (`idPostoDeTrabalho`),
  CONSTRAINT `fk_Setor_TipoSetor1`
    FOREIGN KEY (`TipoSetor_idTipoSetor`)
    REFERENCES `sigeproc`.`TipoSetor` (`idTipoSetor`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Setor_Secretaria1`
    FOREIGN KEY (`Secretaria_idSecretaria`)
    REFERENCES `sigeproc`.`Secretaria` (`idSecretaria`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Setor_PostoDeTrabalho1`
    FOREIGN KEY (`idPostoDeTrabalho`)
    REFERENCES `sigeproc`.`PostoDeTrabalho` (`idPostoDeTrabalho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Assunto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Assunto` (
  `idAssunto` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `descricao` TEXT NOT NULL,
  `Setor_idPostoDeTrabalho` INT NOT NULL,
  PRIMARY KEY (`idAssunto`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC),
  INDEX `fk_Assunto_Setor1_idx` (`Setor_idPostoDeTrabalho` ASC),
  CONSTRAINT `fk_Assunto_Setor1`
    FOREIGN KEY (`Setor_idPostoDeTrabalho`)
    REFERENCES `sigeproc`.`Setor` (`idPostoDeTrabalho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Apenso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Apenso` (
  `idApenso` INT NOT NULL AUTO_INCREMENT,
  `dataInicio` DATETIME NOT NULL,
  `dataFim` DATETIME NULL,
  `Processo_idProcesso` INT NOT NULL,
  PRIMARY KEY (`idApenso`),
  INDEX `fk_Apenso_Processo1` (`Processo_idProcesso` ASC),
  CONSTRAINT `fk_Apenso_Processo1`
    FOREIGN KEY (`Processo_idProcesso`)
    REFERENCES `sigeproc`.`Processo` (`idProcesso`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Telefone`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Telefone` (
  `idTelefone` INT NOT NULL AUTO_INCREMENT,
  `ddd` INT NOT NULL,
  `numero` INT NOT NULL,
  PRIMARY KEY (`idTelefone`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`TipoDocumento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`TipoDocumento` (
  `idTipoDocumento` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`idTipoDocumento`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Documento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Documento` (
  `idDocumento` INT NOT NULL AUTO_INCREMENT,
  `numero` VARCHAR(45) NOT NULL,
  `digito` INT NULL,
  `dataEmissao` DATE NULL,
  `orgaoEmissor` VARCHAR(150) NULL,
  `TipoDocumento_idTipoDocumento` INT NOT NULL,
  PRIMARY KEY (`idDocumento`),
  INDEX `fk_Documento_TipoDocumento1` (`TipoDocumento_idTipoDocumento` ASC),
  CONSTRAINT `fk_Documento_TipoDocumento1`
    FOREIGN KEY (`TipoDocumento_idTipoDocumento`)
    REFERENCES `sigeproc`.`TipoDocumento` (`idTipoDocumento`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Requerente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Requerente` (
  `idRequerente` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `Setor_idSetor` INT NULL,
  `Telefone_idTelefone` INT NOT NULL,
  `Documento_idDocumento` INT NOT NULL,
  PRIMARY KEY (`idRequerente`),
  INDEX `fk_Requerente_Telefone1_idx` (`Telefone_idTelefone` ASC),
  INDEX `fk_Requerente_Documento1_idx` (`Documento_idDocumento` ASC),
  INDEX `requerente_UNIQUE` (`nome` ASC, `Setor_idSetor` ASC, `Telefone_idTelefone` ASC, `Documento_idDocumento` ASC),
  CONSTRAINT `fk_Requerente_Telefone1`
    FOREIGN KEY (`Telefone_idTelefone`)
    REFERENCES `sigeproc`.`Telefone` (`idTelefone`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Requerente_Documento1`
    FOREIGN KEY (`Documento_idDocumento`)
    REFERENCES `sigeproc`.`Documento` (`idDocumento`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`StatusProcesso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`StatusProcesso` (
  `idStatusProcesso` INT NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(255) NULL,
  `nome` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idStatusProcesso`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Funcao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Funcao` (
  `idFuncao` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`idFuncao`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Usuario` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `matricula` INT NOT NULL,
  `digitoMatricula` INT NULL DEFAULT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(100) NOT NULL,
  `dataCriacao` DATE NOT NULL,
  `ativo` TINYINT(1) NOT NULL,
  `Funcao_idFuncao` INT NOT NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC),
  INDEX `fk_Usuario_Funcao1` (`Funcao_idFuncao` ASC),
  UNIQUE INDEX `matricula_UNIQUE` (`matricula` ASC, `digitoMatricula` ASC),
  CONSTRAINT `fk_Usuario_Funcao1`
    FOREIGN KEY (`Funcao_idFuncao`)
    REFERENCES `sigeproc`.`Funcao` (`idFuncao`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Processo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Processo` (
  `idProcesso` INT NOT NULL AUTO_INCREMENT,
  `numero` INT NULL,
  `anoExercicio` INT NOT NULL,
  `imagem` VARCHAR(255) NULL,
  `dataAbertura` DATE NOT NULL,
  `volume` INT NOT NULL,
  `Assunto_idAssunto` INT NOT NULL,
  `Apenso_idApenso` INT NULL,
  `Requerente_idRequerente` INT NOT NULL,
  `StatusProcesso_idStatusProcesso` INT NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  `PostoDeTrabalho_idPostoDeTrabalho` INT NULL,
  PRIMARY KEY (`idProcesso`),
  INDEX `fk_Processo_Assunto` (`Assunto_idAssunto` ASC),
  INDEX `fk_Processo_Apenso1` (`Apenso_idApenso` ASC),
  INDEX `fk_Processo_Requerente1` (`Requerente_idRequerente` ASC),
  INDEX `fk_Processo_StatusProcesso1` (`StatusProcesso_idStatusProcesso` ASC),
  INDEX `fk_Processo_Usuario1` (`Usuario_idUsuario` ASC),
  UNIQUE INDEX `numero_UNIQUE` (`numero` ASC, `anoExercicio` ASC),
  INDEX `fk_Processo_PostoDeTrabalho1_idx` (`PostoDeTrabalho_idPostoDeTrabalho` ASC),
  CONSTRAINT `fk_Processo_Assunto`
    FOREIGN KEY (`Assunto_idAssunto`)
    REFERENCES `sigeproc`.`Assunto` (`idAssunto`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Processo_Apenso1`
    FOREIGN KEY (`Apenso_idApenso`)
    REFERENCES `sigeproc`.`Apenso` (`idApenso`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Processo_Requerente1`
    FOREIGN KEY (`Requerente_idRequerente`)
    REFERENCES `sigeproc`.`Requerente` (`idRequerente`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Processo_StatusProcesso1`
    FOREIGN KEY (`StatusProcesso_idStatusProcesso`)
    REFERENCES `sigeproc`.`StatusProcesso` (`idStatusProcesso`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Processo_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `sigeproc`.`Usuario` (`idUsuario`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_Processo_PostoDeTrabalho1`
    FOREIGN KEY (`PostoDeTrabalho_idPostoDeTrabalho`)
    REFERENCES `sigeproc`.`PostoDeTrabalho` (`idPostoDeTrabalho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`GuiaDeRemessa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`GuiaDeRemessa` (
  `idGuiaDeRemessa` INT NOT NULL AUTO_INCREMENT,
  `numero` INT NULL,
  `anoExercicio` INT NOT NULL,
  `dataCriacao` DATETIME NOT NULL,
  `dataRecebimento` DATETIME NULL,
  `rejeitada` TINYINT(1) NOT NULL DEFAULT 0,
  `Emissor_idUsuario` INT NOT NULL,
  `PostoDeTrabalho_idPostoDeTrabalho` INT NOT NULL,
  `Recebedor_idUsuario` INT NULL,
  PRIMARY KEY (`idGuiaDeRemessa`),
  INDEX `fk_GuiaDeRemessa_Usuario1` (`Emissor_idUsuario` ASC),
  UNIQUE INDEX `numero_UNIQUE` (`numero` ASC, `anoExercicio` ASC),
  INDEX `fk_GuiaDeRemessa_PostoDeTrabalho1_idx` (`PostoDeTrabalho_idPostoDeTrabalho` ASC),
  INDEX `fk_GuiaDeRemessa_Usuario2_idx` (`Recebedor_idUsuario` ASC),
  CONSTRAINT `fk_GuiaDeRemessa_Usuario1`
    FOREIGN KEY (`Emissor_idUsuario`)
    REFERENCES `sigeproc`.`Usuario` (`idUsuario`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_GuiaDeRemessa_PostoDeTrabalho1`
    FOREIGN KEY (`PostoDeTrabalho_idPostoDeTrabalho`)
    REFERENCES `sigeproc`.`PostoDeTrabalho` (`idPostoDeTrabalho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_GuiaDeRemessa_Usuario2`
    FOREIGN KEY (`Recebedor_idUsuario`)
    REFERENCES `sigeproc`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Estado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Estado` (
  `idEstado` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `abreviacao` VARCHAR(2) NOT NULL,
  PRIMARY KEY (`idEstado`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Cidade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Cidade` (
  `idCidade` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `Estado_idEstado` INT NOT NULL,
  PRIMARY KEY (`idCidade`),
  INDEX `fk_Cidade_Estado1` (`Estado_idEstado` ASC),
  CONSTRAINT `fk_Cidade_Estado1`
    FOREIGN KEY (`Estado_idEstado`)
    REFERENCES `sigeproc`.`Estado` (`idEstado`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Endereco`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Endereco` (
  `idEndereco` INT NOT NULL AUTO_INCREMENT,
  `logradouro` VARCHAR(200) NOT NULL,
  `numero` INT NULL,
  `complemento` VARCHAR(150) NULL,
  `bairro` VARCHAR(100) NOT NULL,
  `Cidade_idCidade` INT NOT NULL,
  PRIMARY KEY (`idEndereco`),
  INDEX `fk_Endereco_Cidade1` (`Cidade_idCidade` ASC),
  CONSTRAINT `fk_Endereco_Cidade1`
    FOREIGN KEY (`Cidade_idCidade`)
    REFERENCES `sigeproc`.`Cidade` (`idCidade`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`OrgaoExterno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`OrgaoExterno` (
  `idPostoDeTrabalho` INT NOT NULL,
  `Endereco_idEndereco` INT NOT NULL,
  INDEX `fk_OrgaoExterno_Endereco1` (`Endereco_idEndereco` ASC),
  UNIQUE INDEX `Endereco_idEndereco_UNIQUE` (`Endereco_idEndereco` ASC),
  INDEX `fk_OrgaoExterno_PostoDeTrabalho1_idx` (`idPostoDeTrabalho` ASC),
  PRIMARY KEY (`idPostoDeTrabalho`),
  CONSTRAINT `fk_OrgaoExterno_Endereco1`
    FOREIGN KEY (`Endereco_idEndereco`)
    REFERENCES `sigeproc`.`Endereco` (`idEndereco`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_OrgaoExterno_PostoDeTrabalho1`
    FOREIGN KEY (`idPostoDeTrabalho`)
    REFERENCES `sigeproc`.`PostoDeTrabalho` (`idPostoDeTrabalho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Workflow`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Workflow` (
  `idWorkflow` INT NOT NULL AUTO_INCREMENT,
  `descricao` TEXT NULL,
  `Assunto_idAssunto` INT NOT NULL,
  PRIMARY KEY (`idWorkflow`),
  INDEX `fk_Workflow_Assunto1` (`Assunto_idAssunto` ASC),
  CONSTRAINT `fk_Workflow_Assunto1`
    FOREIGN KEY (`Assunto_idAssunto`)
    REFERENCES `sigeproc`.`Assunto` (`idAssunto`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`FluxoPosto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`FluxoPosto` (
  `idFluxoPosto` INT NOT NULL AUTO_INCREMENT,
  `diasUteis` INT NULL,
  `descricao` VARCHAR(255) NULL,
  `Workflow_idWorkflow` INT NOT NULL,
  `PostoDeTrabalho_idPostoDeTrabalho` INT NOT NULL,
  PRIMARY KEY (`idFluxoPosto`),
  INDEX `fk_FluxoPosto_Workflow1` (`Workflow_idWorkflow` ASC),
  INDEX `fk_FluxoPosto_PostoDeTrabalho1_idx` (`PostoDeTrabalho_idPostoDeTrabalho` ASC),
  CONSTRAINT `fk_FluxoPosto_Workflow1`
    FOREIGN KEY (`Workflow_idWorkflow`)
    REFERENCES `sigeproc`.`Workflow` (`idWorkflow`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_FluxoPosto_PostoDeTrabalho1`
    FOREIGN KEY (`PostoDeTrabalho_idPostoDeTrabalho`)
    REFERENCES `sigeproc`.`PostoDeTrabalho` (`idPostoDeTrabalho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Pendencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Pendencia` (
  `idPendencia` INT NOT NULL AUTO_INCREMENT,
  `descricao` TEXT NOT NULL,
  `imagem` VARCHAR(255) NULL,
  `dataCriacao` DATETIME NOT NULL,
  `resolvido` TINYINT(1) NOT NULL DEFAULT 0,
  `dataConclusao` DATETIME NULL,
  `Processo_idProcesso` INT NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`idPendencia`),
  INDEX `fk_Pendencia_Processo1` (`Processo_idProcesso` ASC),
  INDEX `fk_Pendencia_Usuario1` (`Usuario_idUsuario` ASC),
  CONSTRAINT `fk_Pendencia_Processo1`
    FOREIGN KEY (`Processo_idProcesso`)
    REFERENCES `sigeproc`.`Processo` (`idProcesso`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Pendencia_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `sigeproc`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Parecer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Parecer` (
  `idParecer` INT NOT NULL AUTO_INCREMENT,
  `descricao` TEXT NOT NULL,
  `data` DATETIME NOT NULL,
  `Pendencia_idPendencia` INT NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`idParecer`),
  INDEX `fk_Parecer_Pendencia1` (`Pendencia_idPendencia` ASC),
  INDEX `fk_Parecer_Usuario1` (`Usuario_idUsuario` ASC),
  CONSTRAINT `fk_Parecer_Pendencia1`
    FOREIGN KEY (`Pendencia_idPendencia`)
    REFERENCES `sigeproc`.`Pendencia` (`idPendencia`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Parecer_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `sigeproc`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Recurso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Recurso` (
  `idRecurso` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  PRIMARY KEY (`idRecurso`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Permissao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Permissao` (
  `idPermissao` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `permitido` TINYINT(1) NOT NULL,
  `Recurso_idRecurso` INT NOT NULL,
  PRIMARY KEY (`idPermissao`),
  INDEX `fk_Permissao_Recurso1` (`Recurso_idRecurso` ASC),
  CONSTRAINT `fk_Permissao_Recurso1`
    FOREIGN KEY (`Recurso_idRecurso`)
    REFERENCES `sigeproc`.`Recurso` (`idRecurso`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`Log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Log` (
  `idLog` INT NOT NULL AUTO_INCREMENT,
  `timestamp` TIMESTAMP NOT NULL,
  `tipo` VARCHAR(45) NOT NULL,
  `mensagem` TEXT NOT NULL,
  PRIMARY KEY (`idLog`))
ENGINE = ARCHIVE;


-- -----------------------------------------------------
-- Table `sigeproc`.`Funcao_has_Permissao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`Funcao_has_Permissao` (
  `Funcao_idFuncao` INT NOT NULL,
  `Permissao_idPermissao` INT NOT NULL,
  PRIMARY KEY (`Funcao_idFuncao`, `Permissao_idPermissao`),
  INDEX `fk_Funcao_has_Permissao_Permissao1` (`Permissao_idPermissao` ASC),
  INDEX `fk_Funcao_has_Permissao_Funcao1` (`Funcao_idFuncao` ASC),
  CONSTRAINT `fk_Funcao_has_Permissao_Funcao1`
    FOREIGN KEY (`Funcao_idFuncao`)
    REFERENCES `sigeproc`.`Funcao` (`idFuncao`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_Funcao_has_Permissao_Permissao1`
    FOREIGN KEY (`Permissao_idPermissao`)
    REFERENCES `sigeproc`.`Permissao` (`idPermissao`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`GuiaDeRemessa_has_Processo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`GuiaDeRemessa_has_Processo` (
  `GuiaDeRemessa_idGuiaDeRemessa` INT NOT NULL,
  `Processo_idProcesso` INT NOT NULL,
  PRIMARY KEY (`GuiaDeRemessa_idGuiaDeRemessa`, `Processo_idProcesso`),
  INDEX `fk_GuiaDeRemessa_has_Processo_Processo1` (`Processo_idProcesso` ASC),
  INDEX `fk_GuiaDeRemessa_has_Processo_GuiaDeRemessa1` (`GuiaDeRemessa_idGuiaDeRemessa` ASC),
  CONSTRAINT `fk_GuiaDeRemessa_has_Processo_GuiaDeRemessa1`
    FOREIGN KEY (`GuiaDeRemessa_idGuiaDeRemessa`)
    REFERENCES `sigeproc`.`GuiaDeRemessa` (`idGuiaDeRemessa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_GuiaDeRemessa_has_Processo_Processo1`
    FOREIGN KEY (`Processo_idProcesso`)
    REFERENCES `sigeproc`.`Processo` (`idProcesso`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sigeproc`.`UsuarioSetor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc`.`UsuarioSetor` (
  `idUsuarioSetor` INT NOT NULL AUTO_INCREMENT,
  `dataLotacao` DATE NOT NULL,
  `dataSaida` DATE NULL,
  `Usuario_idUsuario` INT NOT NULL,
  `Setor_idPostoDeTrabalho` INT NOT NULL,
  INDEX `fk_Usuario_has_Setor_Usuario1` (`Usuario_idUsuario` ASC),
  PRIMARY KEY (`idUsuarioSetor`),
  INDEX `fk_UsuarioSetor_Setor1_idx` (`Setor_idPostoDeTrabalho` ASC),
  CONSTRAINT `fk_Usuario_has_Setor_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `sigeproc`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UsuarioSetor_Setor1`
    FOREIGN KEY (`Setor_idPostoDeTrabalho`)
    REFERENCES `sigeproc`.`Setor` (`idPostoDeTrabalho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `sigeproc2` ;

-- -----------------------------------------------------
-- Table `sigeproc2`.`Secretaria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Secretaria` (
  `idSecretaria` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `sigla` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idSecretaria`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`TipoSetor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`TipoSetor` (
  `idTipoSetor` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idTipoSetor`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Setor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Setor` (
  `idSetor` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `sigla` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Setor_idSetor` INT(11) NULL DEFAULT NULL,
  `TipoSetor_idTipoSetor` INT(11) NOT NULL,
  `Secretaria_idSecretaria` INT(11) NOT NULL,
  PRIMARY KEY (`idSetor`),
  INDEX `IDX_BAFFE2C3C6172334` (`Setor_idSetor` ASC),
  INDEX `IDX_BAFFE2C3B019EED3` (`TipoSetor_idTipoSetor` ASC),
  INDEX `IDX_BAFFE2C31E05DA90` (`Secretaria_idSecretaria` ASC),
  CONSTRAINT `FK_BAFFE2C31E05DA90`
    FOREIGN KEY (`Secretaria_idSecretaria`)
    REFERENCES `sigeproc2`.`Secretaria` (`idSecretaria`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `FK_BAFFE2C3B019EED3`
    FOREIGN KEY (`TipoSetor_idTipoSetor`)
    REFERENCES `sigeproc2`.`TipoSetor` (`idTipoSetor`),
  CONSTRAINT `FK_BAFFE2C3C6172334`
    FOREIGN KEY (`Setor_idSetor`)
    REFERENCES `sigeproc2`.`Setor` (`idSetor`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Funcao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Funcao` (
  `idFuncao` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idFuncao`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Usuario` (
  `idUsuario` INT(11) NOT NULL AUTO_INCREMENT,
  `matricula` INT(11) NOT NULL,
  `digitoMatricula` INT(11) NULL DEFAULT NULL,
  `nome` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `email` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `senha` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `dataCriacao` DATE NOT NULL,
  `ativo` TINYINT(1) NOT NULL,
  `Funcao_idFuncao` INT(11) NOT NULL,
  `Setor_idSetor` INT(11) NOT NULL,
  PRIMARY KEY (`idUsuario`),
  UNIQUE INDEX `UNIQ_EDD889C115DF1885` (`matricula` ASC),
  UNIQUE INDEX `UNIQ_EDD889C1E7927C74` (`email` ASC),
  INDEX `IDX_EDD889C18E77073A` (`Funcao_idFuncao` ASC),
  INDEX `IDX_EDD889C1C6172334` (`Setor_idSetor` ASC),
  CONSTRAINT `FK_EDD889C1C6172334`
    FOREIGN KEY (`Setor_idSetor`)
    REFERENCES `sigeproc2`.`Setor` (`idSetor`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `FK_EDD889C18E77073A`
    FOREIGN KEY (`Funcao_idFuncao`)
    REFERENCES `sigeproc2`.`Funcao` (`idFuncao`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Assunto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Assunto` (
  `idAssunto` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `descricao` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idAssunto`),
  UNIQUE INDEX `UNIQ_C422327C54BD530C` (`nome` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Requerente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Requerente` (
  `idRequerente` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Setor_idSetor` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idRequerente`),
  INDEX `IDX_75247112C6172334` (`Setor_idSetor` ASC),
  CONSTRAINT `FK_75247112C6172334`
    FOREIGN KEY (`Setor_idSetor`)
    REFERENCES `sigeproc2`.`Setor` (`idSetor`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`StatusProcesso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`StatusProcesso` (
  `idStatusProcesso` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(80) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idStatusProcesso`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Processo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Processo` (
  `idProcesso` INT(11) NOT NULL AUTO_INCREMENT,
  `numero` INT(11) NULL DEFAULT NULL,
  `anoExercicio` INT(11) NOT NULL,
  `dataAbertura` DATE NOT NULL,
  `dataEncerramento` DATE NULL DEFAULT NULL,
  `volume` INT(11) NOT NULL,
  `Assunto_idAssunto` INT(11) NOT NULL,
  `Apenso_idApenso` INT(11) NULL DEFAULT NULL,
  `Requerente_idRequerente` INT(11) NOT NULL,
  `StatusProcesso_idStatusProcesso` INT(11) NOT NULL,
  `Usuario_idUsuario` INT(11) NOT NULL,
  PRIMARY KEY (`idProcesso`),
  UNIQUE INDEX `numero_UNIQUE` (`numero` ASC, `anoExercicio` ASC),
  INDEX `IDX_EF93DA7B390992FE` (`Assunto_idAssunto` ASC),
  INDEX `IDX_EF93DA7B6CE17E43` (`Apenso_idApenso` ASC),
  INDEX `IDX_EF93DA7BAF476202` (`Requerente_idRequerente` ASC),
  INDEX `IDX_EF93DA7BC6442930` (`StatusProcesso_idStatusProcesso` ASC),
  INDEX `IDX_EF93DA7B95440347` (`Usuario_idUsuario` ASC),
  CONSTRAINT `FK_EF93DA7B95440347`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `sigeproc2`.`Usuario` (`idUsuario`),
  CONSTRAINT `FK_EF93DA7B390992FE`
    FOREIGN KEY (`Assunto_idAssunto`)
    REFERENCES `sigeproc2`.`Assunto` (`idAssunto`),
  CONSTRAINT `FK_EF93DA7B6CE17E43`
    FOREIGN KEY (`Apenso_idApenso`)
    REFERENCES `sigeproc2`.`Apenso` (`idApenso`),
  CONSTRAINT `FK_EF93DA7BAF476202`
    FOREIGN KEY (`Requerente_idRequerente`)
    REFERENCES `sigeproc2`.`Requerente` (`idRequerente`),
  CONSTRAINT `FK_EF93DA7BC6442930`
    FOREIGN KEY (`StatusProcesso_idStatusProcesso`)
    REFERENCES `sigeproc2`.`StatusProcesso` (`idStatusProcesso`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Apenso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Apenso` (
  `idApenso` INT(11) NOT NULL AUTO_INCREMENT,
  `dataInicio` DATETIME NOT NULL,
  `dataFim` DATETIME NULL DEFAULT NULL,
  `Processo_idProcesso` INT(11) NOT NULL,
  PRIMARY KEY (`idApenso`),
  INDEX `IDX_1BBA2F421AD5E10F` (`Processo_idProcesso` ASC),
  CONSTRAINT `FK_1BBA2F421AD5E10F`
    FOREIGN KEY (`Processo_idProcesso`)
    REFERENCES `sigeproc2`.`Processo` (`idProcesso`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Estado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Estado` (
  `idEstado` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `abreviacao` VARCHAR(2) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idEstado`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Cidade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Cidade` (
  `idCidade` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Estado_idEstado` INT(11) NOT NULL,
  PRIMARY KEY (`idCidade`),
  INDEX `IDX_6D34366A3C7FC40A` (`Estado_idEstado` ASC),
  CONSTRAINT `FK_6D34366A3C7FC40A`
    FOREIGN KEY (`Estado_idEstado`)
    REFERENCES `sigeproc2`.`Estado` (`idEstado`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`TipoDocumento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`TipoDocumento` (
  `idTipoDocumento` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idTipoDocumento`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Documento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Documento` (
  `idDocumento` INT(11) NOT NULL AUTO_INCREMENT,
  `numero` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `digito` INT(11) NULL DEFAULT NULL,
  `dataEmissao` DATE NULL DEFAULT NULL,
  `orgaoEmissor` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `Requerente_idRequerente` INT(11) NOT NULL,
  `TipoDocumento_idTipoDocumento` INT(11) NOT NULL,
  PRIMARY KEY (`idDocumento`),
  UNIQUE INDEX `UNIQ_3440AC64AF476202` (`Requerente_idRequerente` ASC),
  INDEX `IDX_3440AC64749E2DB` (`TipoDocumento_idTipoDocumento` ASC),
  CONSTRAINT `FK_3440AC64749E2DB`
    FOREIGN KEY (`TipoDocumento_idTipoDocumento`)
    REFERENCES `sigeproc2`.`TipoDocumento` (`idTipoDocumento`),
  CONSTRAINT `FK_3440AC64AF476202`
    FOREIGN KEY (`Requerente_idRequerente`)
    REFERENCES `sigeproc2`.`Requerente` (`idRequerente`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Endereco`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Endereco` (
  `idEndereco` INT(11) NOT NULL AUTO_INCREMENT,
  `logradouro` VARCHAR(200) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `numero` INT(11) NULL DEFAULT NULL,
  `complemento` VARCHAR(150) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `bairro` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Cidade_idCidade` INT(11) NOT NULL,
  PRIMARY KEY (`idEndereco`),
  INDEX `IDX_196B45845E012A3` (`Cidade_idCidade` ASC),
  CONSTRAINT `FK_196B45845E012A3`
    FOREIGN KEY (`Cidade_idCidade`)
    REFERENCES `sigeproc2`.`Cidade` (`idCidade`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`OrgaoExterno`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`OrgaoExterno` (
  `idOrgaoExterno` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `abreviacao` VARCHAR(10) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `Endereco_idEndereco` INT(11) NOT NULL,
  PRIMARY KEY (`idOrgaoExterno`),
  UNIQUE INDEX `UNIQ_6E6A2FB7F84D2025` (`Endereco_idEndereco` ASC),
  CONSTRAINT `FK_6E6A2FB7F84D2025`
    FOREIGN KEY (`Endereco_idEndereco`)
    REFERENCES `sigeproc2`.`Endereco` (`idEndereco`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Workflow`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Workflow` (
  `idWorkflow` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `descricao` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL,
  `Assunto_idAssunto` INT(11) NOT NULL,
  PRIMARY KEY (`idWorkflow`),
  INDEX `IDX_9CB3FA40390992FE` (`Assunto_idAssunto` ASC),
  CONSTRAINT `FK_9CB3FA40390992FE`
    FOREIGN KEY (`Assunto_idAssunto`)
    REFERENCES `sigeproc2`.`Assunto` (`idAssunto`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`FluxoPosto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`FluxoPosto` (
  `idFluxoPosto` INT(11) NOT NULL AUTO_INCREMENT,
  `diasUteis` INT(11) NULL DEFAULT NULL,
  `Workflow_idWorkflow` INT(11) NOT NULL,
  `Setor_idSetor` INT(11) NULL DEFAULT NULL,
  `OrgaoExterno_idOrgaoExterno` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idFluxoPosto`),
  INDEX `IDX_9353EFBA8DCD1A6C` (`Workflow_idWorkflow` ASC),
  INDEX `IDX_9353EFBAC6172334` (`Setor_idSetor` ASC),
  INDEX `IDX_9353EFBA83A29C01` (`OrgaoExterno_idOrgaoExterno` ASC),
  CONSTRAINT `FK_9353EFBA83A29C01`
    FOREIGN KEY (`OrgaoExterno_idOrgaoExterno`)
    REFERENCES `sigeproc2`.`OrgaoExterno` (`idOrgaoExterno`),
  CONSTRAINT `FK_9353EFBA8DCD1A6C`
    FOREIGN KEY (`Workflow_idWorkflow`)
    REFERENCES `sigeproc2`.`Workflow` (`idWorkflow`),
  CONSTRAINT `FK_9353EFBAC6172334`
    FOREIGN KEY (`Setor_idSetor`)
    REFERENCES `sigeproc2`.`Setor` (`idSetor`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Recurso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Recurso` (
  `idRecurso` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  PRIMARY KEY (`idRecurso`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Permissao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Permissao` (
  `idPermissao` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `permitido` TINYINT(1) NOT NULL,
  `Recurso_idRecurso` INT(11) NOT NULL,
  PRIMARY KEY (`idPermissao`),
  INDEX `IDX_D939CCE96F175218` (`Recurso_idRecurso` ASC),
  CONSTRAINT `FK_D939CCE96F175218`
    FOREIGN KEY (`Recurso_idRecurso`)
    REFERENCES `sigeproc2`.`Recurso` (`idRecurso`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Funcao_has_Permissao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Funcao_has_Permissao` (
  `Funcao_idFuncao` INT(11) NOT NULL,
  `Permissao_idPermissao` INT(11) NOT NULL,
  PRIMARY KEY (`Funcao_idFuncao`, `Permissao_idPermissao`),
  INDEX `IDX_213BA1B68E77073A` (`Funcao_idFuncao` ASC),
  INDEX `IDX_213BA1B6AB520EBC` (`Permissao_idPermissao` ASC),
  CONSTRAINT `FK_213BA1B6AB520EBC`
    FOREIGN KEY (`Permissao_idPermissao`)
    REFERENCES `sigeproc2`.`Permissao` (`idPermissao`),
  CONSTRAINT `FK_213BA1B68E77073A`
    FOREIGN KEY (`Funcao_idFuncao`)
    REFERENCES `sigeproc2`.`Funcao` (`idFuncao`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`GuiaDeRemessa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`GuiaDeRemessa` (
  `idGuiaDeRemessa` INT(11) NOT NULL AUTO_INCREMENT,
  `numero` INT(11) NULL DEFAULT NULL,
  `anoExercicio` INT(11) NOT NULL,
  `dataCriacao` DATETIME NOT NULL,
  `dataRecebimento` DATETIME NULL DEFAULT NULL,
  `Emissor_idUsuario` INT(11) NOT NULL,
  `Destinatario_idUsuario` INT(11) NULL DEFAULT NULL,
  `Setor_idSetor` INT(11) NULL DEFAULT NULL,
  `OrgaoExterno_idOrgaoExterno` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idGuiaDeRemessa`),
  UNIQUE INDEX `numero_UNIQUE` (`numero` ASC, `anoExercicio` ASC),
  INDEX `IDX_949363FA953269CC` (`Emissor_idUsuario` ASC),
  INDEX `IDX_949363FA366609C2` (`Destinatario_idUsuario` ASC),
  INDEX `IDX_949363FAC6172334` (`Setor_idSetor` ASC),
  INDEX `IDX_949363FA83A29C01` (`OrgaoExterno_idOrgaoExterno` ASC),
  CONSTRAINT `FK_949363FA83A29C01`
    FOREIGN KEY (`OrgaoExterno_idOrgaoExterno`)
    REFERENCES `sigeproc2`.`OrgaoExterno` (`idOrgaoExterno`),
  CONSTRAINT `FK_949363FA366609C2`
    FOREIGN KEY (`Destinatario_idUsuario`)
    REFERENCES `sigeproc2`.`Usuario` (`idUsuario`),
  CONSTRAINT `FK_949363FA953269CC`
    FOREIGN KEY (`Emissor_idUsuario`)
    REFERENCES `sigeproc2`.`Usuario` (`idUsuario`),
  CONSTRAINT `FK_949363FAC6172334`
    FOREIGN KEY (`Setor_idSetor`)
    REFERENCES `sigeproc2`.`Setor` (`idSetor`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`GuiaDeRemessa_has_Processo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`GuiaDeRemessa_has_Processo` (
  `GuiaDeRemessa_idGuiaDeRemessa` INT(11) NOT NULL,
  `Processo_idProcesso` INT(11) NOT NULL,
  PRIMARY KEY (`GuiaDeRemessa_idGuiaDeRemessa`, `Processo_idProcesso`),
  INDEX `IDX_53167C6DF9CD4798` (`GuiaDeRemessa_idGuiaDeRemessa` ASC),
  INDEX `IDX_53167C6D1AD5E10F` (`Processo_idProcesso` ASC),
  CONSTRAINT `FK_53167C6D1AD5E10F`
    FOREIGN KEY (`Processo_idProcesso`)
    REFERENCES `sigeproc2`.`Processo` (`idProcesso`),
  CONSTRAINT `FK_53167C6DF9CD4798`
    FOREIGN KEY (`GuiaDeRemessa_idGuiaDeRemessa`)
    REFERENCES `sigeproc2`.`GuiaDeRemessa` (`idGuiaDeRemessa`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Pendencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Pendencia` (
  `idPendencia` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `dataCriacao` DATETIME NOT NULL,
  `resolvido` TINYINT(1) NOT NULL,
  `dataConclusao` DATETIME NULL DEFAULT NULL,
  `Processo_idProcesso` INT(11) NOT NULL,
  PRIMARY KEY (`idPendencia`),
  INDEX `IDX_A8C0DA71AD5E10F` (`Processo_idProcesso` ASC),
  CONSTRAINT `FK_A8C0DA71AD5E10F`
    FOREIGN KEY (`Processo_idProcesso`)
    REFERENCES `sigeproc2`.`Processo` (`idProcesso`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Parecer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Parecer` (
  `idParecer` INT(11) NOT NULL AUTO_INCREMENT,
  `descricao` LONGTEXT CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL,
  `data` DATETIME NOT NULL,
  `Pendencia_idPendencia` INT(11) NOT NULL,
  PRIMARY KEY (`idParecer`),
  INDEX `IDX_1EEBA42384C7652D` (`Pendencia_idPendencia` ASC),
  CONSTRAINT `FK_1EEBA42384C7652D`
    FOREIGN KEY (`Pendencia_idPendencia`)
    REFERENCES `sigeproc2`.`Pendencia` (`idPendencia`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `sigeproc2`.`Telefone`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sigeproc2`.`Telefone` (
  `idTelefone` INT(11) NOT NULL AUTO_INCREMENT,
  `ddd` INT(11) NOT NULL,
  `numero` INT(11) NOT NULL,
  `Requerente_idRequerente` INT(11) NOT NULL,
  PRIMARY KEY (`idTelefone`),
  INDEX `IDX_D8448137AF476202` (`Requerente_idRequerente` ASC),
  CONSTRAINT `FK_D8448137AF476202`
    FOREIGN KEY (`Requerente_idRequerente`)
    REFERENCES `sigeproc2`.`Requerente` (`idRequerente`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
USE `sigeproc`;

DELIMITER $$
USE `sigeproc`$$



CREATE TRIGGER composite_auto_increment_Processo BEFORE INSERT ON Processo
FOR EACH ROW
BEGIN
    DECLARE nextval INT;
    SET nextval = (SELECT IFNULL(MAX(numero),0) + 1 FROM Processo WHERE anoExercicio = NEW.anoExercicio); 
    SET NEW.numero = nextval;
END
$$

USE `sigeproc`$$



CREATE TRIGGER composite_auto_increment_GuiaDeRemessa BEFORE INSERT ON GuiaDeRemessa
FOR EACH ROW
BEGIN
    DECLARE nextval INT;
    SET nextval = (SELECT IFNULL(MAX(numero),0) + 1 FROM GuiaDeRemessa WHERE anoExercicio = NEW.anoExercicio); 
    SET NEW.numero = nextval;
END
$$


DELIMITER ;
