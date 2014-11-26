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
use Zend\Filter;

use Doctrine\Common\Collections\ArrayCollection;

use Application\DAL\DAOInterface;

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
    private $arquivoRd;
    private $secretariaDAO;
    private $tipoSetorDAO;
    private $setorDAO;

    public function __construct($nomeTxt, $siglaTxt, $secretariaSlct, $tipoSlct, 
            $arquivoRd, $setorMestreSlct, DAOInterface $secretariaDAO, DAOInterface $tipoSetorDAO, 
            DAOInterface $setorDAO) {
        var_dump($setorMestreSlct);die();
        $this->nomeTxt = $nomeTxt;
        $this->siglaTxt = $siglaTxt;
        $this->secretariaSlct = $secretariaSlct;
        $this->tipoSlct = $tipoSlct;
        $this->setorMestreSlct = $setorMestreSlct;
        $this->arquivoRd = $arquivoRd;
        $this->secretariaDAO = $secretariaDAO;
        $this->tipoSetorDAO = $tipoSetorDAO;
        $this->setorDAO = $setorDAO;
        $this->setorInputFilters();
    }

    private final function setorInputFilters() {

        $this->add($this->getNomeTxtInput());
        $this->add($this->getSiglaTxtInput());
        $this->add($this->getSecretariaSlctInput());
        $this->add($this->getTipoSetorSlctInput());
        if($this->setorDAO->getQtdRegistros()>0)
            $this->add($this->getSetorSlctInput());
        $this->add($this->getArquivoRdInput());
        
        $data = new ArrayCollection();
        $data->set('nomeTxt',$this->nomeTxt);
        $data->set('siglaTxt',$this->siglaTxt);
        $data->set('secretariaSlct',$this->secretariaSlct);
        $data->set('tipoSlct',$this->tipoSlct);
        if($this->setorDAO->getQtdRegistros()>0)
            $data->set('setorMestreSlct',$this->setorMestreSlct);
        $data->set('arquivoRd',$this->arquivoRd);
        
        $this->setData($data->toArray());
    }

    private function getNomeTxtInput() {
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
        return $nomeFilter;
    }

    public function getSiglaTxtInput() {
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
        return $siglaFilter;
    }

    public function getSecretariaSlctInput() {
        $secretarias = $this->secretariaDAO->lerTodos()->getResult();
        foreach ($secretarias as $secretaria) {
            $idSecretaria[] = $secretaria->getIdSecretaria();
        }

        $secretariaHayStack = new Validator\InArray(array('haystack' => $idSecretaria));
        $secretariaHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhida uma secretaria válida.'
        ));

        $secretariaFilter = new Input('secretariaSlct');
        $secretariaFilter->setRequired(TRUE);
        $secretariaFilter->getValidatorChain()
                ->attach($secretariaHayStack);
        return $secretariaFilter;
    }

    public function getTipoSetorSlctInput() {
        $tiposSetor = $this->tipoSetorDAO->lerTodos()->getResult();
        foreach ($tiposSetor as $tipoSetor) {
            $idTipoSetor[] = $tipoSetor->getIdTipoSetor();
        }

        $tipoSetorHayStack = new Validator\InArray(array('haystack' => $idTipoSetor));
        $tipoSetorHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um tipo de setor válido.'
        ));

        $tipoSetorFilter = new Input('tipoSlct');
        $tipoSetorFilter->setRequired(TRUE);
        $tipoSetorFilter->getValidatorChain()
                ->attach($tipoSetorHayStack);
        return $tipoSetorFilter;
    }

    public function getSetorSlctInput() {
        $setores = $this->setorDAO->lerTodos()->getResult();
        foreach ($setores as $setor) {
            $idSetor[] = $setor->getIdSetor();
        }
        $setorFilter = new Input('setorMestreSlct');
        $setorMestreHaystack = new Validator\InArray(array('haystack' => $idSetor));
        $setorMestreHaystack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => '\'%value%\' não é um id de setor válido.'
        ));
        $setorFilter->getValidatorChain()
                ->attach($setorMestreHaystack);
        $setorFilter->setRequired(FALSE);

        return $setorFilter;
    }

    public function getArquivoRdInput() {
        $arquivoFilter = new Input('arquivoRd');
        $arquivoMestreHaystack = new Validator\InArray(array('haystack' => array(0,1)));
        $arquivoMestreHaystack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => '\'%value%\' não é um id de setor válido.'
        ));
        $arquivoFilter->getValidatorChain()
                ->attach($arquivoMestreHaystack);
        $arquivoFilter->setRequired(FALSE);

        return $arquivoFilter;
    }

}
