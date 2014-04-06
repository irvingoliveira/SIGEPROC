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
 * Description of Funcao
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Funcao {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idFuncao;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="funcao")
     * @var ArrayCollection
     */
    private $usuarios;
    /**
     * @ORM\ManyToMany(targetEntity="Permissao", inversedBy="funcoes")
     * @ORM\JoinTable(name="Funcao_has_Permissao",
     *                joinColumns={@ORM\JoinColumn(name="Funcao_idFuncao",
     *                                             referencedColumnName="idFuncao",
     *                                             nullable=false)},
     *                inverseJoinColumns={@ORM\JoinColumn(name="Permissao_idPermissao",
     *                                                    referencedColumnName="idPermissao",
     *                                                    nullable=false)})
     * @var ArrayCollection
     */
    private $permissoes;
    
    public function __construct() {
        $this->usuarios = new ArrayCollection();
        $this->permissoes = new ArrayCollection();
    }
    
    public function getIdFuncao() {
        return $this->idFuncao;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setIdFuncao($idFuncao) {
        $this->idFuncao = $idFuncao;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }
    
    public function addUsuario(Usuario $usuario){
        if($this->usuarios->contains($usuario)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->usuarios->set($usuario->getIdUsuario(), $usuario);
    }
    
    public function getUsuario($key){
        if(!$this->usuarios->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->usuarios->get($key);
    }
    
    public function removeUsuario($key){
        if(!$this->usuarios->containsKey($key)){
            return;
        }
        $this->usuarios->remove($key);
    }
    
    public function getUsuarios(){
        return $this->usuarios->toArray();
    }
    
    public function addPermissao(Permissao $permissao){
        if($this->permissoes->contains($permissao)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->permissoes->set($permissao->getIdPermissao(), $permissao);
    }
    
    public function getPermissao($key){
        if(!$this->permissoes->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->permissoes->get($key);
    }
    
    public function removePermissao($key){
        if(!$this->permissoes->containsKey($key)){
            return;
        }
        $this->permissoes->remove($key);
    }
    
    public function getPermissoes(){
        return $this->permissoes->toArray();
    }
}
