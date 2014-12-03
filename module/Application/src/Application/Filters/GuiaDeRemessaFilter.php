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

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

use Application\DAL\DAOInterface;
/**
 * Description of SecretariaFilter
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
final class GuiaDeRemessaFilter extends InputFilter {

    private $setorSlct;
    private $orgaoExternoSlct;
    
    private $setorDAO;
    private $orgaoExternoDAO;
    
    public function __construct($setorSlct, $orgaoExternoSlct, DAOInterface $setorDAO, 
                                DAOInterface $orgaoExternoDAO) {
        $this->setorSlct = $setorSlct;
        $this->orgaoExternoDAO = $orgaoExternoSlct;
        
        $this->setorDAO = $setorDAO;
        $this->orgaoExternoDAO = $orgaoExternoDAO;
        
        $this->guiaDeRemessaInputFilters();
    }

    private final function guiaDeRemessaInputFilters() {
        if($this->setorSlct != NULL){
            $this->add($this->getSetorSlctInput());
            $data = array('postoSlct' => $this->setorSlct);
        }else{
            $this->add($this->getOrgaoExternoSlctInput());
            $data = array('postoSlct' => $this->orgaoExternoSlct);
        }
        
        $this->setData($data);
    }
    
    private function getSetorSlctInput(){
        $setores = $this->setorDAO->lerTodos()->getResult();
        foreach ($setores as $setor) {
            $idSetor[] = $setor->getIdSetor();
        }

        $setorHayStack = new Validator\InArray(array('haystack' => $idSetor));
        $setorHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um setor válido.'
        ));
        
        $setorNotEmpty = new Validator\NotEmpty();
        $setorNotEmpty->setMessage('Você deve escolher um posto de trabalho.');
        
        $setorFilter = new Input('postoSlct');
        $setorFilter->setRequired(TRUE);
        $setorFilter->getValidatorChain()
                ->attach($setorNotEmpty)
                ->attach($setorHayStack);

        return $setorFilter;
    }
    
    private function getOrgaoExternoSlctInput(){
        $orgaos = $this->orgaoExternoDAO->lerTodos()->getResult();
        foreach ($orgaos as $orgao) {
            $idOrgao[] = $orgao->getIdOrgaos();
        }

        $orgaoHayStack = new Validator\InArray(array('haystack' => $idOrgao));
        $orgaoHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um setor válido.'
        ));
        
        $orgaoNotEmpty = new Validator\NotEmpty();
        $orgaoNotEmpty->setMessage('Você deve escolher um posto de trabalho.');
        
        $orgaoFilter = new Input('postoSlct');
        $orgaoFilter->setRequired(TRUE);
        $orgaoFilter->getValidatorChain()
                ->attach($orgaoHayStack)
                ->attach($orgaoNotEmpty);

        return $orgaoFilter;
    }
}
