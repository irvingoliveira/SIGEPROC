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
final class SecretariaFilter extends InputFilter {

    private $nomeTxt;
    private $siglaTxt;
    private $objectManager;

    public function __construct(ServiceManager $sm, $nomeTxt, $siglaTxt) {
        $this->objectManager = $sm->get('ObjectManager');
        $this->nomeTxt = $nomeTxt;
        $this->siglaTxt = $siglaTxt;
        $this->secretariaInputFilters();
    }

    private final function secretariaInputFilters() {

        $nomeFilter = new Input('nomeTxt');

        $nomeStringLength = new Validator\StringLength(array('max' => 150, 'min' => 15));
        $nomeStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O nome \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O nome \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));


        $nomeFilter->getValidatorChain()
                ->attach($nomeStringLength)
                ->attach(new Validator\NotEmpty());
        $nomeFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($nomeFilter);

        $siglaFilter = new Input('siglaTxt');

        $siglaStringLength = new Validator\StringLength(array('max' => 10, 'min' => 2));
        $siglaStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'A sigla \'%value%\' é muito curta, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'A sigla \'%value%\' é muito longa, o valor máximo é de %max% caracteres.'
        ));

        $siglaFilter->getValidatorChain()
                ->attach($siglaStringLength)
                ->attach(new Validator\NotEmpty());
        $siglaFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($siglaFilter);

        $this->setData(array(
            'nomeTxt' => $this->nomeTxt,
            'siglaTxt' => $this->siglaTxt
        ));
    }

}
