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
use Application\DAL\GuiaDeRemessaDAO;
use Application\DAL\ProcessoDAO;
use Application\DAL\UsuarioDAO;
/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class IndexController extends AbstractActionController
{
    public function indexAction() {
        
        $authService = $this->getServiceLocator()->get('AuthService');
        $usuario = $authService->getIdentity();
        
        $processosNoSetor = count($this->getProcessosNoSetorDoUsuario($usuario));
        $guiasEnviadas = count($this->getGuiasEnviadasNaoRecebidas($usuario));
        $guiasAReceber = count($this->getGuiasAReceber($usuario));
        return array(
            'processosNoSetor' => $processosNoSetor,
            'guiasEnviadas' => $guiasEnviadas,
            'guiasAReceber' => $guiasAReceber
                );
    }
   
    public function getProcessosNoSetorDoUsuario(array $usuario){
        $processoDAO = new ProcessoDAO($this->getServiceLocator());
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
        return $processoDAO->getProcessosNoSetor(
                $usuarioDAO->lerPorId($usuario['id']))->getResult();
    }
    
    public function getGuiasEnviadasNaoRecebidas(array $usuario){
        $guiaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
        return $guiaDAO->getGuiasEnviadasNaoRecebidas(
                $usuarioDAO->lerPorId($usuario['id']))->getResult();
    }
    
    public function getGuiasAReceber(array $usuario){
        $guiaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
        return $guiaDAO->getGuiasAReceber(
                $usuarioDAO->lerPorId($usuario['id']))->getResult();
    }
}
