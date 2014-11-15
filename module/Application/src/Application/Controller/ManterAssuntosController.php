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
use Application\Filters\AssuntoFilter;
use Application\DAL\AssuntoDAO;
use Application\DAL\SetorDAO;
use Application\DAL\SecretariaDAO;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ManterUsuariosController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterAssuntosController extends AbstractActionController {

    public function indexAction() {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $assuntoDAO = new AssuntoDAO($this->getServiceLocator());

            $ormPaginator = new ORMPaginator($assuntoDAO->lerTodos());
            $ormPaginatorIterator = $ormPaginator->getIterator();

            $adapter = new Iterator($ormPaginatorIterator);

            $paginator = new Paginator($adapter);
            $paginator->setDefaultItemCountPerPage(10);
            $page = (int) $this->params()->fromQuery('page');
            if ($page) {
                $paginator->setCurrentPageNumber($page);
            }
            return array(
                'assuntos' => $paginator,
                'orderby' => $this->params()->fromQuery('orderby'),
            );
        }
    }

    public function preencheCombos() {
        $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
        $qtdSecretaria = $secretariaDAO->getQtdRegistros();

        if ($qtdSecretaria <= 0) {
            $mensagem = "É necessário que sejam cadastradas secretarias ";
            $mensagem .= "para que um assunto possa ser cadastrado.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('assuntos');
        }
        $secretarias = $secretariaDAO->lerRepositorio();
        return array(
            'secretarias' => $secretarias
        );
    }

    public function adicionarAction() {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $this->preencheCombos();
        }
        $nomeTxt = $request->getPost('nomeTxt');
        $secretariaSlct = $request->getPost('secretariaSlct');
        $setorSlct = $request->getPost('setorSlct');
        $descricaoTxt = $request->getPost('descricaoTxt');

        $dadosFiltrados = new AssuntoFilter($this->getServiceLocator(), $nomeTxt, $secretariaSlct, $setorSlct, $descricaoTxt);

        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toUrl('/assuntos/adicionar');
            return;
        }

        $setorDAO = new SetorDAO($this->getServiceLocator());
        $setor = $setorDAO->lerPorId($dadosFiltrados->getValue('setorSlct'));

        $parametros = new ArrayCollection();
        $parametros->set('nome', $dadosFiltrados->getValue('nomeTxt'));
        $parametros->set('setor', $setor);
        $parametros->set('descricao', $dadosFiltrados->getValue('descricaoTxt'));

        try {
            $assuntoDAO = new AssuntoDAO($this->getServiceLocator());
            $assuntoDAO->salvar($parametros);
            $this->flashMessenger()->addSuccessMessage("Assunto adicionado com sucesso.");
        } catch (\Doctrine\DBAL\DBALException $e) {
            if (strpos($e->getMessage(), 'SQLSTATE[23000]') > 0) {
                $mensagem = "Já existe um assunto cadastrado com este nome ";
            } else {
                $mensagem = "Ocorreu um erro na operação, tente novamente ";
                $mensagem .= "ou entre em contato com um administrador ";
                $mensagem .= "do sistema.";
            }
            $this->flashMessenger()->addErrorMessage($mensagem);
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        $this->redirect()->toRoute('assuntos');
    }

    public function editarAction() {
        $request = $this->getRequest();
        $idAssunto = (int) $this->params()->fromRoute('id', 0);

        $setorDAO = new SetorDAO($this->getServiceLocator());
        $AssuntoDAO = new AssuntoDAO($this->getServiceLocator());
        $assunto = $AssuntoDAO->lerPorId($idAssunto);
    
        if (!$request->isPost()) {
            try {
                if ($assunto != NULL) {
                    $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
                    $secretarias = $secretariaDAO->lerRepositorio();
                    $setores = $setorDAO->lerSetoresPorSecretaria($assunto->getSetor()->getSecretaria());

                    return array(
                        'secretarias' => $secretarias,
                        'setores' => $setores,
                        'assunto' => $assunto
                    );
                } else {
                    $this->flashMessenger()->addMessage("Assunto não encotrado.");
                    $this->redirect()->toRoute('assuntos');
                }
            } catch (\Exception $e) {
                $mensagem = "Ocorreu um erro na operação, tente ";
                $mensagem .= "novamente ou entre em contato com um ";
                $mensagem .= "administrador do sistema.";
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toRoute('assuntos');
                return;
            }
        }

        $nomeTxt = $request->getPost('nomeTxt');
        $secretariaSlct = $request->getPost('secretariaSlct');
        $setorSlct = $request->getPost('setorSlct');
        $descricaoTxt = $request->getPost('descricaoTxt');

        $dadosFiltrados = new AssuntoFilter($this->getServiceLocator(), $nomeTxt, $secretariaSlct, $setorSlct, $descricaoTxt);

        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toRoute('assuntos');
            return;
        }

        $setor = $setorDAO->lerPorId($dadosFiltrados->getValue('setorSlct'));

        $parametros = new ArrayCollection();
        $parametros->set('nome', $dadosFiltrados->getValue('nomeTxt'));
        $parametros->set('setor', $setor);
        $parametros->set('descricao', $dadosFiltrados->getValue('descricaoTxt'));
        
        try {
            $assuntoDAO = new AssuntoDAO($this->getServiceLocator());
            $assuntoDAO->editar($idAssunto, $parametros);
            $this->flashMessenger()->addSuccessMessage("Assunto editado com sucesso.");
        } catch (\Doctrine\DBAL\DBALException $e) {
            if (strpos($e->getMessage(), 'SQLSTATE[23000]') > 0) {
                $mensagem = "Já existe um assunto cadastrado com este nome ";
            } else {
                $mensagem = "Ocorreu um erro na operação, tente novamente ";
                $mensagem .= "ou entre em contato com um administrador ";
                $mensagem .= "do sistema.";
            }
            $this->flashMessenger()->addErrorMessage($mensagem);
        } catch (\Exception $e) {
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        $this->redirect()->toRoute('assuntos');
    }

    public function excluirAction() {
        $idAssunto = $this->params()->fromRoute('id');
        if (isset($idAssunto)) {
            try {
                $assuntoDAO = new AssuntoDAO($this->getServiceLocator());
                $assuntoDAO->excluir($idAssunto);
            } catch (\Exception $e) {
                $mensagem = "Ocorreu um erro na operação, tente novamente ou ";
                $mensagem.= "entre em contato com um administrador do sistema.";
                $this->flashMessenger()->addErrorMessage($mensagem);
                $this->redirect()->toRoute('assuntos');
            }
            $this->flashMessenger()->addSuccessMessage("Assunto excluído com sucesso.");
            $this->redirect()->toRoute('assuntos');
        }
    }

    public function visualizarAction() {
        $idAssunto = (int) $this->params()->fromRoute('id', 0);
        if ($idAssunto) {
            try {
                $assuntoDAO = new AssuntoDAO($this->getServiceLocator());
                $assunto = $assuntoDAO->lerPorId($idAssunto);
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage("Ocorreu um erro na operação, tente novamente ou entre em contato com um administrador do sistema.");
                $this->redirect()->toRoute('assuntos');
            }
            if ($assunto != NULL) {
                return array('assunto' => $assunto);
            } else {
                $this->flashMessenger()->addMessage("Assunto não encotrado");
                $this->redirect()->toRoute('assuntos');
            }
        }
    }

    public function getSetorComboBySecretariaAction() {
        $request = $this->getRequest();
        $idSecretaria = $request->getPost('data');
        $selecionado = $request->getPost('selected');

        $secretariaDAO = new SecretariaDAO($this->getServiceLocator());
        $secretaria = $secretariaDAO->lerPorId($idSecretaria);

        $setorDAO = new SetorDAO($this->getServiceLocator());
        $setores = $setorDAO->lerSetoresPorSecretaria($secretaria);
        $qtdSetores = count($setores);

        if ($qtdSetores == 0) {
            echo '<option>---- Não há setor cadastrado nesta secretaria----</option>';
        } else {
            echo '<option>----Selecione um setor----</option>';
            foreach ($setores as $setor) {
                if ($setor->getIdSetor() == $selecionado)
                    echo '<option value="' . $setor->getIdSetor() . '" selected="selected">' . $setor->getTipo()->getNome() . ' de ' . $setor->getNome() . '</option>';
                else
                    echo '<option value="' . $setor->getIdSetor() . '">' . $setor->getTipo()->getNome() . ' de ' . $setor->getNome() . '</option>';
            }
        }
        die();
    }

}
