<?php

/*
 * Copyright (C) 2014 Irving Fernando de Medeiros Oliveira
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Application\Entity\OrgaoExterno;
use Application\Entity\Endereco;
use Application\Filters\OrgaoExternoFilter;

/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterOrgaosExternosController extends AbstractActionController {

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $objectManager;

    /**
     * 
     * @return Doctrine\ORM\EntityManager
     */
    public function getObjectManager() {
        if ($this->objectManager == NULL) {
            $this->objectManager = $this->getServiceLocator()->get('ObjectManager');
        }
        return $this->objectManager;
    }

    public function indexAction() {
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $dql = "SELECT o FROM Application\Entity\OrgaoExterno AS o";

            $query = $objectManager->createQuery($dql);

            $ormPaginator = new ORMPaginator($query);
            $ormPaginatorIterator = $ormPaginator->getIterator();

            $adapter = new Iterator($ormPaginatorIterator);

            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int) $this->params()->fromQuery('page');
            if ($page)
                $paginator->setCurrentPageNumber($page);

            return array(
                'orgaosExternos' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }

    public function adicionarAction() {
        $request = $this->getRequest();
        
        if (!$request->isPost()) {
            return $this->preencheCombos();
        }
        
        $nomeTxt = $request->getPost('nomeTxt');
        $abreviacaoTxt = $request->getPost('abreviacaoTxt');
        $logradouroTxt = $request->getPost('logradouroTxt');
        $numeroTxt = $request->getPost('numeroTxt');
        $complementoTxt = $request->getPost('complementoTxt');
        $bairroTxt = $request->getPost('bairroTxt');
        $cidadeSlct = $request->getPost('cidadeSlct');
        $dadosFiltrados = new OrgaoExternoFilter($this->getObjectManager(), $nomeTxt, $abreviacaoTxt, $logradouroTxt, $numeroTxt, $complementoTxt, $bairroTxt, $cidadeSlct);
        if ($dadosFiltrados->isValid()) {
            $objectManager = $this->getObjectManager();

            $orgaoExterno = new OrgaoExterno();
            $orgaoExterno->setNome($dadosFiltrados->getValue('nomeTxt'));
            $orgaoExterno->setAbreviacao($dadosFiltrados->getValue('abreviacaoTxt'));

            $endereco = new Endereco();
            $endereco->setLogradouro($dadosFiltrados->getValue('logradouroTxt'));
            $endereco->setBairro($dadosFiltrados->getValue('bairroTxt'));
            $endereco->setNumero($dadosFiltrados->getValue('numeroTxt'));
            $endereco->setComplemento($dadosFiltrados->getValue('complementoTxt'));
            $endereco->setCidade($objectManager->getRepository('Application\Entity\Cidade')
                            ->find($dadosFiltrados->getValue('cidadeSlct')));

            $orgaoExterno->setEndereco($endereco);

            try {
                $objectManager->persist($orgaoExterno);
                $objectManager->flush();
                $this->flashMessenger()->addSuccessMessage("Orgao externo adicionado com sucesso.");
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                echo $e;
                die();
            }
            $this->redirect()->toRoute('orgaosexternos');
        } else {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toRoute('orgaosexternos');
        }
    }

    public function buscarAction() {
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();
        $busca = $this->params()->fromQuery('busca');
        if ($busca == null) {
            $this->redirect()->toRoute('tiposdedocumento');
        }
        $busca = '%' . $busca . '%';
        if ($request->isGet()) {
            $dql = "SELECT t FROM Application\Entity\TipoDocumento AS t ";
            $dql.= "WHERE t.nome LIKE ?1";

            $query = $objectManager->createQuery($dql);
            $query->setParameter(1, $busca);

            $ormPaginator = new ORMPaginator($query);
            $ormPaginatorIterator = $ormPaginator->getIterator();

            $adapter = new Iterator($ormPaginatorIterator);

            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int) $this->params()->fromQuery('page');
            if ($page)
                $paginator->setCurrentPageNumber($page);

            $qtdResultados = $paginator->count();

            if ($qtdResultados == 0) {
                $this->flashMessenger()->addInfoMessage("Sua pesquisa não obteve resultados.");
                $this->redirect()->toRoute('tiposdedocumento');
            }

            return array(
                'tiposDeDocumento' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }

    public function editarAction() {
        $request = $this->getRequest();
        $idTipoDocumento = (int) $this->params()->fromRoute('id', 0);
        if ($idTipoDocumento) {
            $objectManager = $this->getObjectManager();
            $tiposDeDocumento = $objectManager->getRepository('Application\Entity\TipoDocumento');
            $tipoDeDocumento = $tiposDeDocumento->find($idTipoDocumento);
            if (!$request->isPost()) {
                try {
                    if ($tipoDeDocumento != NULL) {
                        return array('tipoDocumento' => $tipoDeDocumento);
                    } else {
                        $this->flashMessenger()->addMessage("Tipo de documento não encotrada");
                        $this->redirect()->toRoute('tiposdedocumento');
                    }
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                    $this->redirect()->toRoute('tiposdedocumento');
                }
            } else {
                $nomeTxt = $request->getPost('nomeTxt');
                $dadosFiltrados = new TipoSetorFilter($objectManager, $nomeTxt);
                $tipoDeDocumento->setNome($dadosFiltrados->getValue('nomeTxt'));
                $objectManager->persist($tipoDeDocumento);
                $objectManager->flush();
                $this->flashMessenger()->addSuccessMessage("Tipo de documento editado com sucesso.");
                $this->redirect()->toRoute('tiposdedocumento');
            }
        }
    }

    public function excluirAction() {
        $objectManager = $this->getObjectManager();
        $idTipoDocumento = $this->params()->fromRoute('id');
        if (isset($idTipoDocumento)) {
            $tiposDeDocumento = $objectManager->getRepository('Application\Entity\TipoDocumento');
            $tipoDeDocumento = $tiposDeDocumento->find($idTipoDocumento);
            try {
                $objectManager->remove($tipoDeDocumento);
                $objectManager->flush();
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('tiposdedocumento');
            }
            $this->flashMessenger()->addSuccessMessage("Tipo de documento excluído com sucesso.");
            $this->redirect()->toRoute('tiposdedocumento');
        }
    }

    public function preencheCombos() {
        $objectManager = $this->getObjectManager();
        $dql = "SELECT COUNT(e) ";
        $dql .= "FROM Application\Entity\Estado e";
        $qtdEstados = $objectManager->createQuery($dql)->getSingleScalarResult();

        if ($qtdEstados <= 0) {
            $mensagem = "É necessário que sejam cadastrados Estados ";
            $mensagem .= "para que um orgão externo possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('orgaosexternos');
        }

        $dql = "SELECT COUNT(c) ";
        $dql .= "FROM Application\Entity\Cidade c";
        $qtdCidades = $objectManager->createQuery($dql)->getSingleScalarResult();

        if ($qtdCidades <= 0) {
            $mensagem = "É necessário que sejam cadastradas cidades ";
            $mensagem .= "para que um orgão externo possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('orgaosexternos');
        }

        $estados = $objectManager->getRepository('Application\Entity\Estado')
                ->findAll();

        return array(
            'estados' => $estados
        );
    }

    public function getCidadeComboByEstadoAction() {
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();
        $idEstado = $request->getPost('data');
//        $idEstado = $this->params()->fromRoute('id');
        $selecionado = $request->getPost('selected');

        $estado = $objectManager->getRepository('Application\Entity\Estado')
                ->find($idEstado);
        $dql = "SELECT c FROM Application\Entity\Cidade AS c ";
        $dql.= "WHERE c.estado = ?1";
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1, $estado);

        $cidades = $query->getResult();
        $qtdCidade = count($cidades);
        
        if ($qtdCidade == 0) {
            echo '<option>---- Não há cidades cadastradas neste estado----</option>';
        } else {
            echo '<option>----Selecione uma cidade----</option>';
            foreach ($cidades as $cidade) {
                if ($cidade->getIdCidade() == $selecionado)
                    echo '<option value="' . $cidade->getIdCidade() . '" selected="selected">' . $cidade->getNome() . '</option>';
                else
                    echo '<option value="' . $cidade->getIdCidade() . '">' . $cidade->getNome() . '</option>';
            }
        }
        die();
    }

    public function visualizarAction() {
        $idTipoDocumento = (int) $this->params()->fromRoute('id', 0);
        if ($idTipoDocumento) {
            $objectManager = $this->getObjectManager();
            try {
                $tiposDeDocumento = $objectManager->getRepository('Application\Entity\TipoDocumento');
                $tipoDeDocumento = $tiposDeDocumento->find($idTipoDocumento);
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('tiposdedocumento');
            }
            if ($tipoDeDocumento != NULL) {
                return array('tipoDocumento' => $tipoDeDocumento);
            } else {
                $this->flashMessenger()->addMessage("Tipo de documento não encotrado");
                $this->redirect()->toRoute('tiposdedocumento');
            }
        }
    }

}
