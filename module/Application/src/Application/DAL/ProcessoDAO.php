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

namespace Application\DAL;

use Zend\ServiceManager\ServiceManager;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Entity\Usuario;

/**
 * Description of AssuntoDAO
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class ProcessoDAO extends GenericDAO {

    public function __construct(ServiceManager $serviceManager) {
        parent::__construct($serviceManager);
    }

    public function getNomeDaClasse() {
        return "Processo";
    }
    
    public function setStatus($idProcesso, $status){
        $objectManager = $this->getObjectManager();
        $statusDAO = new StatusProcessoDAO($this->getServiceManager());
        
        $processo = $this->lerPorId($idProcesso);
        $processo->setStatus($statusDAO->lerPorId($status));
    }

    public function salvar(ArrayCollection $params) {
        $documentoDAO = new DocumentoDAO($this->getServiceManager());
        $requerenteDAO = new RequerenteDAO($this->getServiceManager());
        $statusDAO = new StatusProcessoDAO($this->getServiceManager());
        $telefoneDAO = new TelefoneDAO($this->getServiceManager());

        $dql = "SELECT r FROM Application\Entity\Requerente r ";
        $dql.= "JOIN r.telefone t ";
        $dql.= "JOIN r.documento d ";
        $dql.= "WHERE t.ddd = ?1 ";
        $dql.= "AND t.numero = ?2 ";
        $dql.= "AND d.numero = ?3 ";
        $dql.= "AND d.digito = ?4 ";
        
        $objectManager = $this->getObjectManager();
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1,$params->get('ddd'));
        $query->setParameter(2,$params->get('telefone'));
        $query->setParameter(3,$params->get('numero'));
        $query->setParameter(4,$params->get('digito'));
        $resultado = $query->getResult();
        echo $params->get('anoExercicio');
        if ($resultado[0] instanceof \Application\Entity\Requerente) {
            $requerente = $resultado[0];
        } elseif ($resultado instanceof \Application\Entity\Requerente) {
            $requerente = $resultado;
        }else {

            $documentoParams = new ArrayCollection();
            $documentoParams->set('dataEmissao', $params->get('emissao'));
            $documentoParams->set('digito', $params->get('digito'));
            $documentoParams->set('numero', $params->get('numero'));
            $documentoParams->set('tipo', $params->get('tipoDocumento'));
            $documentoParams->set('orgaoEmissor', $params->get('orgaoEmissor'));
            $documento = $documentoDAO->salvar($documentoParams);

            $telefoneParams = new ArrayCollection();
            $telefoneParams->set('ddd', $params->get('ddd'));
            $telefoneParams->set('numero', $params->get('telefone'));
            $telefone = $telefoneDAO->salvar($telefoneParams);

            $requerenteParams = new ArrayCollection();
            $requerenteParams->set('documento', $documento);
            $requerenteParams->set('telefone', $telefone);
            $requerenteParams->set('nome', $params->get('requerente'));
            if ($params->get('setor') != NULL) {
                $requerenteParams->set('setor', $params->get('setor'));
            }
            try {
                $requerente = $requerenteDAO->salvar($requerenteParams);
            } catch (\Exception $e) {
                $documentoDAO->excluir($documento->getIdDocumento());
                $telefoneDAO->excluir($telefone->getIdTelefone());
                throw $e;
            }
        }
        $params->remove('emissao');
        $params->remove('digito');
        $params->remove('numero');
        $params->remove('tipoDocumento');
        $params->remove('orgaoEmissor');
        $params->remove('ddd');
        $params->remove('telefone');
        $params->remove('requerente');
        $params->remove('setor');
        
        $params->set('requerente', $requerente);
        $params->set('status', $statusDAO->lerPorId(1));

        try {
            parent::salvar($params);
        } catch (\Exception $e) {
            $requerenteDAO->excluir($requerente->getIdRequerente());
            if(isset($documento))
                $documentoDAO->excluir($documento->getIdDocumento());
            if(isset($telefone))
                $telefoneDAO->excluir($telefone->getIdTelefone());
            throw $e;
        }
    }
    
    public function getProcessosInicadosNoSetorDoUsuario($idUsuario){
        $objectManager = $this->getObjectManager();

        $dql = 'SELECT p FROM Application\Entity\Processo p ';
        $dql.= 'JOIN p.usuario u ';
        $dql.= 'JOIN u.setores s ';
        $dql.= 'WHERE u.idUsuario = ?1 ';
        $dql.= 'AND p.status = 1 ';
        $dql.= 'AND s.dataSaida IS NULL ';
        
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1,$idUsuario);
        return $query;
    }
    
    public function getProcessosNoSetor(Usuario $usuario){
        $objectManager = $this->getObjectManager();
        
        $statusDAO = new StatusProcessoDAO($this->getServiceManager());
        $statusEmTransito = $statusDAO->lerPorId(2);
        
        $dql = 'SELECT DISTINCT p ';
        $dql.= 'FROM Application\Entity\Processo p ';
        $dql.= 'JOIN p.postoDeTrabalho pt ';
        $dql.= 'WHERE pt.idPostoDeTrabalho = ?1';
        $dql.= 'AND p.status <> ?2';

        $query = $objectManager->createQuery($dql);
        $query->setParameter(1,$usuario->getSetorAtual()->getSetor());
        $query->setParameter(2,$statusEmTransito);
        
        return $query;
    }
    
    public function getProcessosPorAno(){
        $objectManager = $this->getObjectManager();
        
        $dql = 'SELECT p.anoExercicio, COUNT(p.idProcesso) AS processos ';
        $dql.= 'FROM Application\Entity\Processo p ';
        $dql.= 'WHERE p.anoExercicio > ?1-2 ';
        $dql.= 'AND p.anoExercicio < ?1+2 ';
        $dql.= 'GROUP BY p.anoExercicio ';

        $query = $objectManager->createQuery($dql);
        $dataDoSistema = new \DateTime('NOW');
        $query->setParameter(1,$dataDoSistema->format('Y'));
        return $query->getResult();
    }
}
