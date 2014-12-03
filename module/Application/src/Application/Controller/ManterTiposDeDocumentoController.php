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
use Application\Filters\TipoDocumentoFilter;
use Application\Entity\TipoDocumento;
/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterTiposDeDocumentoController extends AbstractActionController{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $objectManager;
    /**
     * 
     * @return Doctrine\ORM\EntityManager
     */
    public function getObjectManager(){
        if($this->objectManager == NULL){
            $this->objectManager = $this->getServiceLocator()->get('ObjectManager');
        }
        return $this->objectManager;
    }
    
    public function indexAction() {
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();
        
        if(!$request->isPost()){
            $dql = "SELECT t FROM Application\Entity\TipoDocumento AS t";
            
            $query = $objectManager->createQuery($dql);
            
            $ormPaginator = new ORMPaginator($query);
            $ormPaginatorIterator = $ormPaginator->getIterator();
            
            $adapter = new Iterator($ormPaginatorIterator);
            
            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int)  $this->params()->fromQuery('page');
            if($page) $paginator->setCurrentPageNumber($page);
            
            return array(
                'tiposDeDocumento' => $paginator, 
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }
    
    public function adicionarAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $nomeTxt = $request->getPost('nomeTxt');
            $dadosFiltrados = new TipoDocumentoFilter($this->getObjectManager(), $nomeTxt);
            if($dadosFiltrados->isValid()){
                $tipoDeDocumento = new TipoDocumento();
                $tipoDeDocumento->setNome($dadosFiltrados->getValue('nomeTxt'));
                try{
                    $objectManager = $this->getServiceLocator()->get('ObjectManager');
                    $objectManager->persist($tipoDeDocumento);
                    $objectManager->flush();
                    $this->flashMessenger()->addSuccessMessage("Tipo de documento adicionado com sucesso.");
                }  catch (\Exception $e){
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                }
                $this->redirect()->toRoute('tiposdedocumento');
            }  else {
                foreach ($dadosFiltrados->getInvalidInput() as $erro){
                    foreach ($erro->getMessages() as $message){
                        $this->flashMessenger()->addErrorMessage($message);
                    }
                }   
                $this->redirect()->toRoute('tiposdedocumento');
            }
        }
    }
    
    public function buscarAction(){
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();
        $busca = $this->params()->fromQuery('busca');
        if($busca == null){
            $this->redirect()->toRoute('tiposdedocumento');
        }
        $busca = '%'.$busca.'%';
        if($request->isGet()){
            $dql = "SELECT t FROM Application\Entity\TipoDocumento AS t ";
            $dql.= "WHERE t.nome LIKE ?1";
            
            $query = $objectManager->createQuery($dql);
            $query->setParameter(1,$busca);
            
            $ormPaginator = new ORMPaginator($query);
            $ormPaginatorIterator = $ormPaginator->getIterator();
            
            $adapter = new Iterator($ormPaginatorIterator);
            
            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int)  $this->params()->fromQuery('page');
            if($page) $paginator->setCurrentPageNumber($page);
            
            $qtdResultados = $paginator->count();
            
            if($qtdResultados==0){
                $this->flashMessenger()->addInfoMessage("Sua pesquisa não obteve resultados.");
                $this->redirect()->toRoute('tiposdedocumento');
            }
                
            return array(
                'tiposDeDocumento' => $paginator, 
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }
    
    public function editarAction(){
        $request = $this->getRequest();
        $idTipoDocumento = (int)$this->params()->fromRoute('id',0);
        if($idTipoDocumento){
            $objectManager = $this->getObjectManager();
            $tiposDeDocumento = $objectManager->getRepository('Application\Entity\TipoDocumento');
            $tipoDeDocumento = $tiposDeDocumento->find($idTipoDocumento);
            if(!$request->isPost()){
                try{
                    if($tipoDeDocumento != NULL){
                        return array('tipoDocumento' => $tipoDeDocumento);
                    }else{
                        $this->flashMessenger()->addMessage("Tipo de documento não encotrada");
                        $this->redirect()->toRoute('tiposdedocumento');
                    }
                }  catch (\Exception $e){
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                    $this->redirect()->toRoute('tiposdedocumento');
                }
            }else{
                $nomeTxt = $request->getPost('nomeTxt');
                $dadosFiltrados = new TipoDocumentoFilter($objectManager, $nomeTxt);
                $tipoDeDocumento->setNome($dadosFiltrados->getValue('nomeTxt'));
                $objectManager->persist($tipoDeDocumento);
                $objectManager->flush();
                $this->flashMessenger()->addSuccessMessage("Tipo de documento editado com sucesso.");
                $this->redirect()->toRoute('tiposdedocumento');
            }
        }
    }
    
    public function excluirAction(){
        $objectManager = $this->getObjectManager();
        $idTipoDocumento = $this->params()->fromRoute('id');
        if(isset($idTipoDocumento)){
            $tiposDeDocumento = $objectManager->getRepository('Application\Entity\TipoDocumento');
            $tipoDeDocumento = $tiposDeDocumento->find($idTipoDocumento); 
            try{
                $objectManager->remove($tipoDeDocumento);
                $objectManager->flush();
            }  catch (\Exception $e){
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('tiposdedocumento');
            }
            $this->flashMessenger()->addSuccessMessage("Tipo de documento excluído com sucesso.");
            $this->redirect()->toRoute('tiposdedocumento');
        }
    }

    public function visualizarAction(){
        $idTipoDocumento = (int)$this->params()->fromRoute('id',0);
        if($idTipoDocumento){
            $objectManager = $this->getObjectManager();
            try{
                $tiposDeDocumento = $objectManager->getRepository('Application\Entity\TipoDocumento');
                $tipoDeDocumento = $tiposDeDocumento->find($idTipoDocumento);
            }  catch (\Exception $e){
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('tiposdedocumento');
            }
            if($tipoDeDocumento != NULL){
                return array('tipoDocumento' => $tipoDeDocumento);
            }else{
                $this->flashMessenger()->addMessage("Tipo de documento não encotrado");
                $this->redirect()->toRoute('tiposdedocumento');
            }
        }
    }
}
