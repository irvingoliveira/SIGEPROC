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
 * Description of TipoSetor
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class TipoSetor {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idTipoSetor;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\OneToMany(targetEntity="Setor", mappedBy="tipoSetor")
     * @var ArrayCollection
     */
    private $setores;
    
    public function __construct() {
        $this->setores = new ArrayCollection();
    }
    
    public function getIdTipoSetor() {
        return $this->idTipoSetor;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setIdTipoSetor($idTipoSetor) {
        $this->idTipoSetor = $idTipoSetor;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function addSetor(Setor $setor){
        if($this->setores->contains($setor)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->setores->set($setor->getIdSetor(), $setor);
    }
    
    public function getSetor($key){
        if(!$this->setores->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->setores->get($key);
    }
    
    public function removeSetor($key){
        if(!$this->setores->containsKey($key)){
            return;
        }
        $this->setores->remove($key);
    }
    
    public function getSetores(){
        return $this->setores->toArray();
    }

    public function __toString() {
        return $this->nome;
    }
}
