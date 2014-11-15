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
 * Description of Secretaria
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 * @ORM\Table(name="Secretaria",uniqueConstraints={
 *     @ORM\UniqueConstraint(name="nome_UNIQUE", columns="nome"),
 *     @ORM\UniqueConstraint(name="sigla_UNIQUE", columns="sigla")
 * }))
 */
class Secretaria {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idSecretaria;
    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     * @var string
     */
    private $sigla;
    /**
     * @ORM\OneToMany(targetEntity="Setor", mappedBy="secretaria")
     * @var ArrayCollection
     */
    private $setores;
    
    public function __construct() {
        $this->setores = new ArrayCollection();
    }
    
    public function getIdSecretaria() {
        return $this->idSecretaria;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getSigla() {
        return $this->sigla;
    }

    public function setIdSecretaria($idSecretaria) {
        $this->idSecretaria = $idSecretaria;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setSigla($sigla) {
        $this->sigla = $sigla;
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

}
