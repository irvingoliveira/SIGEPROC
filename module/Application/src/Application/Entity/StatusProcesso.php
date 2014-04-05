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
 * Description of StatusProcesso
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class StatusProcesso {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idStatusProcesso;
    /**
     * @ORM\Column(type="string", length=80, nullable=false)
     * @var string
     */
    private $descricao;
    /**
     * @ORM\OneToMany(targetEntity="Processo", mappedBy="status")
     * @var ArrayCollection
     */
    private $processos;
    
    function __construct() {
        $this->processos = new ArrayCollection();
    }

    public function getIdStatusProcesso() {
        return $this->idStatusProcesso;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getProcessos() {
        return $this->processos->toArray();
    }

    public function setIdStatusProcesso($idStatusProcesso) {
        $this->idStatusProcesso = $idStatusProcesso;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }
    
    public function addProcesso(Processo $processo){
        if($this->processos->contains($processo)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->processos->set($processo->getIdProcesso(), $processo);
    }
    
    public function getProcesso($key){
        if(!$this->processos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->processos->get($key);
    }
    
    public function removeProcesso($key){
        if(!$this->processos->containsKey($key)){
            return;
        }
        $this->processos->remove($key);
    }
}
