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
use Application\DAL\UsuarioDAO;
use Application\Exception\UserNotFoundException;
use Application\Exception\InactiveUserException;
/**
 * Description of ManterUsuariosController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterUsuariosController extends AbstractActionController {

    private $authService;
    
    public function getAuthService(){
        if($this->authService == NULL){
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authService;
    }

    public function autenticarAction() {
        $request = $this->getRequest();
        $authService = $this->getAuthService();
        if($authService->getIdentity())
            $this->redirect()->toRoute('home');
        if ($request->isPost()) {
            $email = $request->getPost('emailTxt');
            $senha = $request->getPost('senhaPwd');
            $adapter = $authService->getAdapter();
            $adapter->setIdentityValue($email);
            $adapter->setCredentialValue($senha);
            $result = $authService->authenticate($adapter);
            if ($result->isValid()) {
                $identity = $result->getIdentity();
                if ($identity->isAtivo()) {
                    try {
                        $storage = $authService->getStorage();
                        $storage->write(array(
                            'id' => $identity->getIdUsuario(),
                            'matricula' => $identity->getMatricula(),
                            'nome' => $identity->getNome(),
                            'setor' => $identity->getSetorAtual(),
                            'email' => $identity->getEmail(),
                            'funcao' => $identity->getFuncao()->getNome()
                        ));
                        
                        $this->redirect()->toRoute('home');
                    } catch (Exception $e) {
                        $authService = $this->getAuthService();
                        $authService->clearIdentity();
                        $authService->getStorage()->clear();
                        $this->redirect()->toRoute('autenticar');
                    }
                } else {
                    $authService->clearIdentity();
                    $authService->getStorage()->clear();
                    $mensagem = 'Não foi possível autenticar com estas informações ';
                    $mensagem.= 'de login. Tente novamente.';
                    $this->flashMessenger()->addErrorMessage($mensagem);
                    $this->redirect()->toRoute('autenticar');
                }
            } else {
                $authService->clearIdentity();
                $authService->getStorage()->clear();
                $mensagem = 'Não foi possível autenticar com estas informações ';
                $mensagem.= 'de login. Tente novamente.';
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toRoute('autenticar');
             }
        }
    }
    
    public function indexAction() {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $usuarioDAO = new UsuarioDAO($this->getServiceLocator());

            $ormPaginator = new ORMPaginator($usuarioDAO->lerTodos());
            $ormPaginatorIterator = $ormPaginator->getIterator();

            $adapter = new Iterator($ormPaginatorIterator);

            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int) $this->params()->fromQuery('page');
            if ($page)
                $paginator->setCurrentPageNumber($page);

            return array(
                'usuarios' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }
    
    
    
    public function logoutAction(){
        $authService = $this->getAuthService();
        $authService->clearIdentity();
        $authService->getStorage()->clear();
        $this->redirect()->toRoute('autenticar');
    }
}
    