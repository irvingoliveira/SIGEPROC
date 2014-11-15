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
/**
 * Description of GenericDAO
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
abstract class GenericDAO implements DAOInterface {
    private $serviceManager;
    private $objectManager;
    
    protected function __construct(ServiceManager $serviceManager) {
        $this->serviceManager = $serviceManager;
        $this->objectManager = $serviceManager->get('ObjectManager');
    }

    protected function getObjectManager(){
        return $this->objectManager;
    }
    
    public function lerPorId($id) {
        $objetos = $this->objectManager
                        ->getRepository('Application\Entity\\'.$this->getNomeDaClasse());
        $objeto = $objetos->find($id);
        return $objeto;    
    }
    
    public abstract function getNomeDaClasse();
    
    public function lerTodos() {
        $dql = 'SELECT o FROM Application\Entity\\'.$this->getNomeDaClasse().' AS o';
        $query = $this->objectManager->createQuery($dql);
        return $query;
    }
    
    public function salvar(ArrayCollection $params) {
        $reflector = new \ReflectionClass('Application\Entity\\'.$this->getNomeDaClasse());
        $objeto = $reflector->newInstance();
        while(TRUE){
            $objeto->{$params->key()} = $params->current();
            if($params->next() == NULL){
                break;
            }
        }
        $this->objectManager->persist($objeto);
        $this->objectManager->flush();
    }
    
    public function editar($id, ArrayCollection $params) {
        $objetos = $this->objectManager->getRepository('Application\Entity\\'.$this->getNomeDaClasse());
        $objeto = $objetos->find($id);
        while(TRUE){
            $objeto->{$params->key()} = $params->current();
            if($params->next() == NULL){
                break;
            }
        }
        $this->objectManager->persist($objeto);
        $this->objectManager->flush();
    }
    
    public function excluir($id) {
        $objetos = $this->objectManager
                        ->getRepository('Application\Entity\\'.$this->getNomeDaClasse());
        $objeto = $objetos->find($id); 
        $this->objectManager->remove($objeto);
        $this->objectManager->flush();
    }
    
    public function getQtdRegistros(){
        $dql = 'SELECT COUNT(o) ';
        $dql.= 'FROM Application\Entity\\'.$this->getNomeDaClasse().' o';
        return $this->objectManager->createQuery($dql)->getSingleScalarResult();
    }
    
    public function lerRepositorio(){
        return $this->objectManager->getRepository('Application\Entity\\'.$this->getNomeDaClasse())
                ->findAll();
    }
}
