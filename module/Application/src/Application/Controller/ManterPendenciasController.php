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

use Application\Filters\PendenciaFilter;
use Application\DAL\PendenciaDAO;
use Application\DAL\ProcessoDAO;
use Application\DAL\UsuarioDAO;

/**
 * Description of ManterSecretariasController
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ManterPendenciasController extends AbstractActionController {

    public function indexAction() {
        $idProcesso = (int) $this->params()->fromRoute('id', 0);

        $processoDAO = new ProcessoDAO($this->getServiceLocator());
        $processo = $processoDAO->lerPorId($idProcesso);

        $pendenciaDAO = new PendenciaDAO($this->getServiceLocator());

        $ormPaginator = new ORMPaginator(
                $pendenciaDAO->getPendenciasPorProcesso($processo));
        $ormPaginatorIterator = $ormPaginator->getIterator();

        $adapter = new Iterator($ormPaginatorIterator);

        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);
        $page = (int) $this->params()->fromQuery('page');
        if ($page)
            $paginator->setCurrentPageNumber($page);

        return array(
            'pendencias' => $paginator,
            'numeroProcesso' => $processo->getNumero() . '/' . $processo->getAnoExercicio(),
            'idProcesso' => $processo->getIdProcesso(),
            'orderby' => $this->params()->fromQuery('orderby'),
        );
    }

    public function adicionarAction() {
        $request = $this->getRequest();
        $processoDAO = new ProcessoDAO($this->getServiceLocator());

        if (!$request->isPost()) {
            $idProcesso = (int) $this->params()->fromRoute('id', 0);
            $processo = $processoDAO->lerPorId($idProcesso);
            $numeroProcesso = $processo->getNumero() . '/' . $processo->getAnoExercicio();
            return array(
                'processo' => $numeroProcesso,
                'idProcesso' => $idProcesso
            );
        }

        $post = array_merge_recursive(
                $request->getPost()->toArray(), $request->getFiles()->toArray()
        );

        $descricaoTxt = $post['descricaoTxt'];
        $imagemFile = $post['imagemFile'];
        $processoHdn = $post['processoHdn'];

        $pendenciaDAO = new PendenciaDAO($this->getServiceLocator());
        $usuarioDAO = new UsuarioDAO($this->getServiceLocator());

        $dadosFiltrados = new PendenciaFilter($descricaoTxt, $imagemFile, $processoHdn, $processoDAO);

        if (!$dadosFiltrados->isValid()) {
            foreach ($dadosFiltrados->getInvalidInput() as $erro) {
                foreach ($erro->getMessages() as $message) {
                    var_dump($message);
                    die();
                    $this->flashMessenger()->addErrorMessage($message);
                }
            }
            $this->redirect()->toRoute('home');
            return;
        }

        $parametros = new ArrayCollection();
        $parametros->set('descricao', $dadosFiltrados->getValue('descricaoTxt'));
        $imagem = $dadosFiltrados->getValue('imagemFile');
        $parametros->set('imagem', substr($imagem['tmp_name'], 8));
        $parametros->set('resolvido', 'FALSE');
        $parametros->set('dataCriacao', new \DateTime('now'));
        $parametros->set('processo', $processoDAO->lerPorId(
                        $dadosFiltrados->getValue('processoHdn')));
        $authService = $this->getServiceLocator()->get('AuthService');
        $usuarioSession = $authService->getIdentity();
        $usuario = $usuarioDAO->lerPorId($usuarioSession['id']);
        $parametros->set('usuario', $usuario);

        try {
            $pendenciaDAO->salvar($parametros);
            $this->flashMessenger()->addSuccessMessage("Pendecia adicionada com sucesso.");
        } catch (\Exception $e) {
            echo $e->getMessage();die();
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
        }
        $this->redirect()->toRoute('home');
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
                $dadosFiltrados = new SecretariaFilter($this->getServiceLocator(), $nomeTxt, $siglaTxt);
                $secretaria->setNome($dadosFiltrados->getValue('nomeTxt'));
                $secretaria->setSigla($dadosFiltrados->getValue('siglaTxt'));
                try {
                    $objectManager->persist($secretaria);
                    $objectManager->flush();
                    $this->flashMessenger()->addSuccessMessage("Secretaria editada com sucesso.");
                    $this->redirect()->toRoute('secretarias');
                } catch (\Doctrine\DBAL\DBALException $e) {
                    if (strpos($e->getMessage(), 'SQLSTATE[23000]') > 0) {
                        $mensagem = "Já existe uma secretaria cadastrada ";
                        $mensagem.= "com este nome ou sigla.";
                    } else {
                        $mensagem = "Ocorreu um erro na operação, tente novamente ";
                        $mensagem .= "ou entre em contato com um administrador ";
                        $mensagem .= "do sistema.";
                    }
                    $this->redirect()->toRoute('secretarias', array(
                        'action' => 'editar',
                        'id' => $idSecretaria
                    ));
                    $this->flashMessenger()->addErrorMessage($mensagem);
                    return;
                }
            }
        }
    }

    public function resolverAction() {
        $idPendencia = $this->params()->fromRoute('id');
        $idProcesso = $this->params()->fromQuery('processo');
        $parametros = new ArrayCollection();
        $parametros->set('resolvido', true);
        try {
            $pendenciaDAO = new PendenciaDAO($this->getServiceLocator());
            $pendencia = $pendenciaDAO->lerPorId($idPendencia);
            $pendenciaDAO->editar($pendencia->getIdPendencia(), $parametros);
            $this->flashMessenger()->addSuccessMessage("Pendencia resolvida com sucesso.");
            $this->redirect()->toRoute('pendencias', array('id' => $idProcesso));
        } catch (\Exception $e) {
            die();
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('pendencias', array('id' => $idProcesso));
        }
    }

    public function visualizarAction() {
        $idPendencia = (int) $this->params()->fromRoute('id', 0);
        $idProcesso = (int) $this->params()->fromQuery('processo');
        try {
            $pendenciaDAO = new PendenciaDAO($this->getServiceLocator());
            $pendencia = $pendenciaDAO->lerPorId($idPendencia);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
            $mensagem = "Ocorreu um erro na operação, tente novamente ";
            $mensagem .= "ou entre em contato com um administrador ";
            $mensagem .= "do sistema.";
            $this->flashMessenger()->addErrorMessage($mensagem);
            $this->redirect()->toRoute('pendencias', array('id' => $idProcesso));
        }
        if ($pendencia != NULL) {
            return array(
                'pendencia' => $pendencia,
                'processo' => $idProcesso
            );
        } else {
            $this->flashMessenger()->addMessage("Pendencia não encotrada");
            $this->redirect()->toRoute('pendencias', array('id' => $idProcesso));
        }
    }

}
