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
use Zend\Filter;
use Doctrine\Common\Collections\ArrayCollection;
use Application\DAL\AssuntoDAO;
use Application\DAL\OrgaoExternoDAO;
use Application\DAL\SetorDAO;

/**
 * Description of SecretariaFilter
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
final class WorkflowFilter extends InputFilter {

    private $postoList;
    private $descricaoTxt;
    private $assunto;
    private $serviceManager;

    public function __construct(ServiceManager $sm,array $postoList, $descricaoTxt, $assunto) {
        $this->serviceManager = $sm;
        $this->postoList = $postoList;
        $this->descricaoTxt = $descricaoTxt;
        $this->assunto = $assunto;
        $this->workflowInputFilters();
    }

    private final function workflowInputFilters() {

        $setorDAO = new SetorDAO($this->serviceManager);
        $setores = $setorDAO->lerTodos()->getResult();
        foreach ($setores as $setor) {
            $idSetor[] = $setor->getIdSetor();
        }

        $setorHayStack = new Validator\InArray(array('haystack' => $idSetor));
        $setorHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um setor válido.'
        ));

        $orgaoExternoDAO = new OrgaoExternoDAO($this->serviceManager);
        $orgaosExternos = $orgaoExternoDAO->lerTodos()->getResult();
        foreach ($orgaosExternos as $orgao) {
            $idOrgaoExterno[] = $orgao->getIdOrgaoExterno();
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
        
        $descricaoFilter = new Input('descricaoTxt');
        
        $descricaoFilter->setRequired(FALSE);
        $descricaoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($descricaoFilter);
        
        $assuntoDAO = new AssuntoDAO($this->serviceManager);
        $assuntos = $assuntoDAO->lerRepositorio();
        foreach ($assuntos as $assunto) {
            $idAssunto[] = $assunto->getidAssunto();
        }

        $assuntoHayStack = new Validator\InArray(array('haystack' => $idAssunto));
        $assuntoHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um assunto válido.'
        ));
        
        $assuntoFilter = new Input('assuntoTxt');
        $assuntoFilter->setRequired(TRUE);
        $assuntoFilter->getValidatorChain()
                            ->attach($assuntoHayStack);
        
        $this->add($assuntoFilter);
        
        $data = new ArrayCollection();
        
        $data->set('assuntoTxt', $this->assunto);
        $data->set('descricaoTxt', $this->descricaoTxt);
        
        foreach ($this->postoList as $key => $posto){
            $data->set('posto'.$key, substr($posto, 1));
        }     
      
        $this->setData($data->toArray());

    }

}
