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
 * Description of Workflow
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Workflow {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idWorkflow;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $descricao;
    /**
     * @ORM\ManyToOne(targetEntity="Assunto", inversedBy="workflows")
     * @ORM\JoinColumn(name="Assunto_idAssunto",
     *                 referencedColumnName="idAssunto", nullable=false)
     * @var Assunto
     */
    private $assunto;
    /**
     * @ORM\OneToMany(targetEntity="FluxoPosto", mappedBy="workflow")
     * @var ArrayCollection
     */
    private $fluxosPostos;
    
    public function __construct() {
        $this->fluxosPostos = new ArrayCollection();
    }
    
    public function getIdWorkflow() {
        return $this->idWorkflow;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getAssunto() {
        return $this->assunto;
    }

    public function setIdWorkflow($idWorkflow) {
        $this->idWorkflow = $idWorkflow;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setAssunto(Assunto $assunto) {
        $this->assunto = $assunto;
    }

    public function addFluxoPosto(FluxoPosto $fluxoposto){
        if($this->fluxosPostos->contains($fluxoposto)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->fluxosPostos->set($fluxoposto->getIdFluxoPosto(), $fluxoposto);
    }
    
    public function getFluxoPosto($key){
        if(!$this->fluxosPostos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->fluxosPostos->get($key);
    }
    
    public function removeFluxoPosto($key){
        if(!$this->fluxosPostos->containsKey($key)){
            return;
        }
        $this->fluxosPostos->remove($key);
    }
    
    public function getFluxosPosto(){
        return $this->fluxosPostos->toArray();
    }
}
