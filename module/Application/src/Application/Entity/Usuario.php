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
 * Description of Usuario
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Usuario {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idUsuario;
    /**
     * @ORM\Column(type="integer", unique=true, nullable=false)
     * @var int
     */
    private $matricula;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $digitoMatricula;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $nome;
    /**
     * @ORM\Column(type="string", length=100, nullable=false, unique=true)
     * @var string
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $senha;
    /**
     * @ORM\Column(type="date", nullable=false)
     * @var \DateTime
     */
    private $dataCriacao;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var bool
     */
    private $ativo;
    /**
     * @ORM\ManyToOne(targetEntity="Funcao", inversedBy="usuarios")
     * @ORM\JoinColumn(name="Funcao_idFuncao",
     *                 referencedColumnName="idFuncao", nullable=false)
     * @var Funcao 
     */
    private $funcao;
    /**
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="usuarios")
     * @ORM\JoinColumn(name="Setor_idSetor",
     *                 referencedColumnName="idSetor", nullable=true)
     * @var Setor
     */
    private $setor;
    /**
     * @ORM\OneToMany(targetEntity="GuiaDeRemessa", mappedBy="emissor")
     * @var ArrayCollection
     */
    private $guiasDeRemessaEnviadas;
    /**
     * @ORM\OneToMany(targetEntity="GuiaDeRemessa", mappedBy="destinatario")
     * @var ArrayCollection
     */
    private $guiasDeRemessaRecebidas;
    /**
     * @ORM\OneToMany(targetEntity="Processo", mappedBy="usuario")
     * @var ArrayCollection
     */
    private $processos;
    
    public function __construct() {
        $this->guiasDeRemessaEnviadas = new ArrayCollection();
        $this->guiasDeRemessaRecebidas = new ArrayCollection();
        $this->processos = new ArrayCollection();
    }
    
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function getMatricula() {
        return $this->matricula;
    }

    public function getDigito() {
        return $this->digitoMatricula;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function getDataCriacao() {
        return $this->dataCriacao;
    }

    public function isAtivo() {
        return $this->ativo;
    }

    public function getFuncao() {
        return $this->funcao;
    }

    public function getSetor() {
        return $this->setor;
    }

    public function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    public function setMatricula($matricula) {
        $this->matricula = $matricula;
    }

    public function setDigito($digito) {
        $this->digitoMatricula = $digito;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function setDataCriacao(\DateTime $dataCriacao) {
        $this->dataCriacao = $dataCriacao;
    }

    public function setAtivo($ativo) {
        $this->ativo = $ativo;
    }

    public function setFuncao(Funcao $funcao) {
        $this->funcao = $funcao;
    }

    public function setSetor(Setor $setor) {
        $this->setor = $setor;
    }
    
     public function addGuiaDeRemessaEnviada(GuiaDeRemessa $guiaDeRemessa){
        if($this->guiasDeRemessaEnviadas->contains($guiaDeRemessa)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->guiasDeRemessaEnviadas->set($guiaDeRemessa->getIdGuiaDeRemessa(), $guiaDeRemessa);
    }
    
    public function getGuiaDeRemessaEnviada($key){
        if(!$this->guiasDeRemessaEnviadas->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->guiasDeRemessaEnviadas->get($key);
    }
    
    public function removeGuiaDeRemessaEnviada($key){
        if(!$this->guiasDeRemessaEnviadas->containsKey($key)){
            return;
        }
        $this->guiasDeRemessaEnviadas->remove($key);
    }
    
    public function getGuiasDeRemessaEnviadas(){
        return $this->guiasDeRemessaEnviadas->toArray();
    }
    
    public function addProcesso(Processo $processo){
        if($this->processos->contains($processo)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->processos->set($processo->getIdGuiaDeRemessa(), $processo);
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
    
    public function addGuiaDeRemessaRecebida(GuiaDeRemessa $guiaDeRemessa){
        if($this->guiasDeRemessaRecebidas->contains($guiaDeRemessa)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->guiasDeRemessaRecebidas->set($guiaDeRemessa->getIdGuiaDeRemessa(), $guiaDeRemessa);
    }
    
    public function getGuiaDeRemessaRecebida($key){
        if(!$this->guiasDeRemessaRecebidas->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->guiasDeRemessaRecebidas->get($key);
    }
    
    public function removeGuiaDeRemessaRecebida($key){
        if(!$this->guiasDeRemessaRecebidas->containsKey($key)){
            return;
        }
        $this->guiasDeRemessaRecebidas->remove($key);
    }
    
    public function getGuiasDeRemessaRecebidas(){
        return $this->guiasDeRemessaRecebidas->toArray();
    }
}
