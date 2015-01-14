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
use Application\DAL\OrgaoExternoDAO;
use Application\DAL\PostoDeTrabalhoDAO;
use Application\DAL\ProcessoDAO;
use Application\DAL\SecretariaDAO;
use Application\DAL\SetorDAO;
use Application\DAL\StatusProcessoDAO;
use Application\DAL\UsuarioDAO;

use Application\Entity\PostoDeTrabalho;

/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterGuiasDeRemessaController extends AbstractActionController {

    public function indexAction() {
        $guiaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());

        $authService = $this->getServiceLocator()->get('AuthService');
        $usuarioAuth = $authService->getIdentity();
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());

        $query = $guiaDAO->getGuiasEnviadasNaoRecebidas(
                $usuarioDAO->lerPorId($usuarioAuth['id']));

        $ormPaginator = new ORMPaginator($query);
        $ormPaginatorIterator = $ormPaginator->getIterator();

        $adapter = new Iterator($ormPaginatorIterator);

        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $page = (int) $this->params()->fromQuery('page');
        if ($page)
            $paginator->setCurrentPageNumber($page);

        if ($paginator->count() == 0) {
            $mensagem = "Não existem guias de remessa enviadas.";
            $this->flashMessenger()->addInfoMessage($mensagem);
            $this->redirect()->toRoute('home');
            return;
        }
        return array(
            'guias' => $paginator,
            'orderby' => $this->params()->fromQuery('orderby'),
        );
    }

    public function receberAction() {
        $idGuia = $this->params()->fromRoute('id');
        if ($idGuia != NULL) {
            try {
                $authService = $this->getServiceLocator()->get('AuthService');
                $usuarioAuth = $authService->getIdentity();
                $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
                $usuario = $usuarioDAO->lerPorId($usuarioAuth['id']);
                $guiaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());
                $parametros = new ArrayCollection();
                $parametros->set('dataRecebimento', new \DateTime('NOW'));
                $parametros->set('recebedor', $usuario);
                $guia = $guiaDAO->lerPorId($idGuia);

                $statusDAO = new StatusProcessoDAO($this->getServiceLocator());

                $parametrosProcesso = new ArrayCollection();
                $parametrosProcesso->set('postoDeTrabalho', $usuario->getSetorAtual()->getSetor());

                $processoDAO = new ProcessoDAO($this->getServiceLocator());

                foreach ($guia->getProcessos() as $processo) {
                    $processoDAO->editar($processo->getIdProcesso(), $parametrosProcesso);
                    $processoDAO->setStatus($processo->getIdProcesso(), 
                            $statusDAO->lerPorId(3));
                }
                $guiaDAO->editar($idGuia, $parametros);

                $mensagem = 'Guia recebida com sucesso. ';
                $this->flashMessenger()->addSuccessMessage($mensagem);
                $this->redirect()->toRoute('guiasderemessa', array('action' => 'receber'));
                return;
            } catch (Exception $e) {
                $mensagem = 'Não foi possível receber esta guia, tente novamente ';
                $mensagem.= 'ou entre em contato com um adiministradopr do sistema. ';
                $this->flashMessenger()->addInfoMessage($mensagem);
                $this->redirect()->toRoute('guiasderemessa', array('action' => 'receber'));
                return;
            }
        }

        $guiaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());

        $authService = $this->getServiceLocator()->get('AuthService');
        $usuarioAuth = $authService->getIdentity();
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
        $query = $guiaDAO->getGuiasAReceber(
                $usuarioDAO->lerPorId($usuarioAuth['id']));

        $ormPaginator = new ORMPaginator($query);
        $ormPaginatorIterator = $ormPaginator->getIterator();

        $adapter = new Iterator($ormPaginatorIterator);

        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $page = (int) $this->params()->fromQuery('page');
        if ($page)
            $paginator->setCurrentPageNumber($page);

        if ($paginator->count() == 0) {
            $mensagem = "Não existem guias para receber.";
            $this->flashMessenger()->addInfoMessage($mensagem);
            $this->redirect()->toRoute('home');
            return;
        }

        return array(
            'guias' => $paginator,
            'orderby' => $this->params()->fromQuery('orderby'),
        );
    }

    public function rejeitarAction() {
        $idGuia = $this->params()->fromRoute('id');
        if ($idGuia == NULL) {
            $this->redirect()->toRoute('guiasderemessa', array('action' => 'receber'));
            return;
        }
        try {
            $authService = $this->getServiceLocator()->get('AuthService');
            $usuarioAuth = $authService->getIdentity();
            $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
            $usuario = $usuarioDAO->lerPorId($usuarioAuth['id']);
            $guiaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());
            $parametros = new ArrayCollection();
            $parametros->set('dataRecebimento', new \DateTime('NOW'));
            $parametros->set('recebedor', $usuario);
            $parametros->set('rejeitada', TRUE);
            $guia = $guiaDAO->lerPorId($idGuia);

            $statusDAO = new StatusProcessoDAO($this->getServiceLocator());
            $processoDAO = new ProcessoDAO($this->getServiceLocator());

            foreach ($guia->getProcessos() as $processo) {
                $processoDAO->setStatus($processo->getIdProcesso(), $statusDAO->lerPorId(3));
            }
            $guiaDAO->editar($idGuia, $parametros);

            $mensagem = 'Guia recjeitada com sucesso. ';
            $this->flashMessenger()->addSuccessMessage($mensagem);
            $this->redirect()->toRoute('guiasderemessa', array('action' => 'receber'));
            return;
        } catch (Exception $e) {
            $mensagem = 'Não foi possível rejeitar esta guia, tente novamente ';
            $mensagem.= 'ou entre em contato com um adiministradopr do sistema. ';
            $this->flashMessenger()->addInfoMessage($mensagem);
            $this->redirect()->toRoute('guiasderemessa', array('action' => 'receber'));
            return;
        }
    }

    public function adicionarAction() {
        $request = $this->getRequest();
        $processoDAO = new ProcessoDAO($this->getServiceLocator());
        $authService = $this->getServiceLocator()->get('AuthService');
        $usuarioAuth = $authService->getIdentity();
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
        $usuario = $usuarioDAO->lerPorId($usuarioAuth['id']);

        if (!$request->isPost()) {
            $query = $processoDAO->getProcessosNoSetor($usuario);
            $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
            $secretarias = $secretariaDAO->lerRepositorio();
            $orgaoDAO = new OrgaoExternoDAO($this->getServiceLocator());
            $orgaos = $orgaoDAO->lerRepositorio();

            return array(
                'processos' => $query->getResult(),
                'secretarias' => $secretarias,
                'orgaos' => $orgaos
            );
        }

        $ids = $request->getPost('ids');

        foreach ($ids as $id) {
            if (!is_int((int) $id)) {
                $mensagem = "Foi informado um processo inválido. Caso o erro ";
                $mensagem .= "persista, entre em contato com um administrador ";
                $mensagem .= "do sistema.";
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toUrl('/guiasderemessa');
                return;
            }
        }

        $setorDAO = new SetorDAO($this->getServiceLocator());
        $orgaoExternoDAO = new OrgaoExternoDAO($this->getServiceLocator());

        $dadosFiltrados = new GuiaDeRemessaFilter($request->getPost('setorSlct'), $request->getPost('orgaoExternoSlct'), $setorDAO, $orgaoExternoDAO);

        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toRoute('guiasderemessa');
            return;
        }

        $processos = new ArrayCollection();
        $statusProcessoDAO = new StatusProcessoDAO($this->getServiceLocator());
        foreach ($ids as $id) {
            $processo = $processoDAO->editar($id, new ArrayCollection(array(
                'status' => $statusProcessoDAO->lerPorId(2)
            )));

            $processos->add($processo);
        }
        $postoDeTrabalhoDAO = new PostoDeTrabalhoDAO($this->getServiceLocator());
        $postoDeTrabalho = $postoDeTrabalhoDAO->lerPorId($dadosFiltrados->getValue('postoSlct'));
        
        $this->validaFluxo($processos, $postoDeTrabalho);
        
        $parametros = new ArrayCollection();
        $parametros->set('processos', $processos);
        $dataSistema = new \DateTime('NOW');
        $parametros->set('anoExercicio', $dataSistema->format('Y'));
        $parametros->set('dataCriacao', $dataSistema);
        $parametros->set('emissor', $usuario);
        $parametros->set('postoDeTrabalho', $postoDeTrabalho);
        $parametros->set('rejeitada', 'FALSE');
        try {
            $guiaDeRemessaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());
            $guiaDeRemessaDAO->salvar($parametros);


            $this->flashMessenger()->addSuccessMessage("Guia de remessa adicionada com sucesso.");
            $this->redirect()->toRoute('guiasderemessa');
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
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

    public function validaFluxo(ArrayCollection $processos, PostoDeTrabalho $postoDeTrabalho){
        foreach ($processos as $processo){
            
            $guiasDeRemessa = $processo->getGuiasDeRemessa();
        }
    }
    
    public function visualizarAction() {
        $idGuiaDeRemessa = (int) $this->params()->fromRoute('id', 0);
        if ($idGuiaDeRemessa) {
            $guiaDAO = new GuiaDeRemessaDAO($this->getServiceLocator());
            $guia = $guiaDAO->lerPorId($idGuiaDeRemessa);
            if ($guia != NULL) {
                return array('guia' => $guia);
            } else {
                $this->flashMessenger()->addMessage("Guia de remessa não encontrada");
                $this->redirect()->toRoute('guiasderemessa');
            }
        }
        $this->flashMessenger()->addErrorMessage("Guia de remessa não encontrada.");
        $this->redirect()->toRoute('guiasderemessa', array('action' => 'receber'));
    }

}
