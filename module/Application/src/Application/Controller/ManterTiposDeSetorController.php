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
use Application\Filters\TipoSetorFilter;
use Application\Entity\TipoSetor;
/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterTiposDeSetorController extends AbstractActionController{
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
            $dql = "SELECT t FROM Application\Entity\TipoSetor AS t";
            
            $query = $objectManager->createQuery($dql);
            
            $ormPaginator = new ORMPaginator($query);
            $ormPaginatorIterator = $ormPaginator->getIterator();
            
            $adapter = new Iterator($ormPaginatorIterator);
            
            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int)  $this->params()->fromQuery('page');
            if($page) $paginator->setCurrentPageNumber($page);
            
            return array(
                'tiposDeSetor' => $paginator, 
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }
    
    public function adicionarAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $nomeTxt = $request->getPost('nomeTxt');
            $dadosFiltrados = new TipoSetorFilter($this->getServiceLocator(), $nomeTxt);
            if($dadosFiltrados->isValid()){
                $tipoDeSetor = new TipoSetor();
                $tipoDeSetor->setNome($dadosFiltrados->getValue('nomeTxt'));
                try{
                    $objectManager = $this->getServiceLocator()->get('ObjectManager');
                    $objectManager->persist($tipoDeSetor);
                    $objectManager->flush();
                    $this->flashMessenger()->addSuccessMessage("Tipo de setor adicionado com sucesso.");
                }  catch (\Exception $e){
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                }
                $this->redirect()->toRoute('tiposdesetor');
            }  else {
                foreach ($dadosFiltrados->getInvalidInput() as $erro){
                    foreach ($erro->getMessages() as $message){
                        $this->flashMessenger()->addErrorMessage($message);
                    }
                }   
                $this->redirect()->toRoute('tiposdesetor');
            }
        }
    }
    
    public function buscarAction(){
        $objectManager = $this->getObjectManager();
        $request = $this->getRequest();
        $busca = $this->params()->fromQuery('busca');
        if($busca == null){
            $this->redirect()->toRoute('tiposdesetor');
        }
        $busca = '%'.$busca.'%';
        if($request->isGet()){
            $dql = "SELECT t FROM Application\Entity\TipoSetor AS t ";
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
                $this->redirect()->toRoute('tiposdesetor');
            }
                
            return array(
                'tiposDeSetor' => $paginator, 
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }
    
    public function editarAction(){
        $request = $this->getRequest();
        $idTipoSetor = (int)$this->params()->fromRoute('id',0);
        if($idTipoSetor){
            $objectManager = $this->getObjectManager();
            $tiposDeSetor = $objectManager->getRepository('Application\Entity\TipoSetor');
            $tipoDeSetor = $tiposDeSetor->find($idTipoSetor);
            if(!$request->isPost()){
                try{
                    if($tipoDeSetor != NULL){
                        return array('tipoSetor' => $tipoDeSetor);
                    }else{
                        $this->flashMessenger()->addMessage("Tipo de setor não encotrada");
                        $this->redirect()->toRoute('tiposdesetor');
                    }
                }  catch (\Exception $e){
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                    $this->redirect()->toRoute('tiposdesetor');
                }
            }else{
                $nomeTxt = $request->getPost('nomeTxt');
                $dadosFiltrados = new TipoSetorFilter($this->getServiceLocator(), $nomeTxt);
                $tipoDeSetor->setNome($dadosFiltrados->getValue('nomeTxt'));
                $objectManager->persist($tipoDeSetor);
                $objectManager->flush();
                $this->flashMessenger()->addSuccessMessage("Tipo de setor editado com sucesso.");
                $this->redirect()->toRoute('tiposdesetor');
            }
        }
    }
    
    public function excluirAction(){
        $objectManager = $this->getObjectManager();
        $idTipoSetor = $this->params()->fromRoute('id');
        if(isset($idTipoSetor)){
            $tiposDeSetor = $objectManager->getRepository('Application\Entity\TipoSetor');
            $tipoDeSetor = $tiposDeSetor->find($idTipoSetor); 
            try{
                $objectManager->remove($tipoDeSetor);
                $objectManager->flush();
            }  catch (\Exception $e){
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('tiposdesetor');
            }
            $this->flashMessenger()->addSuccessMessage("Tipo de setor excluído com sucesso.");
            $this->redirect()->toRoute('tiposdesetor');
        }
    }

    public function visualizarAction(){
        $idTipoSetor = (int)$this->params()->fromRoute('id',0);
        if($idTipoSetor){
            $objectManager = $this->getObjectManager();
            try{
                $tiposDeSetor = $objectManager->getRepository('Application\Entity\TipoSetor');
                $tipoDeSetor = $tiposDeSetor->find($idTipoSetor);
            }  catch (\Exception $e){
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('tiposdesetor');
            }
            if($tipoDeSetor != NULL){
                return array('tipoSetor' => $tipoDeSetor);
            }else{
                $this->flashMessenger()->addMessage("Tipo de setor não encotrado");
                $this->redirect()->toRoute('tiposdesetor');
            }
        }
    }
}
