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
 * Description of Estado
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Estado {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idEstado;
    /**
     * @ORM\Column(type="string", length=80, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     * @var string
     */
    private $abreviacao;
    /**
     * @ORM\OneToMany(targetEntity="Cidade", mappedBy="estado")
     * @var ArrayCollection
     */
    private $cidades;
    
    public function __construct() {
        $this->cidades = new ArrayCollection();
    }
    
    public function getIdEstado() {
        return $this->idEstado;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getAbreviacao() {
        return $this->abreviacao;
    }

    public function setIdEstado($idEstado) {
        $this->idEstado = $idEstado;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setAbreviacao($abreviacao) {
        $this->abreviacao = $abreviacao;
    }

    public function addCidade(Cidade $cidade){
        if($this->cidades->contains($cidade)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->cidades->set($cidade->getIdCidade(), $cidade);
    }
    
    public function getCidade($key){
        if(!$this->cidades->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->cidades->get($key);
    }
    
    public function removeCidade($key){
        if(!$this->cidades->containsKey($key)){
            return;
        }
        $this->cidades->remove($key);
    }
    
    public function getCidades(){
        return $this->cidades->toArray();
    }

}
