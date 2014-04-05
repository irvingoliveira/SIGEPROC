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
class Apenso {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idApenso;
    /**
     * @ORM\Column(type="date", nullable=false)
     * @var DateTime
     */
    private $dataInicio;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @var DateTime
     */
    private $dataFim;
    /**
     * @ORM\OneToOne(targetEntity="Processo", inversedBy="apensoFilho")
     * @ORM\JoinColumn(name="Processo_idProcesso",
     *                 referencedColumnName="idProcesso", nullable=false)
     * @var Processo
     */
    private $processoPai;
    /**
     * @ORM\OneToMany(targetEntity="Processo", mappedBy="apenso")
     * @var ArrayCollection
     */
    private $processosFilhos;
    
    public function __construct() {
        $this->processosFilhos = new ArrayCollection();
    }
    
    public function getIdApenso() {
        return $this->idApenso;
    }

    public function getDataInicio() {
        return $this->dataInicio;
    }

    public function getDataFim() {
        return $this->dataFim;
    }

    public function getProcessoPai() {
        return $this->processoPai;
    }

    public function getProcessosFilhos() {
        return $this->processosFilhos;
    }

    public function setIdApenso($idApenso) {
        $this->idApenso = $idApenso;
    }

    public function setDataInicio(\DateTime $dataInicio) {
        $this->dataInicio = $dataInicio;
    }

    public function setDataFim(\DateTime $dataFim) {
        $this->dataFim = $dataFim;
    }

    public function setProcessoPai(Processo $processoPai) {
        $this->processoPai = $processoPai;
    }
    
    public function addProcessoFilho(Processo $processo){
        if($this->processosFilhos->contains($processo)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->processosFilhos->set($processo->getIdProcesso(), $processo);
        
    }
    
    public function getProcessoFilho($key){
        if(!$this->processosFilhos->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->processosFilhos->get($key);
    }

    public function removeProcessoFilho($key){
        if(!$this->processosFilhos->containsKey($key)){
            return;
        }
        $this->processosFilhos->remove($key);
    }
}
