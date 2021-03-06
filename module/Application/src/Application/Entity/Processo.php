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
 * @ORM\Table(name="Processo", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="numero_UNIQUE",
 *                        columns={"numero", "anoExercicio"}
 *  )
 * })
 */
class Processo {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idProcesso;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $numero;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $anoExercicio;
    /**
     * @ORM\Column(type="date", nullable=false)
     * @var DateTime
     */
    private $dataAbertura;
    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @var int
     */
    private $volume;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $imagem;
    /**
     * @ORM\ManyToOne(targetEntity="Assunto", inversedBy="processos")
     * @ORM\JoinColumn(name="Assunto_idAssunto",
     *                 referencedColumnName="idAssunto",
     *                 nullable=false
     * )
     * @var Assunto
     */
    private $assunto;
    /**
     * @ORM\ManyToOne(targetEntity="Apenso", inversedBy="processosFilhos")
     * @ORM\JoinColumn(name="Apenso_idApenso",
     *                 referencedColumnName="idApenso", unique=false, nullable=true)
     * @var Apenso
     */
    private $apenso;
    /**
     * @ORM\OneToMany(targetEntity="Apenso", mappedBy="processoPai")
     * @var ArrayCollection
     */
    private $apensosFilhos;
    /**
     * @ORM\ManyToOne(targetEntity="Requerente", inversedBy="processos")
     * @ORM\JoinColumn(name="Requerente_idRequerente",
     *                 referencedColumnName="idRequerente", 
     *                 nullable=false)
     * @var Requerente
     */
    private $requerente;
    /**
     * @ORM\OneToMany(targetEntity="Pendencia", mappedBy="processo")
     * @var ArrayCollection
     */
    private $pendencias;
    /**
     * @ORM\ManyToOne(targetEntity="StatusProcesso", inversedBy="processos")
     * @ORM\JoinColumn(name="StatusProcesso_idStatusProcesso",
     *                 referencedColumnName="idStatusProcesso", nullable=false)
     * @var StatusProcesso
     */
    private $status;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="processos")
     * @ORM\JoinColumn(name="Usuario_idUsuario",
     *                 referencedColumnName="idUsuario",nullable=false)
     * @var Usuario
     */
    private $usuario;
    /**
     * @ORM\ManyToOne(targetEntity="PostoDeTrabalho", inversedBy="processos")
     * @ORM\JoinColumn(name="PostoDeTrabalho_idPostoDeTrabalho",
     *                 referencedColumnName="idPostoDeTrabalho",nullable=false)
     * @var PostoDeTrabalho
     */
    private $postoDeTrabalho;
    /**
     * @ORM\ManyToMany(targetEntity="GuiaDeRemessa", mappedBy="processos")
     * @var ArrayCollection
     */
    private $guiasDeRemessa;

    public function __construct() {
        $this->apensosFilhos = new ArrayCollection();
        $this->pendencias = new ArrayCollection();
        $this->guiasDeRemessa =new ArrayCollection();
    }
    
    public function __set($atrib, $value){
        $this->$atrib = $value;
    }
 
    public function __get($atrib){
        return $this->$atrib;
    }
    
    public function getIdProcesso() {
        return $this->idProcesso;
    }

    public function setIdProcesso($idProcesso) {
        $this->idProcesso = $idProcesso;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getAnoExercicio() {
        return $this->anoExercicio;
    }

    public function getDataAbertura() {
        return $this->dataAbertura;
    }

    public function getVolume() {
        return $this->volume;
    }

    public function getImagem() {
        return $this->imagem;
    }

    public function setImagem($imagem) {
        $this->imagem = $imagem;
        return $this;
    }

    public function getAssunto() {
        return $this->assunto;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function setAnoExercicio($anoExercicio) {
        $this->anoExercicio = $anoExercicio;
    }

    public function setDataAbertura(\DateTime $dataAbertura) {
        $this->dataAbertura = $dataAbertura;
    }

    public function setVolume($volume) {
        $this->volume = $volume;
    }

    public function setAssunto(Assunto $assunto) {
        $this->assunto = $assunto;
    }
    
    public function getApenso() {
        return $this->apenso;
    }


    public function setApenso(Apenso $apenso) {
        $this->apenso = $apenso;
    }


    public function getRequerente() {
        return $this->requerente;
    }

    public function setRequerente(Requerente $requerente) {
        $this->requerente = $requerente;
    }
    
    public function addParecer(Pendencia $parecer){
        if($this->pendencias->contains($parecer)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->pendencias->set($parecer->getIdParecer(), $parecer);
    }
    
    public function getParecer($key){
        if(!$this->pendencias->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->pendencias->get($key);
    }
    
    public function removeParecer($key){
        if(!$this->pendencias->containsKey($key)){
            return;
        }
        $this->pendencias->remove($key);
    }
    
    public function addGuiasDeRemessa(GuiaDeRemessa $guiaDeRemessa){
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
    
    public function getStatus() {
        return $this->status;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function setStatus(StatusProcesso $status) {
        $this->status = $status;
    }

    public function setUsuario(Usuario $usuario) {
        $this->usuario = $usuario;
    }
    
    public function addPendencia(Pendencia $pendencia){
        if($this->pendencias->contains($pendencia)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->pendencias->set($pendencia->getIdPendencia(), $pendencia);
    }
    
    public function getPendencia($key){
        if(!$this->pendencias->containsKey($key)){
            throw new NullPointerException();
        }
        $this->pendencias->get($key);
    }

    public function removePendencia($key){
        if(!$this->pendencias->containsKey($key)){
            return;
        }
        $this->pendencias->remove($key);
    }
    
    public function getPendencias(){
        $this->pendencias->toArray();
    }
    
    public function addApensoFilho(Apenso $apensoFilho) {
        if($this->apensosFilhos->contains($apensoFilho)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->apensosFilhos->set($apensoFilho->getIdApenso(),$apensoFilho);
    }
    
    public function getApensoFilho($key) {
        if(!$this->apensosFilhos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->apensosFilhos->get($key);
    }
    
    public function removeApensoFilho($key){
        if(!$this->apensosFilhos->containsKey($key)){
            return;
        }
        $this->apensosFilhos->remove($key);
    }
    
    public function getApensosFilhos(){
        return $this->apensosFilhos->toArray();
    }
    
    public function getPostoDeTrabalho() {
        return $this->postoDeTrabalho;
    }

    public function setPostoDeTrabalho(PostoDeTrabalho $postoDeTrabalho) {
        $this->postoDeTrabalho = $postoDeTrabalho;
    }

}
