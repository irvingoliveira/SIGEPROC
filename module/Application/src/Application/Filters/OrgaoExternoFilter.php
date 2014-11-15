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
final class OrgaoExternoFilter extends InputFilter{
    
    private $nomeTxt;
    private $abreviacaoTxt;
    private $logradouroTxt;
    private $numeroTxt;
    private $complementoTxt;
    private $bairroTxt;
    private $cidadeSlct;
    private $objectManager;
    
    public function __construct(ObjectManager $om, $nomeTxt, $abreviacaoTxt, 
                                $logradouroTxt, $numeroTxt, $complementoTxt,
                                $bairroTxt, $cidadeSlct) {
        
        $this->objectManager = $om;
        $this->nomeTxt = $nomeTxt;
        $this->abreviacaoTxt = $abreviacaoTxt;
        $this->logradouroTxt = $logradouroTxt;
        $this->numeroTxt = $numeroTxt;
        $this->complementoTxt = $complementoTxt;
        $this->bairroTxt = $bairroTxt;
        $this->cidadeSlct = $cidadeSlct;
        $this->setorInputFilters();
    }

    private final function setorInputFilters() {
        
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
        
        $abreviacaoFilter = new Input('abreviacaoTxt');
        $abreviacaoStringLength = new Validator\StringLength(array('max' => 10, 'min' => 2));
        $abreviacaoStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'A abreviacao \'%value%\' é muito curta. O valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'A abreviacao \'%value%\' é muito longa. O valor máximo é de %max% caracteres.'
        ));

        $abreviacaoFilter->setRequired(TRUE);
        $abreviacaoFilter->getValidatorChain()
                ->attach($abreviacaoStringLength)
                ->attach(new Validator\NotEmpty());
        $abreviacaoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($abreviacaoFilter);
                
        $logradouroFilter = new Input('logradouroTxt');
        $logradouroStringLength = new Validator\StringLength(array('max' => 200, 'min' => 4));
        $logradouroStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O nome do logradouro \'%value%\' é muito curto. O valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O nome do logradouro \'%value%\' é muito longo. O valor máximo é de %max% caracteres.'
        ));

        $logradouroFilter->setRequired(TRUE);
        $logradouroFilter->getValidatorChain()
                ->attach($logradouroStringLength)
                ->attach(new Validator\NotEmpty());
        $logradouroFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($logradouroFilter);
                
        $numeroFilter = new Input('numeroTxt');
        $numeroStringLength = new Validator\StringLength(array('max' => 5));
        $numeroStringLength->setMessages(array(
            Validator\StringLength::TOO_LONG =>
            'O número \'%value%\' é muito longo. O valor máximo é de %max% caracteres.'
        ));

        $numeroFilter->setRequired(FALSE);
        $numeroFilter->getValidatorChain()
                ->attach($numeroStringLength);
        $numeroFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($numeroFilter);
        
        $complementoFilter = new Input('complementoTxt');
        $complementoStringLength = new Validator\StringLength(array('max' => 150));
        $complementoStringLength->setMessages(array(
            Validator\StringLength::TOO_LONG =>
            'O complemento \'%value%\' é muito longo. O valor máximo é de %max% caracteres.'
        ));

        $complementoFilter->setRequired(FALSE);
        $complementoFilter->getValidatorChain()
                ->attach($complementoStringLength);
        $complementoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($complementoFilter);
        
        $bairroFilter = new Input('bairroTxt');
        $bairroStringLength = new Validator\StringLength(array('max' => 100, 'min' => 2));
        $bairroStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O nome do bairro \'%value%\' é muito curto. O valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O nome bairro \'%value%\' é muito longo. O valor máximo é de %max% caracteres.'
        ));

        $bairroFilter->setRequired(TRUE);
        $bairroFilter->getValidatorChain()
                ->attach($bairroStringLength)
                ->attach(new Validator\NotEmpty());
        $bairroFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        $this->add($bairroFilter);
        
        $dql = "SELECT c.idCidade FROM Application\Entity\Cidade AS c";
        $query = $this->objectManager->createQuery($dql);
        $cidades = $query->getResult();
        foreach ($cidades as $cidade) {
            $idCidade[] = $cidade['idCidade'];
        }

        $cidadeHayStack = new Validator\InArray(array('haystack' => $idCidade));
        $cidadeHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhida uma cidade válida.'
        ));

        $cidadeFilter = new Input('cidadeSlct');
        $cidadeFilter->setRequired(TRUE);
        $cidadeFilter->getValidatorChain()
                ->attach($cidadeHayStack);
        
        $this->add($cidadeFilter);

        $this->setData(array(
                    'nomeTxt' => $this->nomeTxt,
                    'abreviacaoTxt' => $this->abreviacaoTxt,
                    'logradouroTxt' => $this->logradouroTxt,
                    'numeroTxt' => $this->numeroTxt,
                    'complementoTxt' => $this->complementoTxt,
                    'bairroTxt' => $this->bairroTxt,
                    'cidadeSlct' => $this->cidadeSlct
        ));
    }

}
