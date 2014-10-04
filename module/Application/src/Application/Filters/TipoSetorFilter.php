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

use \Doctrine\Common\Persistence\ObjectManager;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Filter;

/**
 * Description of SecretariaFilter
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
final class SetorFilter extends InputFilter {

    private $nomeTxt;
    private $siglaTxt;
    private $secretariaSlct;
    private $tipoSlct;
    private $setorMestreSlct;
    private $objectManager;

    public function __construct(ObjectManager $om, $nomeTxt) {
        $this->objectManager = $om;
        $this->nomeTxt = $nomeTxt;
        $this->setorInputFilters();
    }

    public function TipoSetorInputFilters() {

        $nomeFilter = new Input('nomeTxt');

        $nomeStringLength = new Validator\StringLength(array('max' => 100, 'min' => 3));
        $nomeStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O nome \'%value%\' é muito curto, o valor mínimo é %min%',
            Validator\StringLength::TOO_LONG =>
            'O nome \'%value%\' é muito longo, o valor máximo é %max%'
        ));

        $nomeFilter->getValidatorChain()
                ->attach($nomeStringLength)
                ->attach(new Validator\NotEmpty());
        $nomeFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($nomeFilter);
        $this->setData(array(
            'nomeTxt' => $this->nomeTxt,
        ));
    }

}
