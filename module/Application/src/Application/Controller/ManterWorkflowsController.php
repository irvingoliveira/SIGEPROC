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
use Application\DAL\OrgaoExternoDAO;
use Application\DAL\SecretariaDAO;
use Application\DAL\WorkflowDAO;
use Application\Filters\WorkflowFilter;
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
    
        $dadosFiltrados = new WorkflowFilter($this->getServiceLocator(), $request->getPost('postos'));
        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toUrl('/workflows/adicionar');
            return $this->preencheCombo();
        }
        
        $workflowDAO = new WorkflowDAO($this->getServiceLocator());
        
    }
}
