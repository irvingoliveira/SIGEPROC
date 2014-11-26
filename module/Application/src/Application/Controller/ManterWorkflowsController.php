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
use Doctrine\Common\Collections\ArrayCollection;
use Application\DAL\AssuntoDAO;
use Application\DAL\OrgaoExternoDAO;
use Application\DAL\SecretariaDAO;
use Application\DAL\WorkflowDAO;
use Application\DAL\FluxoPostoDAO;
use Application\Filters\WorkflowFilter;
use Application\Filters\FluxoPostoFilter;
/**
 * Description of ManterWorkflowsController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterWorkflowsController extends AbstractActionController{
    
    public function indexAction() {
         $request = $this->getRequest();

        if (!$request->isPost()) {
            $workflowDAO = new WorkflowDAO($this->getServiceLocator());

            $ormPaginator = new ORMPaginator($workflowDAO->lerTodos());
            $ormPaginatorIterator = $ormPaginator->getIterator();

            $adapter = new Iterator($ormPaginatorIterator);

            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int) $this->params()->fromQuery('page');
            if ($page) {
                $paginator->setCurrentPageNumber($page);
            }
            return array(
                'workflows' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }
    
    public function preencheCombo(){
        $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
        $secretarias = $secretariaDAO->lerRepositorio();
        
        $orgaoExternoDAO = new OrgaoExternoDAO($this->getServiceLocator());
        $orgaosExternos = $orgaoExternoDAO->lerRepositorio();
        
        return array(
            'secretarias' => $secretarias,
            'orgaosExternos' => $orgaosExternos
        );
    }

    public function adicionarAction(){
        $request = $this->getRequest();
        if (!$request->isPost())
            return $this->preencheCombo();
    
        $dadosFiltrados = new WorkflowFilter($this->getServiceLocator(), 
                                             $request->getPost('postos'), 
                                             $request->getPost('descricaoTxt'), 
                                             $request->getPost('assunto'));
        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toUrl('/workflows/adicionar');
            return $this->preencheCombo();
        }
        
        $parametros = new ArrayCollection();
        $assuntoDao = new AssuntoDAO($this->getServiceLocator());
        $assunto = $assuntoDao->lerPorId($dadosFiltrados->getValue('assuntoTxt'));
        $parametros->set('descricao', $dadosFiltrados->getValue('descricaoTxt'));
        $parametros->set('assuntoTxt', $assunto);

        
        $qtdPostos = count($request->getPost('postos'));
        $postos = $request->getPost('postos');
        for($i=0;$i<$qtdPostos;$i++){
            $parametros->set('posto'.$i, $postos[$i]);
        }
        try {
            $workflowDAO = new WorkflowDAO($this->getServiceLocator());
            $workflow = $workflowDAO->salvar($parametros);

        } catch (\Doctrine\DBAL\DBALException $e) {
            if (strpos($e->getMessage(), 'SQLSTATE[23000]') > 0) {
                $mensagem = "Já existe um workflow cadastrado com este nome ";
                echo $e->getMessage();die();
            } else {
                $mensagem = "Ocorreu um erro na operação, tente novamente ";
                $mensagem .= "ou entre em contato com um administrador ";
                $mensagem .= "do sistema.";
            }
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('workflows');
            return;
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('workflows');
            return;
        }
        $this->redirect()->toRoute('workflows', array(
            'action' => 'descreverworkflow',
            'id' => $workflow->getIdWorkflow()
        ));
    }
    
    public function descreverWorkflowAction(){
        $request = $this->getRequest();
        
        if($request->isPost()){
            $idWorkflow = $request->getPost('idWorkflow');
            $idPosto = $request->getPost('id');
            $indice = $request->getPost('in');
            $diasUteisTxt = $request->getPost('diasUteisTxt');
            $descricaoTxt = $request->getPost('descricaoTxt');
            
            $dadosFiltrados = new FluxoPostoFilter($this->getServiceLocator(), 
                                                $diasUteisTxt, $descricaoTxt);
            if (!$dadosFiltrados->isValid()) {
                foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                    foreach ($erro->getMessages() as $message) {
                        $this->flashMessenger()->addErrorMessage($message);
                    }
                }
                $this->redirect()->toRoute('workflows');
                return;
            }
            
            $parametros = new ArrayCollection();
            $parametros->set('diasUteis', $dadosFiltrados->getValue('diasUteisTxt'));
            $parametros->set('descricao', $dadosFiltrados->getValue('descricaoTxt'));

            try{
                $fluxoPostoDAO = new FluxoPostoDAO($this->getServiceLocator());
                $fluxoPostoDAO->editar($idPosto, $parametros);      
            } catch (\Exception $e) {
                $mensagem = "Ocorreu um erro na operação, tente novamente ";
                $mensagem .= "ou entre em contato com um administrador ";
                $mensagem .= "do sistema.";
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toRoute('workflows');
            }
        } else{
            $idWorkflow = $this->params()->fromRoute('id');
            $indice = $this->params()->fromRoute('in');
        }
        
        $workflowDAO = new WorkflowDAO($this->getServiceLocator());
        $workflow = $workflowDAO->lerPorId($idWorkflow);
        $postos = $workflow->getFluxosPostos();
        
        
        if ($indice == NULL){
            return array(
                'posto' => $postos[0],
                'indice' => 0
                    );
        }
        
        if($postos[$indice] != NULL){
            return array(
                'posto' => $postos[$indice],
                'indice' => $indice
                    );
        }  else {
            $mensagem = "Workflow cadastrado com sucesso.";
            $this->flashMessenger()->addSuccessMessage($mensagem);
            $this->redirect()->toRoute('workflows');
        }
        
        $this->redirect()->toRoute('workflows', array('id' => $idWorkflow));
    }
    
    public function visualizarAction(){
        $idWorkflow = (int)$this->params()->fromRoute('id');
        if (!$idWorkflow) {
            $this->flashMessenger()->addMessage("Workflow não encotrado");
            $this->redirect()->toRoute('workflows');
        }
        try {
            $workflowDAO = new WorkflowDAO($this->getServiceLocator());
            $workflow = $workflowDAO->lerPorId($idWorkflow);
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('workflows');
        }
        if ($workflow != NULL) {
            return array('workflow' => $workflow);
        } else {
            $this->flashMessenger()->addMessage("Workflow não encotrado");
            $this->redirect()->toRoute('workflows');
        }
    }


    public function excluirAction() {
        $idWorkflow = $this->params()->fromRoute('id');
        if (isset($idWorkflow)) {
            try {
                $workflowDAO = new WorkflowDAO($this->getServiceLocator());
                $workflowDAO->excluir($idWorkflow);
            } catch (\Exception $e) {
                $mensagem = "Ocorreu um erro na operação, tente novamente ou ";
                $mensagem.= "entre em contato com um administrador do sistema.";
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toRoute('workflows');
            }
            $this->flashMessenger()->addSuccessMessage("Workflow excluído com sucesso.");
            $this->redirect()->toRoute('workflows');
        }
    }
}
