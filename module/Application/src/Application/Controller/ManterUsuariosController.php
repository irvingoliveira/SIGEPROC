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
use Application\Exception\UserNotFoundException;
use Application\Exception\InactiveUserException;
/**
 * Description of ManterUsuariosController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterUsuariosController extends AbstractActionController {

    private $objectManager;
    private $authService;
    
    public function getAuthService(){
        if($this->authService == NULL){
            $this->authService = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authService;
    }

    public function getObjectManager() {
        if (!$this->objectManager) {
            $this->objectManager = $this->getServiceLocator()->get('ObjectManager');
        }
        return $this->objectManager;
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
                            'setor' => $identity->getSetor(),
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
                    try {
                        throw new InactiveUserException();
                    } catch (InactiveUserException $e) {
                        return array('errMsg' => $e->getMessage());
                    }
                }
            } else {
                try{
                    throw new UserNotFoundException();
                }  catch (UserNotFoundException $e){
                    return array('errMsg' => $e->getMessage());
                }
            }
        }
    }
    
    public function logoutAction(){
        $authService = $this->getAuthService();
        $authService->clearIdentity();
        $authService->getStorage()->clear();
        $this->redirect()->toRoute('autenticar');
    }
}
    