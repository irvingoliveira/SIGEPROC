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

use Application\Filters\GuiaDeRemessaFilter;

use Application\DAL\GuiaDeRemessaDAO;
use Application\DAL\ProcessoDAO;
use Application\DAL\SecretariaDAO;
use Application\DAL\UsuarioDAO;

/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterGuiasDeRemessaController extends AbstractActionController {

    public function indexAction() {
        $processoDAO = new ProcessoDAO($this->getServiceLocator());
        
        $authService = $this->getServiceLocator()->get('AuthService');
        $usuarioAuth = $authService->getIdentity();
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
        $query = $processoDAO->getProcessosNoSetor($usuarioDAO->lerPorId($usuarioAuth['id']));

        $ormPaginator = new ORMPaginator($query);
        $ormPaginatorIterator = $ormPaginator->getIterator();

        $adapter = new Iterator($ormPaginatorIterator);

        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $page = (int) $this->params()->fromQuery('page');
        if ($page)
            $paginator->setCurrentPageNumber($page);

        return array(
            'processos' => $paginator,
            'orderby' => $this->params()->fromQuery('orderby'),
        );
    }

    public function adicionarAction() {
        $request = $this->getRequest();
        $processoDAO = new ProcessoDAO($this->getServiceLocator());
        
        if (!$request->isPost()){
            $authService = $this->getServiceLocator()->get('AuthService');
            $usuarioAuth = $authService->getIdentity();
            $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
            $query = $processoDAO->getProcessosNoSetor($usuarioDAO->lerPorId($usuarioAuth['id']));
            $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
            $secretarias = $secretariaDAO->lerRepositorio();
        
            return array(
                'processos' => $query->getResult(),
                'secretarias' => $secretarias
            );
        }
        
        $ids = $request->getPost('ids');
        
        foreach ($ids as $id){
            if (!is_int($id)){
                $mensagem = "Foi informado um processo inválido. Caso o erro ";
                $mensagem .= "persista, entre em contato com um administrador ";
                $mensagem .= "do sistema.";
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toUrl('/guiasderemessa');
                return;
            }
        }
        
        $dadosFiltrados = new GuiaDeRemessaFilter($request->getPost('setorSlct'));
        
        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toUrl('/processos/adicionar');
            return;
        }
        
        $processos = new ArrayCollection();
        
        foreach ($ids as $id){
            $processos->add($processoDAO->lerPorId($id));
        }
        
        $parametros = new ArrayCollection();
        $parametros->set('processos', $processos);
        $parametros->set('setor', $processos);
        $dataSistema = new \DateTime('NOW');
        $parametros->set('anoExercicio', $dataSistema->format('Y'));
        $parametros->set('dataCriacao', $dataSistema->format('Y-m-a'));
        try {
            $guiaDeRemessaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());
            $guiaDeRemessaDAO->salvar($parametros);
            $this->flashMessenger()->addSuccessMessage("Guia de remessa adicionada com sucesso.");
            $this->redirect()->toRoute('guiasderemessa');

        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        $this->redirect()->toRoute('guiasderemessa');
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
            $dql = "SELECT s FROM Application\Entity\Secretaria AS s ";
            $dql.= "WHERE s.nome LIKE ?1 ";
            $dql.= "OR s.sigla LIKE ?1";

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
                $this->flashMessenger()->addErrorMessage("Secretaria não encontrada.");
                $this->redirect()->toRoute('secretarias');
            }
            return array(
                'secretarias' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }

    public function editarAction() {
        $request = $this->getRequest();
        $idSecretaria = (int) $this->params()->fromRoute('id', 0);
        if ($idSecretaria) {
            $objectManager = $this->getObjectManager();
            $secretarias = $objectManager->getRepository('Application\Entity\Secretaria');
            $secretaria = $secretarias->find($idSecretaria);
            if (!$request->isPost()) {
                try {
                    if ($secretaria != NULL) {
                        return array('secretaria' => $secretaria);
                    } else {
                        $this->flashMessenger()->addMessage("Secretaria não encotrada");
                        $this->redirect()->toRoute('secretarias');
                    }
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                    $this->redirect()->toRoute('secretarias');
                }
            } else {
                $nomeTxt = $request->getPost('nomeTxt');
                $siglaTxt = $request->getPost('siglaTxt');
                $dadosFiltrados = new SecretariaFilter($objectManager, $nomeTxt, $siglaTxt);
                $secretaria->setNome($dadosFiltrados->getValue('nomeTxt'));
                $secretaria->setSigla($dadosFiltrados->getValue('siglaTxt'));
                $objectManager->persist($secretaria);
                $objectManager->flush();
                $this->flashMessenger()->addSuccessMessage("Secretaria editada com sucesso.");
                $this->redirect()->toRoute('secretarias');
            }
        }
    }

    public function excluirAction() {
        $objectManager = $this->getObjectManager();
        $idSecretaria = $this->params()->fromRoute('id');
        if (isset($idSecretaria)) {
            $secretarias = $objectManager->getRepository('Application\Entity\Secretaria');
            $secretaria = $secretarias->find($idSecretaria);
            try {
                $objectManager->remove($secretaria);
                $objectManager->flush();
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('secretarias');
            }
            $this->flashMessenger()->addSuccessMessage("Secretaria excluida com sucesso.");
            $this->redirect()->toRoute('secretarias');
        }
    }

    public function visualizarAction() {
        $idSecretaria = (int) $this->params()->fromRoute('id', 0);
        if ($idSecretaria) {
            $objectManager = $this->getObjectManager();
            try {
                $secretarias = $objectManager->getRepository('Application\Entity\Secretaria');
                $secretaria = $secretarias->find($idSecretaria);
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('secretarias');
            }
            if ($secretaria != NULL) {
                return array('secretaria' => $secretaria);
            } else {
                $this->flashMessenger()->addMessage("Secretaria não encotrada");
                $this->redirect()->toRoute('secretarias');
            }
        }
    }

}
