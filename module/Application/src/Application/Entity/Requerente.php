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
 * Description of Requerente
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Requerente {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idRequerente;
    /**
     * @ORM\Column(type="string", length=80, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\OneToOne(targetEntity="Documento", mappedBy="requerenteProcesso")
     * @var Documento
     */
    private $documento;
    /**
     * @ORM\OneToMany(targetEntity="Telefone", mappedBy="requerenteTelefone")
     * @var ArrayCollection
     */
    private $telefones;
    /**
     * @ORM\OneToMany(targetEntity="Processo", mappedBy="requerente")
     * @var ArrayCollection
     */
    private $processos;
    /**
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="requerentes")
     * @ORM\JoinColumn(name="Setor_idSetor",
     *                 referencedColumnName="idSetor", nullable=true)
     * @var Setor
     */
    private $setor;
    
    public function __construct() {
        $this->processos = new ArrayCollection();
        $this->telefones = new ArrayCollection();
    }
    
    public function getIdRequerente() {
        return $this->idRequerente;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getProcessos() {
        return $this->processos->toArray();
    }

    public function setIdRequerente($idRequerente) {
        $this->idRequerente = $idRequerente;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function addProcesso(Processo $processos) {
        if($this->processos->contains($processos)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->processos->set($processos->getIdProcesso(), $processos);
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
    
    public function getDocumento() {
        return $this->documento;
    }

    public function setDocumento(Documento $documento) {
        $this->documento = $documento;
    }
    
    public function addTelefone(Telefone $telefone){
        if($this->telefones->contains($telefone)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->telefones->set($telefone->getIdTelefone(), $telefone);
    }
    
    public function getTelefone($key){
        if(!$this->telefones->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->telefones->get($key);
    }
    
    public function removeTelefone($key){
        if(!$this->telefones->containsKey($key)){
            return;
        }
        $this->telefones->remove($key);
    }
    
    public function getTelefones(){
        return $this->telefones->toArray();
    }
    
    public function getSetor() {
        return $this->setor;
    }

    public function setSetor(Setor $setor) {
        $this->setor = $setor;
    }
}
