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

/**
 * Description of SecretariaFilter
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
final class FluxoPostoFilter extends InputFilter {

    private $diasUteisTxt;
    private $descricaoTxt;
    private $serviceManager;

    public function __construct(ServiceManager $sm, $diasUteisTxt, $descricaoTxt) {
        $this->serviceManager = $sm;
        $this->diasUteisTxt = $diasUteisTxt;
        $this->descricaoTxt = $descricaoTxt;
        $this->fluxoPostoInputFilters();
    }

    private final function fluxoPostoInputFilters() {

        $diasUteisFilter = new Input('diasUteisTxt');

        $diasUteisStringLength = new Validator\StringLength(array('max' => 2));
        $diasUteisStringLength->setMessages(array(
            Validator\StringLength::TOO_LONG =>
            'O valor máximo para dias úteis é de %max% caracteres.'
        ));


        $diasUteisFilter->setRequired(FALSE);
        $diasUteisFilter->getValidatorChain()
                ->attach($diasUteisStringLength);
        $diasUteisFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($diasUteisFilter);

        $descricaoFilter = new Input('descricaoTxt');
        
        $descricaoFilter->setRequired(FALSE);
        $descricaoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($descricaoFilter);

        $this->setData(array(
            'diasUteisTxt' => $this->diasUteisTxt,
            'descricaoTxt' => $this->descricaoTxt
        ));
    }

}
