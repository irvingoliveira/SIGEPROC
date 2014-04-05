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
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $matricula;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $digito;
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
     *                 referencedColumnName="idSetor", nullable=false)
     * @var Setor
     */
    private $setor;
    /**
     * @ORM\OneToMany(targetEntity="GuiaDeRemessa", mappedBy="emissor")
     * @var ArrayCollection
     */
    private $guiasDeRemessasEnviadas;
    /**
     * @ORM\OneToMany(targetEntity="GuiaDeRemessa", mappedBy="destinatario")
     * @var ArrayCollection
     */
    private $guiasDeRemessasRecebidas;
    
    public function __construct() {
        $this->guiasDeRemessaEnviadas = new ArrayCollection();
        $this->guiasDeRemessaRecebidas = new ArrayCollection();
    }
    
    public function getIdUsuario() {
        return $this->idUsuario;
    }

    public function getMatricula() {
        return $this->matricula;
    }

    public function getDigito() {
        return $this->digito;
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

    public function getAtivo() {
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
        $this->digito = $digito;
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
        if($this->guiasDeRemessasEnviadas->contains($guiaDeRemessa)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->guiasDeRemessasEnviadas->set($guiaDeRemessa->getIdGuiaDeRemessa(), $guiaDeRemessa);
    }
    
    public function getGuiaDeRemessaEnviada($key){
        if(!$this->guiasDeRemessasEnviadas->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->guiasDeRemessasEnviadas->get($key);
    }
    
    public function removeGuiaDeRemessaEnviada($key){
        if(!$this->guiasDeRemessasEnviadas->containsKey($key)){
            return;
        }
        $this->guiasDeRemessasEnviadas->remove($key);
    }
    
    public function getGuiasDeRemessaEnviadas(){
        return $this->guiasDeRemessasEnviadas->toArray();
    }
    
    public function addGuiaDeRemessaRecebida(GuiaDeRemessa $guiaDeRemessa){
        if($this->guiasDeRemessasRecebidas->contains($guiaDeRemessa)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }
        $this->guiasDeRemessasRecebidas->set($guiaDeRemessa->getIdGuiaDeRemessa(), $guiaDeRemessa);
    }
    
    public function getGuiaDeRemessaRecebida($key){
        if(!$this->guiasDeRemessasRecebidas->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->guiasDeRemessasRecebidas->get($key);
    }
    
    public function removeGuiaDeRemessaRecebida($key){
        if(!$this->guiasDeRemessasRecebidas->containsKey($key)){
            return;
        }
        $this->guiasDeRemessasRecebidas->remove($key);
    }
    
    public function getGuiasDeRemessaRecebidas(){
        return $this->guiasDeRemessasRecebidas->toArray();
    }
}
