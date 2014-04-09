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
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Filter;
use Application\Entity\Secretaria;
/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterSecretariasController extends AbstractActionController{
    private $objectManager;
    
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
            $dql = "SELECT s FROM Application\Entity\Secretaria AS s";
            
            $query = $objectManager->createQuery($dql);
            
            $ormPaginator = new ORMPaginator($query);
            $ormPaginatorIterator = $ormPaginator->getIterator();
            
            $adapter = new Iterator($ormPaginatorIterator);
            
            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int)  $this->params()->fromQuery('page');
            if($page) $paginator->setCurrentPageNumber($page);
            
            return array(
                'secretarias' => $paginator, 
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }
    
    public function adicionarAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $nomeTxt = $request->getPost('nomeTxt');
            $siglaTxt = $request->getPost('siglaTxt');
            $dadosFiltrados = $this->secretariaInputFilters($nomeTxt, $siglaTxt);
            if($dadosFiltrados->isValid()){
                $secretaria = new Secretaria();
                $secretaria->setNome($dadosFiltrados->getValue('nomeTxt'));
                $secretaria->setSigla($dadosFiltrados->getValue('siglaTxt'));
                try{
                    $objectManager = $this->getServiceLocator()->get('ObjectManager');
                    $objectManager->persist($secretaria);
                    $objectManager->flush();
                }  catch (\Exception $e){
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                    $this->redirect()->toRoute('secretarias');
                }
                $this->flashMessenger()->addSuccessMessage("Secretaria adicionada com sucesso.");
                return array('sucesso' => TRUE);
            }
        }
    }
    
    public function editarAction(){
        $request = $this->getRequest();
        $idSecretaria = (int)$this->params()->fromRoute('id',0);
        if($idSecretaria){
            $objectManager = $this->getObjectManager();
            $secretarias = $objectManager->getRepository('Application\Entity\Secretaria');
            $secretaria = $secretarias->find($idSecretaria);
            if(!$request->isPost()){
                try{
                    if($secretaria != NULL){
                        return array('secretaria' => $secretaria);
                    }else{
                        $this->flashMessenger()->addMessage("Secretaria não encotrada");
                        $this->redirect()->toRoute('secretarias');
                    }
                }  catch (\Exception $e){
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                    $this->redirect()->toRoute('secretarias');
                }
            }else{
                $nomeTxt = $request->getPost('nomeTxt');
                $siglaTxt = $request->getPost('siglaTxt');
                $dadosFiltrados = $this->secretariaInputFilters($nomeTxt, $siglaTxt);
                $secretaria->setNome($dadosFiltrados->getValue('nomeTxt'));
                $secretaria->setSigla($dadosFiltrados->getValue('siglaTxt'));
                $objectManager->persist($secretaria);
                $objectManager->flush();
                $this->flashMessenger()->addSuccessMessage("Secretaria editada com sucesso.");
                $this->redirect()->toRoute('secretarias');
            }
        }
    }
    
    public function excluirAction(){
        $objectManager = $this->getObjectManager();
        $idSecretaria = $this->params()->fromRoute('id');
        if(isset($idSecretaria)){
            $secretarias = $objectManager->getRepository('Application\Entity\Secretaria');
            $secretaria = $secretarias->find($idSecretaria); 
            try{
                $objectManager->remove($secretaria);
                $objectManager->flush();
            }  catch (\Exception $e){
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('secretarias');
            }
            $this->flashMessenger()->addSuccessMessage("Secretaria excluida com sucesso.");
            $this->redirect()->toRoute('secretarias');
        }
    }

    public function vizualizarAction(){
        $idSecretaria = (int)$this->params()->fromRoute('id',0);
        if($idSecretaria){
            $objectManager = $this->getObjectManager();
            try{
                $secretarias = $objectManager->getRepository('Application\Entity\Secretaria');
                $secretaria = $secretarias->find($idSecretaria);
            }  catch (\Exception $e){
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('secretarias');
            }
            if($secretaria != NULL){
                return array('secretaria' => $secretaria);
            }else{
                $this->flashMessenger()->addMessage("Secretaria não encotrada");
                $this->redirect()->toRoute('secretarias');
            }
        }
    }
    

    public function secretariaInputFilters($nomeTxt, $siglaTxt){
        $nomeFilter = new Input('nomeTxt');
        
        $nomeStringLength = new Validator\StringLength(array('max' => 150,'min' => 15));
        $nomeStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
                'O nome \'%value%\' é muito curto, o valor mínimo é %min%',
            Validator\StringLength::TOO_LONG  =>
                'O nome \'%value%\' é muito longo, o valor máximo é %max%'
        ));
        
        $nomeFilter->getValidatorChain()
                   ->attach($nomeStringLength)
                   ->attach(new Validator\NotEmpty());
        $nomeFilter->getFilterChain()
                   ->attach(new Filter\HtmlEntities())
                   ->attach(new Filter\StringTrim())
                   ->attach(new Filter\StripTags());
        
        $siglaFilter = new Input('siglaTxt');
        
        $siglaStringLength = new Validator\StringLength(array('max' => 10,'min' => 2));
        $siglaStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
                'A sigla \'%value%\' é muito curto, o valor mínimo é %min%',
            Validator\StringLength::TOO_LONG  =>
                'A sigla \'%value%\' é muito longo, o valor máximo é %max%'
        ));
        
        $siglaFilter->getValidatorChain()
                   ->attach($siglaStringLength)
                   ->attach(new Validator\NotEmpty());
        $siglaFilter->getFilterChain()
                   ->attach(new Filter\HtmlEntities())
                   ->attach(new Filter\StringTrim())
                   ->attach(new Filter\StripTags());
        
        $inputFilter = new InputFilter();
        $inputFilter->add($nomeFilter)
                    ->add($siglaFilter)
                    ->setData(array(
                        'nomeTxt' => $nomeTxt,
                        'siglaTxt' => $siglaTxt
                    ));
        
        return $inputFilter;
    }
}
