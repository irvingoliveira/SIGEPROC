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

use Application\Filters\ProcessoFilter;
use Application\DAL\AssuntoDAO;
use Application\DAL\ProcessoDAO;
use Application\DAL\RequerenteDAO;
use Application\DAL\StatusProcessoDAO;
use Application\DAL\SecretariaDAO;
use Application\DAL\SetorDAO;
use Application\DAL\TipoDocumentoDAO;
use Application\DAL\UsuarioDAO;

/**
 * Description of ManterUsuariosController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterProcessosController extends AbstractActionController {

    public function indexAction() {
        $request = $this->getRequest();

        $processoDAO = new ProcessoDAO($this->getServiceLocator());
        $query = $processoDAO->lerTodos();

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

    public function preencheCombos() {
        $AssuntoDAO = new AssuntoDAO($this->getServiceLocator());
        $qtdAssuntos = $AssuntoDAO->getQtdRegistros();

        if ($qtdAssuntos <= 0) {
            $mensagem = "É necessário que sejam cadastrados assuntos ";
            $mensagem .= "para que um processo possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('processos');
        }

        $tipoDocumentoDAO = new TipoDocumentoDAO($this->getServiceLocator());
        $qtdTiposDocumento = $tipoDocumentoDAO->getQtdRegistros();

        if ($qtdTiposDocumento <= 0) {
            $mensagem = "É necessário que sejam cadastrados tipos de documento ";
            $mensagem .= "para que um processo possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('processos');
        }

        $tiposDeDocumento = $tipoDocumentoDAO->lerRepositorio();

        $statusProcessosDAO = new StatusProcessoDAO($this->getServiceLocator());
        $qtdStatus = $statusProcessosDAO->getQtdRegistros();

        if ($qtdStatus <= 0) {
            $mensagem = "É necessário que sejam cadastrados status ";
            $mensagem .= "para que um processo possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('processos');
            return;
        }

        $assuntos = $AssuntoDAO->lerRepositorio();

        $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
        $secretarias = $secretariaDAO->lerRepositorio();

        return array(
            'assuntos' => $assuntos,
            'secretarias' => $secretarias,
            'tiposDeDocumento' => $tiposDeDocumento
        );
    }

    public function adicionarAction() {
        $request = $this->getRequest();

        if (!$request->isPost())
            return $this->preencheCombos();

        $assuntoTxt = $request->getPost('assuntoTxt');
        $volumeTxt = $request->getPost('volumeTxt');
        $requerenteTxt = $request->getPost('requerenteTxt');
        $dddTxt = $request->getPost('dddTxt');
        $telefoneTxt = $request->getPost('telefoneTxt');
        $secretariaSlct = $request->getPost('secretariaSlct');
        $setorSlct = $request->getPost('setorSlct');
        $tipoDocumentoSlct = $request->getPost('tipoDocumentoSlct');
        $numeroTxt = $request->getPost('numeroTxt');
        $digitoTxt = $request->getPost('digitoTxt');
        $emissaoDt = $request->getPost('emissaoDt');
        $orgaoEmissorTxt = $request->getPost('orgaoEmissorTxt');

        $assuntoDAO = new AssuntoDAO($this->getServiceLocator());
        $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
        $setorDAO = new SetorDAO($this->getServiceLocator());
        $tipoDocumentoDAO = new TipoDocumentoDAO($this->getServiceLocator());

        $dadosFiltrados = new ProcessoFilter($assuntoTxt, $volumeTxt, $requerenteTxt, 
                $dddTxt, $telefoneTxt, $secretariaSlct, $setorSlct, $tipoDocumentoSlct, 
                $numeroTxt, $digitoTxt, $emissaoDt, $orgaoEmissorTxt, $assuntoDAO, 
                $secretariaDAO, $setorDAO, $tipoDocumentoDAO);

        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toUrl('/processos/adicionar');
            return;
        }

        $parametros = new ArrayCollection();
        $parametros->set('assunto', $assuntoDAO->buscaExata(
                        $dadosFiltrados->getValue('assuntoTxt'))[0]);
        $parametros->set('volume', $dadosFiltrados->getValue('volumeTxt'));
        $parametros->set('requerente', $dadosFiltrados->getValue('requerenteTxt'));
        $parametros->set('ddd', $dadosFiltrados->getValue('dddTxt'));
        $parametros->set('telefone', $dadosFiltrados->getValue('telefoneTxt'));
        $parametros->set('setor', $setorDAO->lerPorId(
                        $dadosFiltrados->getValue('setorSlct')));
        $parametros->set('tipoDocumento', $tipoDocumentoDAO->lerPorId(
                        $dadosFiltrados->getValue('tipoDocumentoSlct')));
        $parametros->set('numero', $dadosFiltrados->getValue('numeroTxt'));
        $parametros->set('digito', $dadosFiltrados->getValue('digitoTxt'));
        $parametros->set('emissao', new \DateTime($dadosFiltrados->getValue('emissaoDt')));
        $parametros->set('orgaoEmissor', $dadosFiltrados->getValue('orgaoEmissorTxt'));
        $dataSistema = new \DateTime('NOW');
        $parametros->set('anoExercicio', (int)$dataSistema->format('Y'));
        $parametros->set('dataAbertura', $dataSistema);
        $authService = $this->getServiceLocator()->get('AuthService');
        $usuario = $authService->getIdentity();
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());
        $usuario = $usuarioDAO->lerPorId($usuario['id']);
        $parametros->set('usuario', $usuario);
        $parametros->set('postoDeTrabalho', $usuario->getSetorAtual()->getSetor());

        try {
            $processoDAO = new ProcessoDAO($this->getServiceLocator());
            $processoDAO->salvar($parametros);
            $this->flashMessenger()->addSuccessMessage("Processo adicionado com sucesso.");
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        $this->redirect()->toRoute('processos');
    }

    public function buscarAction() {
        $request = $this->getRequest();
        $busca = $this->params()->fromQuery('busca');
        if ($busca == null) {
            $this->redirect()->toRoute('processos');
            return;
        }

        if (!$request->isGet()) {
            $this->redirect()->toRoute('processos');
            return;
        }
        
        $parametrosBusca = new ArrayCollection();
        
        if(strpos($busca, '/') === FALSE){
            $parametrosBusca->set('numero', $busca);
        }else{
            $partesBusca = explode('/', $busca);
            $parametrosBusca->set('numero', $partesBusca[0]);
            $parametrosBusca->set('anoExercicio', $partesBusca[1]);
        }

        $processoDAO = new ProcessoDAO($this->getServiceLocator());
        $query = $processoDAO->buscaPersonalizada($parametrosBusca);

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
            $this->flashMessenger()->addErrorMessage("Processo não encontrada.");
            $this->redirect()->toRoute('processos');
        }
        return array(
            'processos' => $paginator,
            'orderby' => $this->params()->fromQuery('orderby'),
        );
    }
    
    public function editarAction() {
        
    }
    
    public function excluirAction() {
        $idProcesso = $this->params()->fromRoute('id');
        if (!$idProcesso) {
            $this->flashMessenger()->addErrorMessage("Processo não encontrado.");
            $this->redirect()->toRoute('processos');
            return;
        }
        try {
            $processoDAO = new ProcessoDAO($this->getServiceLocator());
            $processo = $processoDAO->lerPorId($idProcesso);
            if ($processo->getStatus() != "1") {
                $mensagem = "Somente processos que não possuam trâmie podem ser ";
                $mensagem.= "excluídos.";
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toRoute('processos');
                return;
            }
            $processoDAO->excluir($idProcesso);
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ou ";
            $mensagem.= "entre em contato com um administrador do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('processos');
        }
        $this->flashMessenger()->addSuccessMessage("Proceso excluído com sucesso.");
        $this->redirect()->toRoute('processos');
    }

    public function visualizarAction() {
        $idProcesso = (int) $this->params()->fromRoute('id', 0);
        if (!$idProcesso) {
            $this->flashMessenger()->addMessage("Processo não encotrado");
            $this->redirect()->toRoute('processos');
        }
        try {
            $processoDAO = new ProcessoDAO($this->getServiceLocator());
            $processo = $processoDAO->lerPorId($idProcesso);
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('processos');
        }
        if ($processo != NULL) {
            return array('processo' => $processo);
        } else {
            $this->flashMessenger()->addMessage("Processo não encotrado");
            $this->redirect()->toRoute('processos');
        }
    }
    
    
    public function processosNoSetorAction(){
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
        
                if ($paginator->count() == 0) {
            $mensagem = "Não existem processos no setor.";
            $this->flashMessenger()->addInfoMessage($mensagem);
            $this->redirect()->toRoute('home');
            return;
        }
        
        return array(
            'processos' => $paginator,
            'orderby' => $this->params()->fromQuery('orderby'),
        );
    }
    

    public function requerenteAutoCompleteAction() {
        $termo = $this->params()->fromQuery('term');

        $requerenteDAO = new RequerenteDAO($this->getServiceLocator());
        $requerentes = $requerenteDAO->busca($termo . '%');

        $json = '[';
        foreach ($requerentes as $key => $requerente) {
            if ($key != 0)
                $json .= ',';
            $json.= '{"value":"' . $requerente->getNome() . '"}';
        }
        $json.= ']';

        echo $json;
        die();
    }

    public function getRequerenteInfoAction() {
        $request = $this->getRequest();
        $termo = $request->getPost('data');
        $requerenteDAO = new RequerenteDAO($this->getServiceLocator());
        $requerentes = $requerenteDAO->busca($termo);

        $json = '{"ddd":"' . $requerentes[0]->getTelefone()->getDdd() . '",';
        $json.= '"telefone":"' . $requerentes[0]->getTelefone()->getNumero() . '",';
        $json.= '"tipoDocumento":"' . $requerentes[0]->getDocumento()->getTipo() . '",';
        $json.= '"documento":"' . $requerentes[0]->getDocumento()->getNumero() . '",';
        $json.= '"digito":"' . $requerentes[0]->getDocumento()->getDigito() . '",';
        $json.= '"emissao":"' . $requerentes[0]->getDocumento()->getDataEmissao()->format('Y-m-d') . '",';
        $json.= '"orgaoEmissor":"' . $requerentes[0]->getDocumento()->getOrgaoEmissor() . '"}';

        echo $json;
        die();
    }    
}
