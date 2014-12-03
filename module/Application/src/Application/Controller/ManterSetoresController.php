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
use Application\Filters\SetorFilter;
use Application\Entity\Setor;

use Application\DAL\SecretariaDAO;
use Application\DAL\SetorDAO;
use Application\DAL\TipoSetorDAO;

/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterSetoresController extends AbstractActionController {

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
            $dql = "SELECT s FROM Application\Entity\Setor AS s";

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
                'setores' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }

    public function preencheCombos() {
        $objectManager = $this->getObjectManager();
        $dql = "SELECT COUNT(t) ";
        $dql .= "FROM Application\Entity\TipoSetor t";
        $qtdTiposSetor = $objectManager->createQuery($dql)->getSingleScalarResult();

        if ($qtdTiposSetor <= 0) {
            $mensagem = "É necessário que sejam cadastrados tipos de ";
            $mensagem .= "setor para que um setor possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('setores');
        }

        $dql = "SELECT COUNT(s) ";
        $dql .= "FROM Application\Entity\Secretaria s";
        $qtdSecretaria = $objectManager->createQuery($dql)->getSingleScalarResult();

        if ($qtdSecretaria <= 0) {
            $mensagem = "É necessário que sejam cadastradas secretarias ";
            $mensagem .= "para que um setor possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('setores');
        }

        $setores = $objectManager->getRepository('Application\Entity\Setor')
                ->findAll();
        $secretarias = $objectManager->getRepository('Application\Entity\Secretaria')
                ->findAll();
        $tiposSetor = $objectManager->getRepository('Application\Entity\TipoSetor')
                ->findAll();

        return array(
            'secretarias' => $secretarias,
            'tiposSetor' => $tiposSetor,
            'setores' => $setores
        );
    }

    public function adicionarAction() {
        $request = $this->getRequest();

        if (!$request->isPost())
            return $this->preencheCombos();
        $nomeTxt = $request->getPost('nomeTxt');
        $siglaTxt = $request->getPost('siglaTxt');
        $secretariaSlct = $request->getPost('secretariaSlct');
        $tipoSlct = $request->getPost('tipoSlct');
        if(is_int($request->getPost('setorMestreSlct')))
            $setorMestreSlct = $request->getPost('setorMestreSlct');
        else
            $setorMestreSlct = NULL;
        $arquivoRd = $request->getPost('arquivoRd');
        $secretariaDAO= new SecretariaDAO($this->getServiceLocator());
        $setorDAO= new SetorDAO($this->getServiceLocator());
        $tipoSetorDAO= new TipoSetorDAO($this->getServiceLocator());
        
        $dadosFiltrados = new SetorFilter($nomeTxt, $siglaTxt, $secretariaSlct, 
                $tipoSlct, $arquivoRd, $setorMestreSlct, $secretariaDAO, $tipoSetorDAO, $setorDAO);

        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toUrl('/setores/adicionar');
            return;
        }

        $objectManager = $this->getServiceLocator()->get('ObjectManager');
        $secretaria = $objectManager->getRepository('Application\Entity\Secretaria')
                ->find((int) $dadosFiltrados->getValue('secretariaSlct'));
        try{
            $setorMestre = $objectManager->getRepository('Application\Entity\Setor')
                    ->find((int) $dadosFiltrados->getValue('setorMestreSlct'));
        }  catch (\Exception $e){
            
        }
        
        $tipo = $objectManager->getRepository('Application\Entity\TipoSetor')
                ->find((int) $dadosFiltrados->getValue('tipoSlct'));

        $setor = new Setor();
        $setor->setNome($dadosFiltrados->getValue('nomeTxt'));
        $setor->setSigla($dadosFiltrados->getValue('siglaTxt'));
        $setor->setArquivo((bool)$dadosFiltrados->getValue('arquivoRd'));
        $setor->setSecretaria($secretaria);
        $setor->setTipoSetor($tipo);
        if ($setorMestre instanceof \Application\Entity\Setor)
            $setor->setSetorPai($setorMestre);

        try {
            $objectManager->persist($setor);
            $objectManager->flush();
            $this->flashMessenger()->addSuccessMessage("Setor adicionado com sucesso.");
        } catch (\Doctrine\DBAL\DBALException $e){
            if(strpos($e->getMessage(), 'SQLSTATE[23000]') > 0){
                $mensagem = "Já existe um setor cadastrado nesta secretaria ";
                $mensagem.= "com este nome ou sigla.";
            } else{
                $mensagem = "Ocorreu um erro na operação, tente novamente ";
                $mensagem .= "ou entre em contato com um administrador ";
                $mensagem .= "do sistema.";
            }
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        $this->redirect()->toRoute('setores');
    }

    public function buscarAction() {
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();
        $busca = $this->params()->fromQuery('busca');
        if ($busca == null) {
            $this->redirect()->toRoute('secretarias');
        }
        $busca = '%' . $busca . '%';
        if ($request->isGet()) {
            $dql = "SELECT DISTINCT s FROM Application\Entity\Setor AS s ";
            $dql.= "JOIN s.tipoSetor t ";
            $dql.= "WHERE s.nome LIKE ?1 ";
            $dql.= "OR s.sigla LIKE ?1";
            $dql.= "OR t.nome LIKE ?1";

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
                $this->flashMessenger()->addErrorMessage("Setor não encontrado.");
                $this->redirect()->toRoute('setores');
            }
            return array(
                'setores' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }

    public function editarAction() {
        $request = $this->getRequest();
        $idSetor = (int) $this->params()->fromRoute('id', 0);
        if ($idSetor) {
            $objectManager = $this->getObjectManager();
            $setores = $objectManager->getRepository('Application\Entity\Setor');
            $setor = $setores->find($idSetor);
            if (!$request->isPost()) {
                try {
                    if ($setor != NULL) {
                        $objectManager = $this->getObjectManager();

                        $secretarias = $objectManager->getRepository('Application\Entity\Secretaria')
                                ->findAll();

                        $tiposSetor = $objectManager->getRepository('Application\Entity\TipoSetor')
                                ->findAll();

                        $setores = $objectManager->getRepository('Application\Entity\Setor')
                                ->findAll();

                        return array(
                            'secretarias' => $secretarias,
                            'tiposSetor' => $tiposSetor,
                            'setores' => $setores,
                            'setor' => $setor
                        );
                    } else {
                        $this->flashMessenger()->addMessage("Setor não encotrado.");
                        $this->redirect()->toRoute('setor');
                    }
                } catch (\Exception $e) {
                    $mensagem = "Ocorreu um erro na operação, tente ";
                    $mensagem .= "novamente ou entre em contato com um ";
                    $mensagem .= "administrador do sistema.";
                    $this->flashMessenger()->addErrorMessage($mensagem);
                    $this->redirect()->toRoute('setores');
                }
            } else {
                $nomeTxt = $request->getPost('nomeTxt');
                $siglaTxt = $request->getPost('siglaTxt');
                $secretariaSlct = $request->getPost('secretariaSlct');
                $tipoSlct = $request->getPost('tipoSlct');
                $setorMestreSlct = $request->getPost('setorMestreSlct');
                $dadosFiltrados = new SetorFilter($this->getObjectManager(),
                                                  $nomeTxt, $siglaTxt, $secretariaSlct, 
                                                  $tipoSlct, $setorMestreSlct);
                
                if (!$dadosFiltrados->isValid()) {
                    foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                        foreach ($erro->getMessages() as $message) {
                            $this->flashMessenger()->addErrorMessage($message);
                        }
                    }
                    $this->redirect()->toRoute('setores');
                    return;
                }
                $secretaria = $objectManager->getRepository('Application\Entity\Secretaria')
                                ->find($dadosFiltrados->getValue('secretariaSlct'));
                
                $tipo = $objectManager->getRepository('Application\Entity\TipoSetor')
                                ->find($dadosFiltrados->getValue('tipoSlct'));
                
                $setorMestre = $objectManager->getRepository('Application\Entity\Setor')
                                ->find($dadosFiltrados->getValue('setorMestreSlct'));
                
                $setor->setNome($dadosFiltrados->getValue('nomeTxt'));
                $setor->setSigla($dadosFiltrados->getValue('siglaTxt'));
                $setor->setSecretaria($secretaria);
                $setor->setTipo($tipo);
                if ($setorMestre instanceof \Application\Entity\Setor)
                    $setor->setSetorPai($setorMestre);
                
                $objectManager->persist($setor);
                $objectManager->flush();
                $this->flashMessenger()->addSuccessMessage("Setor editado com sucesso.");
                $this->redirect()->toRoute('setores');
            }
        }
    }

    public function excluirAction() {
        $objectManager = $this->getObjectManager();
        $idSetor = $this->params()->fromRoute('id');
        if (isset($idSetor)) {
            $setores = $objectManager->getRepository('Application\Entity\Setor');
            $setor = $setores->find($idSetor);
            try {
                $objectManager->remove($setor);
                $objectManager->flush();
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('setores');
                echo $e->getMessage();
            }
            $this->flashMessenger()->addSuccessMessage("Secretaria excluida com sucesso.");
            $this->redirect()->toRoute('setores');
        }
    }

    public function visualizarAction() {
        $idSetor = (int) $this->params()->fromRoute('id', 0);
        if ($idSetor) {
            $objectManager = $this->getObjectManager();
            try {
                $setorDAO = new SetorDAO($this->getServiceLocator());
                $setor = $setorDAO->lerPorId($idSetor);
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('setores');
            }
            if ($setor != NULL) {
                return array('setor' => $setor);
            } else {
                $this->flashMessenger()->addMessage("Setor não encotrado");
                $this->redirect()->toRoute('setores');
            }
        }
    }

    public function getSetorComboBySecretariaAction() {
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();
        $idSecretaria = $request->getPost('data');
        $selecionado = $request->getPost('selected');

        $secretaria = $objectManager->getRepository('Application\Entity\Secretaria')
                ->find($idSecretaria);
        $dql = "SELECT s FROM Application\Entity\Setor AS s ";
        $dql.= "WHERE s.secretaria = ?1";
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1, $secretaria);

        $setores = $query->getResult();
        $qtdSetores = count($setores);
        if ($qtdSetores == 0) {
            echo '<option>---- Não há setor cadastrado nesta secretaria----</option>';
        } else {
            echo '<option>----Selecione um setor mestre----</option>';
            foreach ($setores as $setor) {
                if ($setor->getIdSetor() == $selecionado)
                    echo '<option value="' . $setor->getIdSetor() . '" selected="selected">' . $setor->getTipo()->getNome() . ' de ' . $setor->getNome() . '</option>';
                else
                    echo '<option value="' . $setor->getIdSetor() . '">' . $setor->getTipo()->getNome() . ' de ' . $setor->getNome() . '</option>';
            }
        }
        die();
    }
}