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

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Exception\NullPointerException;
use Application\Exception\ObjectAlreadyExistsOnCollectionException;
/**
 * Description of TipoDocumento
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class TipoDocumento {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idTipoDocumento;
    /**
     * @ORM\Column(type="string", length=80, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\OneToMany(targetEntity="Documento", mappedBy="tipo", cascade={"persist"})
     * @var ArrayCollection
     */
    private $documentos;
    
    public function __construct() {
        $this->documentos = new ArrayCollection();
    }
    
    public function getIdTipoDocumento() {
        return $this->idTipoDocumento;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setIdTipoDocumento($idTipoDocumento) {
        $this->idTipoDocumento = $idTipoDocumento;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function addDocumento(Documento $documento){
        if($this->documentos->contains($documento)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->documentos->set($documento->getIdDocumento(), $documento);
    }
    
    public function getDocumento($key){
        if(!$this->documentos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->documentos->get($key);
    }
    
    public function removeDocumento($key){
        if(!$this->documentos->containsKey($key)){
            return;
        }
        $this->documentos->remove($key);
    }
    
    public function getDocumentos(){
        $this->documentos->toArray();
    }
    
    public function __toString() {
        return $this->nome;
    }
}
