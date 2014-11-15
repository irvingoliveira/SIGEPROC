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

namespace Application\Filters;

use Zend\ServiceManager\ServiceManager;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of SecretariaFilter
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
final class WorkflowFilter extends InputFilter {

    private $postoList;
    private $objectManager;

    public function __construct(ServiceManager $sm,array $postoList) {
        $this->objectManager = $sm->get("ObjectManager");
        $this->postoList = $postoList;
        $this->assuntoInputFilters();
    }

    private final function assuntoInputFilters() {

        $dql = "SELECT s.idSetor FROM Application\Entity\Setor AS s";
        $query = $this->objectManager->createQuery($dql);
        $setores = $query->getResult();
        foreach ($setores as $setor) {
            $idSetor[] = $setor['idSetor'];
        }

        $setorHayStack = new Validator\InArray(array('haystack' => $idSetor));
        $setorHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um setor válido.'
        ));

        $dql = "SELECT o.idOrgaoExterno FROM Application\Entity\OrgaoExterno AS o";
        $query = $this->objectManager->createQuery($dql);
        $orgaosExternos = $query->getResult();
        foreach ($orgaosExternos as $orgao) {
            $idOrgaoExterno[] = $orgao['idOrgaoExterno'];
        }

        $orgaoHayStack = new Validator\InArray(array('haystack' => $idOrgaoExterno));
        $orgaoHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um orgão externo válido.'
        ));
        
        foreach ($this->postoList as $key => $posto){
            
            $postoFilter = new Input('posto'.$key);
            $postoFilter->setRequired(TRUE);
            if(substr($posto, 0,1) == "S"){
                $postoFilter->getValidatorChain()
                            ->attach($setorHayStack);
            }else{
                $postoFilter->getValidatorChain()
                            ->attach($orgaoHayStack);
            }
            $this->add($postoFilter);
        }
        
        $data = new ArrayCollection();
        
        foreach ($this->postoList as $key => $posto){
            $data->set('posto'.$key, substr($posto, 1));
        }
        
         $this->setData($data->toArray());

    }

}
