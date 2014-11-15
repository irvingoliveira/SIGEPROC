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
final class AssuntoFilter extends InputFilter {

    private $nomeTxt;
    private $secretariaSlct;
    private $setorSlct;
    private $descricaoTxt;
    private $objectManager;

    public function __construct(ServiceManager $sm, $nomeTxt, $secretariaSlct, $setorSlct, $descricaoTxt) {
        $this->objectManager = $sm->get("ObjectManager");
        $this->nomeTxt = $nomeTxt;
        $this->secretariaSlct = $secretariaSlct;
        $this->setorSlct = $setorSlct;
        $this->descricaoTxt = $descricaoTxt;
        $this->assuntoInputFilters();
    }

    private final function assuntoInputFilters() {

        $nomeFilter = new Input('nomeTxt');

        $nomeStringLength = new Validator\StringLength(array('max' => 150, 'min' => 5));
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

        $dql = "SELECT s.idSecretaria FROM Application\Entity\Secretaria AS s";
        $query = $this->objectManager->createQuery($dql);
        $secretarias = $query->getResult();
        foreach ($secretarias as $secretaria) {
            $idSecretaria[] = $secretaria['idSecretaria'];
        }

        $secretariaHayStack = new Validator\InArray(array('haystack' => $idSecretaria));
        $secretariaHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhida uma secretaria válida.'
        ));

        $secretariaFilter = new Input('secretariaSlct');
        $secretariaFilter->setRequired(TRUE);
        $secretariaFilter->getValidatorChain()
                ->attach($secretariaHayStack);
        
        $this->add($secretariaFilter);

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

        $setorFilter = new Input('setorSlct');
        $setorFilter->setRequired(TRUE);
        $setorFilter->getValidatorChain()
                ->attach($setorHayStack);

        $this->add($setorFilter);

        $descricaoFilter = new Input('descricaoTxt');
        
        $descricaoFilter->setRequired(FALSE);
        $descricaoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($descricaoFilter);

        $this->setData(array(
            'nomeTxt' => $this->nomeTxt,
            'secretariaSlct' => $this->secretariaSlct,
            'setorSlct' => $this->setorSlct,
            'descricaoTxt' => $this->descricaoTxt
        ));
    }

}
