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
use Application\Entity\Usuario;
/**
 * Description of AssuntoDAO
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class GuiaDeRemessaDAO extends GenericDAO{
    
    public function __construct(ServiceManager $serviceManager) {
        parent::__construct($serviceManager);
    }

    public function getNomeDaClasse() {
        return "GuiaDeRemessa";
    }
    
    public function getGuiasEnviadasNaoRecebidas(Usuario $usuario){
        $objectManager = $this->getObjectManager();
        
        $dql = 'SELECT g ';
        $dql.= 'FROM Application\Entity\GuiaDeRemessa g ';
        $dql.= 'WHERE g.dataRecebimento IS NULL ';
        $dql.= 'AND g.emissor = ?1 ';
        
        
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1,$usuario);
        
        return $query;
    }
    
    public function getGuiasRejeitadasDoSetor(Usuario $usuario){
        $objectManager = $this->getObjectManager();
        
        $dql = 'SELECT g ';
        $dql.= 'FROM Application\Entity\GuiaDeRemessa g ';
        $dql.= 'JOIN g.emissor u ';
        $dql.= 'JOIN u.setores us ';
        $dql.= 'WHERE g.rejeitada = TRUE ';
        $dql.= 'AND us.setor = ?1 ';
        
        
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1,$usuario->getSetorAtual()->getSetor());
        
        return $query;
    }
    
    public function getGuiasEnviadasDoSetor(Usuario $usuario){
        $objectManager = $this->getObjectManager();
        
        $dql = 'SELECT g ';
        $dql.= 'FROM Application\Entity\GuiaDeRemessa g ';
        $dql.= 'JOIN g.emissor u ';
        $dql.= 'JOIN u.setores us ';
        $dql.= 'WHERE g.rejeitada = FALSE ';
        $dql.= 'AND us.setor = ?1 ';
        
        
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1,$usuario->getSetorAtual()->getSetor());
        
        return $query;
    }
    
    public function getGuiasAReceber(Usuario $usuario){
        $objectManager = $this->getObjectManager();
        
        $dql = 'SELECT g ';
        $dql.= 'FROM Application\Entity\GuiaDeRemessa g ';
        $dql.= 'WHERE g.dataRecebimento IS NULL ';
        $dql.= 'AND g.postoDeTrabalho = ?1 ';
        
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1,$usuario->getSetorAtual()->getSetor());
        
        return $query;
    }
}
