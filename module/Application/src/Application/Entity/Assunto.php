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
 * @ORM\Table(name="Assunto",uniqueConstraints={
 *     @ORM\UniqueConstraint(name="nome_UNIQUE", columns="nome")
 * })
 */
class Assunto {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idAssunto;
    /**
     * @ORM\Column(type="string", length=150, nullable=false, unique=true)
     * @var string
     */
    private $nome;
    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    private $descricao;
    /**
     * @ORM\OneToMany(targetEntity="Processo", mappedBy="assunto")
     * @var ArrayCollection
     */
    private $processos;
    /**
     * @ORM\OneToMany(targetEntity="Workflow", mappedBy="assunto")
     * @var ArrayCollection
     */
    private $workflows;
    /**
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="assuntos")
     * @ORM\JoinColumn(name="Setor_idSetor", 
     *                 referencedColumnName="idSetor", nullable=false)
     * @var Setor
     */
    private $setor;

    public function __construct() {
        $this->processos = new ArrayCollection();
        $this->workflows = new ArrayCollection();
    }
    
    public function __set($atrib, $value){
        $this->$atrib = $value;
    }
 
    public function __get($atrib){
        return $this->$atrib;
    }
    
    public function getIdAssunto() {
        return $this->idAssunto;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setIdAssunto($idAssunto) {
        $this->idAssunto = $idAssunto;
    }

    public function setNome($nome) {
        $this->nome = $nome;
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
        if (!$this->processos->containsKey($key)) {
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
    
    public function getProcessos(){
        return $this->processos->toArray();
    }
    
    public function addWorkflow(Workflow $workflow){
        if($this->workflows->contains($workflow)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->workflows->set($workflow->getIdWorkflow(), $workflow);
    }
    
    public function getWorkflow($key){
        if(!$this->workflows->containsKey($key)){
            throw new NullPointerException();
        }
        $this->workflows->get($key);
    }

    public function removeWorkflow($key){
        if(!$this->workflows->containsKey($key)){
            return;
        }
        $this->workflows->remove($key);
    }
    
    public function getWorkflows(){
        $this->workflows->toArray();
    }
    
    public function getSetor() {
        return $this->setor;
    }

    public function setSetor(Setor $setor) {
        $this->setor = $setor;
    }
}
