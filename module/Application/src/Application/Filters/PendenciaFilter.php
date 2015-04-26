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
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Filter;
use Application\DAL\DAOInterface;
/**
 * Description of PendenciaFilter
 *
 * @author Irving Fernando de Medeiros Oliveira
 */
final class PendenciaFilter extends InputFilter {

    private $descricaoTxt;
    private $imagemFile;
    private $processoHdn;
    
    private $processoDAO;

    public function __construct($descricaoTxt, $imagemFile, $processoHdn,
            DAOInterface $processoDAO) {
        $this->descricaoTxt = $descricaoTxt;
        $this->imagemFile = $imagemFile;
        $this->processoHdn = (int)$processoHdn;
        
        $this->processoDAO = $processoDAO;
        
        $this->pendenciaInputFilters();
    }

    private final function pendenciaInputFilters() {

        $this->add($this->getDescricaoTxtInput());
        $this->add($this->getImagemFileInput());
        $this->add($this->getProcessoHdnInput());

        $this->setData(array(
            'descricaoTxt' => $this->descricaoTxt,
            'imagemFile' => $this->imagemFile,
            'processoHdn' => $this->processoHdn,
        ));
    }
    
    private function getDescricaoTxtInput(){
        $descricaoFilter = new Input('descricaoTxt');

        $descricaoStringLength = new Validator\StringLength(array('max' => 255, 'min' => 3));
        $descricaoStringLength->setMessages(array(
            Validator\StringLength::TOO_SHORT =>
            'A desrição é muito curta, o valor mínimo é de %min% caracteres.',
            Validator\StringLength::TOO_LONG =>
            'A desrição é muito longa, o valor máximo é de %max% caracteres.'
        ));

        $descricaoNotEmpty = new Validator\NotEmpty();
        $descricaoNotEmpty->setMessage('O campo descrição é obrigatório e não pode ser vazio.');
        
        $descricaoFilter->getValidatorChain()
                ->attach($descricaoStringLength)
                ->attach($descricaoNotEmpty);
        $descricaoFilter->getFilterChain()
                ->attach(new Filter\HtmlEntities())
                ->attach(new Filter\StringTrim())
                ->attach(new Filter\StripTags());
        return $descricaoFilter;
    }
    
    private function getImagemFileInput(){
        $imagemFilter = new FileInput('imagemFile');

        $imagemExtension = new \Zend\Validator\File\Extension(array(
            'jpg','jpeg','tiff','pdf'
        ));
        
        $imagemFilter->getValidatorChain()
                ->attach($imagemExtension)
                ->attach(new Validator\File\UploadFile())
                ->attach(new Validator\File\Size(array(
                    'max' => substr(ini_get('upload_max_filesize'), 0, 1).'MB'
                )));
        $imagemFilter->getFilterChain()
                ->attach(new Filter\File\RenameUpload(array(
                    'target'    => './public/img/documentos/',
                    'overwrite' => true,
                    'randomize' => true,
                    'use_upload_extension' => true,
                )));
        return $imagemFilter;
    }
    
    private function getProcessoHdnInput(){
        $processoFilter = new Input('processoHdn');
        
        $processoDbExists = new \DoctrineModule\Validator\ObjectExists(array(
            'object_repository' => $this->processoDAO->getRepositorio(),
            'fields' => array('idProcesso')
        ));        

        $processoNotEmpty = new Validator\NotEmpty();
        $processoNotEmpty->setMessage('O campo requerente é obrigatório e não ser vazio.');
        
        $processoFilter->getValidatorChain()
                ->attach($processoDbExists)
                ->attach($processoNotEmpty);

        return $processoFilter;
    }
}
