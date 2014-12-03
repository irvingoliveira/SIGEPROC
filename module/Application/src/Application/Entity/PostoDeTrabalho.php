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
/**
 * Description of PostoDeTrabalho
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="colunaDiscriminatoria", type="string")
 * @ORM\DiscriminatorMap({"PostoDeTrabalho" = "PostoDeTrabalho", "Setor" = "Setor", "OrgaoExterno" = "OrgaoExterno"})
 */
abstract class PostoDeTrabalho {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idPostoDeTrabalho;
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
     * @ORM\OneToMany(targetEntity="FluxoPosto", mappedBy="posto")
     * @var ArrayCollection
     */
    private $fluxosPosto;
    /**
     * @ORM\OneToMany(targetEntity="GuiaDeRemessa", mappedBy="posto")
     * @var ArrayCollection
     */
    private $guiasDeRemessa;
    /**
     * @ORM\OneToMany(targetEntity="Processo", mappedBy="postoDeTrabalho")
     * @var ArrayCollection 
     */
    private $processos;

    public function __construct() {
        $this->fluxosPosto = new ArrayCollection();
        $this->guiasDeRemessa = new ArrayCollection();
        $this->processos = new ArrayCollection();
    }

    public function __set($atrib, $value){
        $this->$atrib = $value;
    }
 
    public function __get($atrib){
        return $this->$atrib;
    }
    
    public function getIdPostoDeTrabalho() {
        return $this->idPostoDeTrabalho;
    }

    public function setIdPostoDeTrabalho($idPostoDeTrabalho) {
        $this->idPostoDeTrabalho = $idPostoDeTrabalho;
    }
   
    public function getNome(){
        return $this->nome;
    }

    public function getSigla() {
        return $this->sigla;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setSigla($sigla) {
        $this->sigla = $sigla;
    }

    public function getFluxosPosto() {
        return $this->fluxosPosto;
    }

    public function getGuiasDeRemessa() {
        return $this->guiasDeRemessa;
    }

    public function setFluxosPosto(ArrayCollection $fluxosPosto) {
        $this->fluxosPosto = $fluxosPosto;
    }

    public function setGuiasDeRemessa(ArrayCollection $guiasDeRemessa) {
        $this->guiasDeRemessa = $guiasDeRemessa;
    }

    public function getProcessos() {
        return $this->processos;
    }

    public function setProcessos(ArrayCollection $processos) {
        $this->processos = $processos;
    }

}
