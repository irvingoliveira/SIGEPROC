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
     * @ORM\Column(type="date", nullable=true)
     * @var DateTime
     */
    private $dataEncerramento;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $volume;
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
     *                 referencedColumnName="idApenso", nullable=true)
     * @var Apenso
     */
    private $apenso;
    /**
     * @ORM\OneToOne(targetEntity="Apenso", mappedBy="processoPai")
     * @var Apenso
     */
    private $apensoFilho;
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
    private $status;
    private $usuario;
    /**
     * @ORM\ManyToMany(targetEntity="GuiaDeRemessa", mappedBy="processos")
     * @var ArrayCollection
     */
    private $guiasDeRemessa;

    public function __construct() {
        $this->pendencias = new ArrayCollection();
        $this->guiasDeRemessa =new ArrayCollection();
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

    public function getDataEncerramento() {
        return $this->dataEncerramento;
    }

    public function getVolume() {
        return $this->volume;
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

    public function setDataAbertura(DateTime $dataAbertura) {
        $this->dataAbertura = $dataAbertura;
    }

    public function setDataEncerramento(DateTime $dataEncerramento) {
        $this->dataEncerramento = $dataEncerramento;
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

    public function getApensoFilho() {
        return $this->apensoFilho;
    }

    public function setApenso(Apenso $apenso) {
        $this->apenso = $apenso;
    }

    public function setApensoFilho(Apenso $apensoFilho) {
        $this->apensoFilho = $apensoFilho;
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
}
