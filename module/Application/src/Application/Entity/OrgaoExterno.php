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
 * Description of OrgaoExterno
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class OrgaoExterno {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idOrgaoExterno;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     * @var string
     */
    private $abreviacao;
    /**
     * @ORM\OneToOne(targetEntity="Endereco", inversedBy="orgaoExterno")
     * @ORM\JoinColumn(name="Endereco_idEndereco",
     *                 referencedColumnName="idEndereco", nullable=false)
     * @var Endereco
     */
    private $endereco;
    /**
     * @ORM\OneToMany(targetEntity="FluxoPosto", mappedBy="orgaoExterno")
     * @var ArrayCollection
     */
    private $fluxosOrgaoExterno;
    /**
     * @ORM\OneToMany(targetEntity="GuiaDeRemessa", mappedBy="orgaoExterno")
     * @var ArrayCollection
     */
    private $guiasDeRemessa;
    
    public function __construct() {
        $this->fluxosOrgaoExterno = new ArrayCollection();
        $this->guiasDeRemessa = new ArrayCollection();
    }

    public function getIdOrgaoExterno() {
        return $this->idOrgaoExterno;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getAbreviacao() {
        return $this->abreviacao;
    }

    public function getEndereco() {
        return $this->endereco;
    }

    public function setIdOrgaoExterno($idOrgaoExterno) {
        $this->idOrgaoExterno = $idOrgaoExterno;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setAbreviacao($abreviacao) {
        $this->abreviacao = $abreviacao;
    }

    public function setEndereco(Endereco $endereco) {
        $this->endereco = $endereco;
    }
    
    public function addGuiaDeRemessa(GuiaDeRemessa $guiaDeremessa){
        if($this->guiasDeRemessa->contains($guiaDeremessa)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->guiasDeRemessa->set($guiaDeremessa->getIdGuiaDeRemessa(), $guiaDeremessa);
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
    
    public function getGuiaDeRemessas(){
        return $this->guiasDeRemessa->toArray();
    }
    
    public function addFluxoPosto(FluxoPosto $fluxoPosto){
        if($this->fluxosOrgaoExterno->contains($fluxoPosto)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->fluxosOrgaoExterno->set($fluxoPosto->getIdFluxoPosto(), $fluxoPosto);
    }
    
    public function getFluxoPosto($key){
        if(!$this->fluxosOrgaoExterno->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->fluxosOrgaoExterno->get($key);
    }
    
    public function removeFluxoPosto($key){
        if(!$this->fluxosOrgaoExterno->containsKey($key)){
            return;
        }
        $this->fluxosOrgaoExterno->remove($key);
    }
    
    public function getFluxosPostos(){
        return $this->fluxosOrgaoExterno->toArray();
    }
}
