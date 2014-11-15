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
use Application\Filters\AssuntoFilter;
use Application\Entity\Assunto;

/**
 * Description of ManterUsuariosController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterProcessosController extends AbstractActionController {

    private $objectManager;

    public function getObjectManager() {
        if (!$this->objectManager) {
            $this->objectManager = $this->getServiceLocator()->get('ObjectManager');
        }
        return $this->objectManager;
    }

    public function indexAction() {
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $dql = "SELECT a FROM Application\Entity\Assunto AS a";

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
                'assuntos' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }

    public function preencheCombos() {
        $objectManager = $this->getObjectManager();
        $dql = "SELECT COUNT(s) ";
        $dql .= "FROM Application\Entity\Setor s";
        $qtdSetores = $objectManager->createQuery($dql)->getSingleScalarResult();

        if ($qtdSetores <= 0) {
            $mensagem = "É necessário que sejam cadastrados setores ";
            $mensagem .= "para que um assunto possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('assuntos');
        }

        $dql = "SELECT COUNT(s) ";
        $dql .= "FROM Application\Entity\Secretaria s";
        $qtdSecretaria = $objectManager->createQuery($dql)->getSingleScalarResult();

        if ($qtdSecretaria <= 0) {
            $mensagem = "É necessário que sejam cadastradas secretarias ";
            $mensagem .= "para que um assunto possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('assuntos');
        }

        $setores = $objectManager->getRepository('Application\Entity\Setor')
                ->findAll();
        $secretarias = $objectManager->getRepository('Application\Entity\Secretaria')
                ->findAll();

        return array(
            'secretarias' => $secretarias,
            'setores' => $setores
        );
    }

    public function adicionarAction() {
        $request = $this->getRequest();

        if (!$request->isPost())
            return $this->preencheCombos();

        $nomeTxt = $request->getPost('nomeTxt');
        $secretariaSlct = $request->getPost('secretariaSlct');
        $setorSlct = $request->getPost('setorSlct');
        $descricaoTxt = $request->getPost('descricaoTxt');

        $objectManager = $this->getObjectManager();
        $dadosFiltrados = new AssuntoFilter($objectManager, $nomeTxt, $secretariaSlct, $setorSlct, $descricaoTxt);

        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toUrl('/assuntos/adicionar');
            return;
        }

        $setor = $objectManager->getRepository('Application\Entity\Setor')
                ->find((int) $dadosFiltrados->getValue('setorSlct'));

        $assunto = new Assunto();
        $assunto->setNome($dadosFiltrados->getValue('nomeTxt'));
        $assunto->setSetor($setor);
        $assunto->setDescricao($dadosFiltrados->getValue('descricaoTxt'));

        try {
            $objectManager->persist($assunto);
            $objectManager->flush();
            $this->flashMessenger()->addSuccessMessage("Assunto adicionado com sucesso.");
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        $this->redirect()->toRoute('assuntos');
    }
    
    public function excluirAction(){
        $objectManager = $this->getObjectManager();
        $idAssunto = $this->params()->fromRoute('id');
        if(isset($idAssunto)){
            $Assuntos = $objectManager->getRepository('Application\Entity\Assunto');
            $assunto = $Assuntos->find($idAssunto); 
            try{
                $objectManager->remove($assunto);
                $objectManager->flush();
            }  catch (\Exception $e){
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('assuntos');
            }
            $this->flashMessenger()->addSuccessMessage("Assunto excluído com sucesso.");
            $this->redirect()->toRoute('assuntos');
        }
    }

    public function visualizarAction() {
        $idAssunto = (int) $this->params()->fromRoute('id', 0);
        if ($idAssunto) {
            $objectManager = $this->getObjectManager();
            try {
                $assuntos = $objectManager->getRepository('Application\Entity\Assunto');
                $assunto = $assuntos->find($idAssunto);
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('assuntos');
            }
            if ($assunto != NULL) {
                return array('assunto' => $assunto);
            } else {
                $this->flashMessenger()->addMessage("Assunto não encotrado");
                $this->redirect()->toRoute('assuntos');
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
            echo '<option>----Selecione um setor----</option>';
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
