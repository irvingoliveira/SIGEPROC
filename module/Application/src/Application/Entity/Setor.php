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
 * @ORM\Table(name="Setor", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="uq_Setor_por_Secretaria", 
 *                           columns={"nome", "Secretaria_idSecretaria"}),
 *     @ORM\UniqueConstraint(name="uq_Sigla_Setor_por_Secretaria", 
 *                           columns={"sigla", "Secretaria_idSecretaria"})
 * })
 */
class Setor extends PostoDeTrabalho{
    /**
     * @ORM\Column(type="boolean",nullable=false)
     * @var boolean
     */
    private $arquivo;
    /**
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="setoresFilhos")
     * @ORM\JoinColumn(name="Setor_idSetor",
     *                 referencedColumnName="idSetor", nullable=true, onDelete="SET NULL")
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
     *                 referencedColumnName="idTipoSetor", nullable=false)
     * @var TipoSetor
     */
    private $tipoSetor;
    /**
     * @ORM\ManyToOne(targetEntity="Secretaria", inversedBy="setores")
     * @ORM\JoinColumn(name="Secretaria_idSecretaria",
     *                 referencedColumnName="idSecretaria", nullable=false)
     * @var Secretaria
     */
    private $secretaria;
    /**
     * @ORM\OneToMany(targetEntity="UsuarioSetor", mappedBy="setor")
     * @var ArrayCollection
     */
    private $usuarios;
    /**
     * @ORM\OneToMany(targetEntity="Requerente", mappedBy="setor")
     * @var ArrayCollection
     */
    private $requerentes;
    /**
     * @ORM\OneToMany(targetEntity="Assunto", mappedBy="setor")
     * @var ArrayCollection
     */
    private $assuntos;


    public function __construct() {
        parent::__construct();
        $this->requerentes = new ArrayCollection();
        $this->setoresFilhos = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
        $this->assuntos = new ArrayCollection();
    }
    
    public function getIdSetor(){
        return parent::getIdPostoDeTrabalho();
    }

    public function getSetorPai() {
        return $this->setorPai;
    }

    public function getSecretaria() {
        return $this->secretaria;
    }

    public function setSetorPai(Setor $setorPai) {
        $this->setorPai = $setorPai;
    }

    public function setSecretaria(Secretaria $secretaria) {
        $this->secretaria = $secretaria;
    }

    public function addSetorFilho(Setor $setor){
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

    public function addRequerente(Requerente $requerente){
        if($this->requerentes->contains($requerente)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->requerentes->set($requerente->getIdRequerente(), $requerente);
    }
    
    public function getRequerente($key){
        if(!$this->requerentes->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->requerentes->get($key);
    }
    
    public function removeRequerente($key){
        if(!$this->requerentes->containsKey($key)){
            return;
        }
        $this->requerentes->remove($key);
    }
    
    public function getRequerentes(){
        return $this->requerentes->toArray();
    }
    
    public function addAssunto(Assunto $assunto){
        if($this->assuntos->contains($assunto)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->assuntos->set($assunto->getIdAssunto(), $assunto);
    }
    
    public function getAssunto($key){
        if(!$this->assuntos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->assuntos->get($key);
    }
    
    public function removeAssunto($key){
        if(!$this->assuntos->containsKey($key)){
            return;
        }
        $this->assuntos->remove($key);
    }
    
    public function getAssuntos(){
        return $this->assuntos->toArray();
    }
    
    public function setArquivo($boolean = FALSE){
        $this->arquivo=$boolean;
    }
    
    public function isArquivo(){
        return $this->arquivo;
    }

    public function getTipoSetor() {
        return $this->tipoSetor;
    }

    public function getUsuarios() {
        return $this->usuarios;
    }

    public function setTipoSetor(TipoSetor $tipoSetor) {
        $this->tipoSetor = $tipoSetor;
    }

    public function setUsuarios(ArrayCollection $usuarios) {
        $this->usuarios = $usuarios;
    }

    public function __toString() {
        return $this->secretaria->getSigla().' - '.$this->getTipoSetor().' de '.$this->getNome();
    }
}
