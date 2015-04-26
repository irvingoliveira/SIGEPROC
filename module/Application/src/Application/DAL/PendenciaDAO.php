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

use Application\Entity\Processo;

use Zend\ServiceManager\ServiceManager;
/**
 * Description of PendenciaDAO
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
class PendenciaDAO extends GenericDAO{
    
    public function __construct(ServiceManager $serviceManager) {
        parent::__construct($serviceManager);
    }

    public function getNomeDaClasse() {
        return "Pendencia";
    }
    
    public function getPendenciasPorProcesso(Processo $processo){
        $dql = 'SELECT p FROM Application\Entity\Pendencia AS p ';
        $dql.= 'WHERE p.processo = ?1';
        $objectManager = $this->getObjectManager();
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1, $processo);
        return $query;
    }    
    
    public function getQtdPendenciasPorProcesso(Processo $processo){
        $dql = 'SELECT COUNT(p) FROM Application\Entity\Pendencia AS p ';
        $dql.= 'WHERE p.processo = ?1';
        $objectManager = $this->getObjectManager();
        $query = $objectManager->createQuery($dql);
        $query->setParameter(1, $processo);
        return $query->getSingleScalarResult();
    }    
}
