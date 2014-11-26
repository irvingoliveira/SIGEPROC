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
use Application\DAL\DAOInterface;
/**
 * Description of SecretariaFilter
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
final class ProcessoFilter extends InputFilter {

    private $assuntoTxt;
    private $volumeTxt;
    private $requerenteTxt;
    private $dddTxt;
    private $telefoneTxt;
    private $secretariaSlct;
    private $setorSlct;
    private $tipoDocumentoSlct;
    private $numeroTxt;
    private $digitoTxt;
    private $emissaoDt;
    private $orgaoEmissorTxt;
    
    private $assuntoDAO;
    private $secretariaDAO;
    private $setorDAO;
    private $tipoDocumentoDAO;

    public function __construct($assuntoTxt, $volumeTxt, $requerenteTxt,
            $dddTxt, $telefoneTxt, $secretariaSlct, $setorSlct, $tipoDocumentoSlct,
            $numeroTxt, $digitoTxt, $emissaoDt, $orgaoEmissorTxt,  DAOInterface $assuntoDAO, 
            DAOInterface $secretariaDAO, DAOInterface $setorDAO, DAOInterface $tipoDocumentoDAO) {
        $this->assuntoTxt = $assuntoTxt;
        $this->volumeTxt = $volumeTxt;
        $this->requerenteTxt = $requerenteTxt;
        $this->dddTxt = $dddTxt;
        $this->telefoneTxt = $telefoneTxt;
        $this->secretariaSlct = $secretariaSlct;
        $this->setorSlct = $setorSlct;
        $this->tipoDocumentoSlct = $tipoDocumentoSlct;
        $this->numeroTxt = $numeroTxt;
        $this->digitoTxt = $digitoTxt;
        $this->emissaoDt = $emissaoDt;
        $this->orgaoEmissorTxt = $orgaoEmissorTxt;
        
        $this->assuntoDAO = $assuntoDAO;
        $this->secretariaDAO = $secretariaDAO;
        $this->setorDAO = $setorDAO;
        $this->tipoDocumentoDAO = $tipoDocumentoDAO;
        
        $this->processoInputFilters();
    }

    private final function processoInputFilters() {

        $this->add($this->getAssuntoTxtInput());
        $this->add($this->getVolumeTxtInput());
        $this->add($this->getRequerenteTxtInput());
        $this->add($this->getDddTxtInput());
        $this->add($this->getTelefoneTxtInput());
        $this->add($this->getSecretariaSlctInput());
        $this->add($this->getSetorSlctInput());
        $this->add($this->getTipoDocumentoSlctInput());
        $this->add($this->getNumeroTxtInput());
        $this->add($this->getDigitoTxtInput());
        $this->add($this->getEmissaoDtInput());
        $this->add($this->getOrgaoEmissorTxtInput());

        $this->setData(array(
            'assuntoTxt' => $this->assuntoTxt,
            'volumeTxt' => $this->volumeTxt,
            'requerenteTxt' => $this->requerenteTxt,
            'dddTxt' => $this->dddTxt,
            'telefoneTxt' => $this->telefoneTxt,
            'secretariaSlct' => $this->secretariaSlct,
            'setorSlct' => $this->setorSlct,
            'tipoDocumentoSlct' => $this->tipoDocumentoSlct,
            'numeroTxt' => $this->numeroTxt,
            'digitoTxt' => $this->digitoTxt,
            'emissaoDt' => $this->emissaoDt,
            'orgaoEmissorTxt' => $this->orgaoEmissorTxt,
        ));
    }
    
    private function getAssuntoTxtInput(){
        $assuntoFilter = new Input('assuntoTxt');

        $assuntoStringLength = new Validator\StringLength(array('max' => 150, 'min' => 3));
        $assuntoStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O assunto \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O assunto \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));

        $assuntoDbExists = new \DoctrineModule\Validator\ObjectExists(array(
            'object_repository' => $this->assuntoDAO->getRepositorio(),
            'fields' => array('nome')
        ));

        $assuntoNotEmpty = new Validator\NotEmpty();
        $assuntoNotEmpty->setMessage('O campo assunto é obrigatório e não pode ser vazio.');
        
        $assuntoFilter->getValidatorChain()
                ->attach($assuntoStringLength)
                ->attach($assuntoDbExists)
                ->attach($assuntoNotEmpty);
        $assuntoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());
        return $assuntoFilter;
    }
    
    private function getVolumeTxtInput(){
        $volumeFilter = new Input('volumeTxt');

        $volumeStringLength = new Validator\StringLength(array('max' => 2, 'min' => 1));
        $volumeStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O volume \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O volume \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));
        
        $volumeNotEmpty = new Validator\NotEmpty();
        $volumeNotEmpty->setMessage('O campo volume é obrigatório e não pode ser vazio.');

        $volumeFilter->getValidatorChain()
                ->attach($volumeStringLength)
                ->attach($volumeNotEmpty);
        $volumeFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $volumeFilter;
    }
    
    private function getRequerenteTxtInput(){
        $requerenteFilter = new Input('requerenteTxt');

        $requerenteStringLength = new Validator\StringLength(array('max' => 80, 'min' => 3));
        $requerenteStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O requerente \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O requerente \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));

        $requerenteNotEmpty = new Validator\NotEmpty();
        $requerenteNotEmpty->setMessage('O campo requerente é obrigatório e não ser vazio.');
        
        $requerenteFilter->getValidatorChain()
                ->attach($requerenteStringLength)
                ->attach($requerenteNotEmpty);
        $requerenteFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $requerenteFilter;
    }
    
    private function getDddTxtInput(){
        $dddFilter = new Input('dddTxt');

        $dddStringLength = new Validator\StringLength(array('max' => 2, 'min' => 2));
        $dddStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O ddd \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O ddd \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));

        $dddNotEmpty = new Validator\NotEmpty();
        $dddNotEmpty->setMessage('O campo ddd é obrigatório e não pode ser vazio.');

        $dddFilter->getValidatorChain()
                ->attach($dddStringLength)
                ->attach($dddNotEmpty);
        $dddFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $dddFilter;
    }
    
    private function getTelefoneTxtInput(){
        $telefoneFilter = new Input('telefoneTxt');

        $telefoneStringLength = new Validator\StringLength(array('max' => 9, 'min' => 8));
        $telefoneStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O número de telefone \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O número de telefone \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));

        $telefoneNotEmpty = new Validator\NotEmpty();
        $telefoneNotEmpty->setMessage('O campo telefone é obrigatório e não pode ser vazio.');

        $telefoneFilter->getValidatorChain()
                ->attach($telefoneStringLength)
                ->attach($telefoneNotEmpty);
        $telefoneFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $telefoneFilter;
    }
    
    private function getSecretariaSlctInput(){
        $secretarias = $this->secretariaDAO->lerTodos()->getResult();
        foreach ($secretarias as $secretaria) {
            $idSecretaria[] = $secretaria->getIdSecretaria();
        }

        $secretariaHayStack = new Validator\InArray(array('haystack' => $idSecretaria));
        $secretariaHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhida uma secretaria válida.'
        ));

        $secretariaFilter = new Input('secretariaSlct');
        $secretariaFilter->setRequired(FALSE);
        $secretariaFilter->getValidatorChain()
                ->attach($secretariaHayStack);
        
        return $secretariaFilter;
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
        
        $setorFilter = new Input('setorSlct');
        $setorFilter->setRequired(FALSE);
        $setorFilter->getValidatorChain()
                ->attach($setorHayStack);

        return $setorFilter;
    }
    
    private function getTipoDocumentoSlctInput(){
        $tiposdocumento = $this->tipoDocumentoDAO->lerTodos()->getResult();
        foreach ($tiposdocumento as $tipo) {
            $idTipo[] = $tipo->getIdTipoDocumento();
        }

        $tipoDocumentoHayStack = new Validator\InArray(array('haystack' => $idTipo));
        $tipoDocumentoHayStack->setMessages(array(
            Validator\InArray::NOT_IN_ARRAY => 'Não foi escolhido um tipo de documento válido.'
        ));
        
        $tipoDocumentoNotEmpty = new Validator\NotEmpty();
        $tipoDocumentoNotEmpty->setMessage('O campo \'Tipo do documento de identi'
                . 'ficação do requerente\' é obrgtório e não pode ser vazio.');

        $tipoDocumentoFilter = new Input('tipoDocumentoSlct');
        $tipoDocumentoFilter->setRequired(TRUE);
        $tipoDocumentoFilter->getValidatorChain()
                ->attach($tipoDocumentoNotEmpty)
                ->attach($tipoDocumentoHayStack);

        return $tipoDocumentoFilter;
    }
    
    private function getNumeroTxtInput(){
        $numeroFilter = new Input('numeroTxt');

        $numeroStringLength = new Validator\StringLength(array('max' => 12, 'min' => 6));
        $numeroStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O número de documento \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O número de documento \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));

        $numeroNotEmpty = new Validator\NotEmpty();
        $numeroNotEmpty->setMessage('O campo \'Telefone do requerente\' é obrgtório e não pode ser vazio.');

        $numeroFilter->getValidatorChain()
                ->attach($numeroStringLength)
                ->attach(new Validator\NotEmpty());
        $numeroFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $numeroFilter;
    }
    
    private function getDigitoTxtInput(){
        $digitoFilter = new Input('digitoTxt');

        $digitoStringLength = new Validator\StringLength(array('max' => 2));
        $digitoStringLength->setMessages(array(
            Validator\StringLength::TOO_LONG =>
            'O dígito verificador do documento \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));

        $digitoFilter->setRequired(FALSE);
        $digitoFilter->getValidatorChain()
                ->attach($digitoStringLength);
        $digitoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $digitoFilter;
    }
    
    private function getEmissaoDtInput(){
        $emissaoFilter = new Input('emissaoDt');

        $emissaoStringLength = new Validator\StringLength(array('max' => 10, 'min' => 10));
        $emissaoStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'A data de emissão \'%value%\' é muito curta, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'A data de emissão \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));
        
        $emissaoData = new Validator\Date();
        $emissaoData->setMessage(array(
            Validator\Date::INVALID_DATE =>
            'A data \'%value%\' é invalida.'
        ));

        $emissaoFilter->setRequired(FALSE);
        $emissaoFilter->getValidatorChain()
                ->attach($emissaoStringLength)
                ->attach($emissaoData);
        $emissaoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $emissaoFilter;
    }
    
    private function getOrgaoEmissorTxtInput(){
        $orgaoEmissorFilter = new Input('orgaoEmissorTxt');

        $orgaoEmissorStringLength = new Validator\StringLength(array('max' => 150, 'min' => 3));
        $orgaoEmissorStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'O nome do orgão emissor \'%value%\' é muito curto, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'O nome do orgão emissor \'%value%\' é muito longo, o valor máximo é de %max% caracteres.'
        ));

        $orgaoEmissorFilter->setRequired(FALSE);
        $orgaoEmissorFilter->getValidatorChain()
                ->attach($orgaoEmissorStringLength);
        $orgaoEmissorFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());

        return $orgaoEmissorFilter;
    }
}
