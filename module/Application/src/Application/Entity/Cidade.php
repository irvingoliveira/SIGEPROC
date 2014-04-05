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
 * Description of Cidade
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Cidade {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idCidade;
    /**
     * @ORM\Column(type="string", length=150, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\ManyToOne(targetEntity="Estado", inversedBy="cidades")
     * @ORM\JoinColumn(name="Estado_idEstado",
     *                 referencedColumnName="idEstado", nullable=false)
     * @var Estado
     */
    private $estado;
    /**
     * @ORM\OneToMany(targetEntity="Endereco", mappedBy="cidade")
     * @var ArrayCollection
     */
    private $enderecos;
    
    public function __construct() {
        $this->enderecos = new ArrayCollection();
    }
    
    public function getIdCidade() {
        return $this->idCidade;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setIdCidade($idCidade) {
        $this->idCidade = $idCidade;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setEstado(Estado $estado) {
        $this->estado = $estado;
    }

    public function addEndereco(Endereco $endereco){
        if($this->enderecos->contains($endereco)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->enderecos->set($endereco->getIdEndereco(), $endereco);
    }
    
    public function getEndereco($key){
        if(!$this->enderecos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->enderecos->get($key);
    }
    
    public function removeEndereco($key){
        if(!$this->enderecos->containsKey($key)){
            return;
        }
        $this->enderecos->remove($key);
    }
    
    public function getEnderecos(){
        return $this->enderecos->toArray();
    }
}
