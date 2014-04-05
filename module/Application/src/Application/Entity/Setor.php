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
 * Description of Setor
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Setor {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idSetor;
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
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="setoresFilhos")
     * @ORM\JoinColumn(name="Setor_idSetor",
     *                 referencedColumnName="idSetor", nullable=true)
     * @var Setor
     */
    private $setorPai;
    /**
     * @ORM\OneToMany(targetEntity="Setor", mappedBy="setorPai")
     * @var ArrayCollection
     */
    private $setoresFilhos;
    /**
     * @ORM\ManyToOne(targetEntity="TipoSetor", inversedBy="setores")
     * @ORM\JoinColumn(name="TipoSetor_idTipoSetor",
     *                 referencedColumnName="idTipoSetor")
     * @var TipoSetor
     */
    private $tipo;
    /**
     * @ORM\ManyToOne(targetEntity="Secretaria", inversedBy="setores")
     * @ORM\JoinColumn(name="Secretaria_idSecretaria",
     *                 referencedColumnName="idSecretaria", nullable=false)
     * @var Secretaria
     */
    private $secretaria;
    /**
     * @ORM\OneToMany(targetEntity="Usuario", mappedBy="setor")
     * @var ArrayCollection
     */
    private $usuarios;
    /**
     * @ORM\OneToMany(targetEntity="FluxoPosto", mappedBy="setor")
     * @var ArrayCollection
     */
    private $fluxosPostos;
    /**
     * @ORM\OneToMany(targetEntity="GuiaDeRemessa", mappedBy="setor")
     * @var ArrayCollection
     */
    private $guiasDeRemessa;
    
    public function __construct() {
        $this->fluxosPostos = new ArrayCollection();
        $this->guiasDeRemessa = new ArrayCollection();
        $this->setoresFilhos = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
    }
    
    public function getIdSetor() {
        return $this->idSetor;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getSigla() {
        return $this->sigla;
    }

    public function getSetorPai() {
        return $this->setorPai;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function getSecretaria() {
        return $this->secretaria;
    }

    public function setIdSetor($idSetor) {
        $this->idSetor = $idSetor;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setSigla($sigla) {
        $this->sigla = $sigla;
    }

    public function setSetorPai(Setor $setorPai) {
        $this->setorPai = $setorPai;
    }

    public function setTipo(TipoSetor $tipo) {
        $this->tipo = $tipo;
    }

    public function setSecretaria(Secretaria $secretaria) {
        $this->secretaria = $secretaria;
    }

    public function addSetorFilho(SetorFilho $setor){
        if($this->setoresFilhos->contains($setor)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->setoresFilhos->set($setor->getIdSetorFilho(), $setor);
    }
    
    public function getSetorFilho($key){
        if(!$this->setoresFilhos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->setoresFilhos->get($key);
    }
    
    public function removeSetorFilho($key){
        if(!$this->setoresFilhos->containsKey($key)){
            return;
        }
        $this->setoresFilhos->remove($key);
    }
    
    public function getSetoresFilhos(){
        return $this->setoresFilhos->toArray();
    }
    
    public function addGuiaDeRemessa(GuiaDeRemessa $guiaDeRemessa){
        if($this->guiasDeRemessa->contains($guiaDeRemessa)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->guiasDeRemessa->set($guiaDeRemessa->getIdGuiaDeRemessa(), $guiaDeRemessa);
    }
    
    public function getGuiaDeRemessa($key){
        if(!$this->guiasDeRemessa->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->guiasDeRemessa->get($key);
    }
    
    public function removeGuiaDeRemessa($key){
        if(!$this->guiasDeRemessa->containsKey($key)){
            return;
        }
        $this->guiasDeRemessa->remove($key);
    }
    
    public function getGuiasDeRemessa(){
        return $this->guiasDeRemessa->toArray();
    }

    public function addFluxoPosto(FluxoPosto $fluxoPosto){
        if($this->fluxosPostos->contains($fluxoPosto)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->fluxosPostos->set($fluxoPosto->getIdFluxoPosto(), $fluxoPosto);
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
    
    public function getFluxosPostos(){
        return $this->fluxosPostos->toArray();
    }
}
