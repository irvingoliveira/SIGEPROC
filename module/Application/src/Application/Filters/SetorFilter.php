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
final class SetorFilter extends InputFilter{
    
    private $nomeTxt;
    private $siglaTxt;
    private $secretariaSlct;
    private $tipoSlct;
    private $setorMestreSlct;
    private $objectManager;
    
    public function __construct(ObjectManager $om, $nomeTxt, $siglaTxt, $secretariaSlct, $tipoSlct, $setorMestreSlct) {
        $this->objectManager = $om;
        $this->nomeTxt = $nomeTxt;
        $this->siglaTxt = $siglaTxt;
        $this->secretariaSlct = $secretariaSlct;
        $this->tipoSlct = $tipoSlct;
        $this->setorMestreSlct = $setorMestreSlct;
        $this->setorInputFilters();
    }

    public function setorInputFilters() {
        
        $nomeFilter = new Input('nomeTxt');

        $nomeStringLength = new Validator\StringLength(array('max' => 150, 'min' => 3));
        $nomeStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O nome \'%value%\' é muito curto. O valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O nome \'%value%\' é muito longo. O valor máximo é de %max% caracteres.'
        ));
        $nomeFilter->setRequired(TRUE);
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
            'A sigla \'%value%\' é muito curta. O valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'A sigla \'%value%\' é muito longa. O valor máximo é de %max% caracteres.'
        ));

        $siglaFilter->setRequired(TRUE);
        $siglaFilter->getValidatorChain()
                ->attach($siglaStringLength)
                ->attach(new Validator\NotEmpty());
        $siglaFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($siglaFilter);
        
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
        
        $dql = "SELECT t.idTipoSetor FROM Application\Entity\TipoSetor AS t";
        $query = $this->objectManager->createQuery($dql);
        $tiposSetor = $query->getResult();
        foreach ($tiposSetor as $tipoSetor) {
            $idTipoSetor[] = $tipoSetor['idTipoSetor'];
        }

        $tipoSetorHayStack = new Validator\InArray(array('haystack' => $idTipoSetor));
        $tipoSetorHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um tipo de setor válido.'
        ));

        $tipoSetorFilter = new Input('tipoSlct');
        $tipoSetorFilter->setRequired(TRUE);
        $tipoSetorFilter->getValidatorChain()
                ->attach($tipoSetorHayStack);

        $this->add($tipoSetorFilter);
        
        $dql = "SELECT se.idSetor FROM Application\Entity\Setor AS se";
        $query = $this->objectManager->createQuery($dql);
        $setores = $query->getResult();
        foreach ($setores as $setor) {
            $idSetor[] = $setor['idSetor'];
        }

        $setorFilter = new Input('setorMestreSlct');
        if (($setorMestreSlct != NULL) && (is_int($secretariaSlct))) {
            $setorMestreHaystack = new Validator\InArray(array('haystack' => $idSetor));
            $setorMestreHaystack->setMessages(array(
                Validator\InArray::NOT_IN_ARRAY => '\'%value%\' não é um id de setor válido.'
            ));
            $setorFilter->getValidatorChain()
                    ->attach($setorMestreHaystack);
        }
        $setorFilter->setRequired(FALSE);

        $this->add($setorFilter);

        $this->setData(array(
                    'nomeTxt' => $this->nomeTxt,
                    'siglaTxt' => $this->siglaTxt,
                    'secretariaSlct' => $this->secretariaSlct,
                    'tipoSlct' => $this->tipoSlct,
                    'setorMestreSlct' => $this->setorMestreSlct,
        ));
    }

}
