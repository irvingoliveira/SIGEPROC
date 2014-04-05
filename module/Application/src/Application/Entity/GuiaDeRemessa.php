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
 * Description of GuiaDeRemessa
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 * @ORM\Table(name="GuiaDeRemessa", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="numero_UNIQUE",
 *                        columns={"numero", "anoExercicio"}
 *  )
 * })
 */
class GuiaDeRemessa {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @var int
     */
    private $idGuiaDeRemessa;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var inf
     */
    private $numero;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $anoExercicio;
    /**
     * @ORM\Column(type="date", nullable=false)
     * @var \DateTime
     */
    private $dataCriacao;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var \DateTime
     */
    private $dataRecebimento;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="guiasDeRemessaEnviadas")
     * @ORM\JoinColumn(name="Emissor_idUsuario",
     *                 referencedColumnName="idUsuario", nullable=false)
     * @var Usuario
     */
    private $emissor;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="guiasDeRemessaRecebidas")
     * @ORM\JoinColumn(name="Destinatario_idUsuario", nullable=true)
     * @var Usuario
     */
    private $destinatario;
    /**
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="guiasDeRemessa")
     * @ORM\JoinColumn(name="Setor_idSetor",
     *                 referencedColumnName="idSetor", nullable=true)
     * @var Setor
     */
    private $setor;
    /**
     * @ORM\ManyToOne(targetEntity="OrgaoExterno", inversedBy="guiasDeRemessa")
     * @ORM\JoinColumn(name="OrgaoExterno_idOrgaoExterno",
     *                 referencedColumnName="idOrgaoExterno", nullable=true)
     * @var OrgaoExterno
     */
    private $orgaoExterno;
    /**
     * @ORM\ManyToMany(targetEntity="Processo", inversedBy="guiasDeRemessa")
     * @ORM\JoinTable(name="GuiaDeRemessa_has_Processo",
     *                joinColumns={@ORM\JoinColumn(name="GuiaDeRemessa_idGuiaDeRemessa",
     *                                             referencedColumnName="idGuiaDeRemessa", 
     *                                             nullable=false)},
     *                inverseJoinColumns={@ORM\JoinColumn(name="Processo_idProcesso",
     *                                                    referencedColumnName="idProcesso",
     *                                                    nullable=false)}
     * )
     * @var ArrayCollection
     */
    private $processos;

    public function __construct() {
        $this->processos = new ArrayCollection();
    }
    
    public function getIdGuiaDeRemessa() {
        return $this->idGuiaDeRemessa;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getAnoExercicio() {
        return $this->anoExercicio;
    }

    public function getDataCriacao() {
        return $this->dataCriacao;
    }

    public function getDataRecebimento() {
        return $this->dataRecebimento;
    }

    public function setIdGuiaDeRemessa($idGuiaDeRemessa) {
        $this->idGuiaDeRemessa = $idGuiaDeRemessa;
    }

    public function setNumero(inf $numero) {
        $this->numero = $numero;
    }

    public function setAnoExercicio($anoExercicio) {
        $this->anoExercicio = $anoExercicio;
    }

    public function setDataCriacao(\DateTime $dataCriacao) {
        $this->dataCriacao = $dataCriacao;
    }

    public function setDataRecebimento(\DateTime $dataRecebimento) {
        $this->dataRecebimento = $dataRecebimento;
    }
    
    public function addProcesso(Processo $processo){
        if($this->processos->contains($processo)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->processos->set($processo->getIdProcesso(), $processo);
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
    
    public function getProcessos(){
        return $this->processos->toArray();
    }
    
    public function getOrgaoExterno() {
        return $this->orgaoExterno;
    }

    public function setOrgaoExterno(OrgaoExterno $orgaoExterno) {
        $this->orgaoExterno = $orgaoExterno;
    }
    
    public function getSetor() {
        return $this->setor;
    }

    public function setSetor(Setor $setor) {
        $this->setor = $setor;
    }
    
    public function getEmissor() {
        return $this->emissor;
    }

    public function getDestinatario() {
        return $this->destinatario;
    }

    public function setEmissor(Usuario $emissor) {
        $this->emissor = $emissor;
    }

    public function setDestinatario(Usuario $destinatario) {
        $this->destinatario = $destinatario;
    }
}