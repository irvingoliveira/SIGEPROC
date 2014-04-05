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
 * Description of Parecer
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Pendencia {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idPendencia;
    /**
     * @ORM\Column(type="text", nullable=false)
     * @var int
     */
    private $descricao;
    /**
     * @ORM\Column(type="date", nullable=false)
     * @var \DateTime
     */
    private $dataCriacao;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var bool
     */
    private $resolvido;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    private $dataConclusão;
    /**
     * @ORM\ManyToOne(targetEntity="Processo", inversedBy="pareceres")
     * @ORM\JoinColumn(name="Processo_idProcesso",
     *                 referencedColumnName="idProcesso", nullable=false)
     * @var Processo
     */
    private $processo;
    /**
     * @ORM\OneToMany(targetEntity="Parecer", mappedBy="pendencia")
     * @var ArrayCollection
     */
    private $pareceres;
    
    public function __construct() {
        $this->pareceres = new ArrayCollection();
    }
    
    public function getIdParecer() {
        return $this->idPendencia;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getDataCriacao() {
        return $this->dataCriacao;
    }

    public function getResolvido() {
        return $this->resolvido;
    }

    public function getDataConclusão() {
        return $this->dataConclusão;
    }

    public function getProcesso() {
        return $this->processo;
    }

    public function setIdParecer($idParecer) {
        $this->idPendencia = $idParecer;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setDataCriacao(\DateTime $dataCriacao) {
        $this->dataCriacao = $dataCriacao;
    }

    public function setResolvido($resolvido) {
        $this->resolvido = $resolvido;
    }

    public function setDataConclusão(\DateTime $dataConclusão) {
        $this->dataConclusão = $dataConclusão;
    }

    public function setProcesso(Processo $processo) {
        $this->processo = $processo;
    }
    
    public function addParecer(Parecer $parecer){
        if($this->pareceres->contains($parecer)){
            throw new ObjectAlreadyExistsOnCollectionException();
        }        
        $this->pareceres->set($parecer->getIdParecer(), $parecer);
    }
    
    public function getParecer($key){
        if(!$this->pareceres->containsKey($key)){
            throw new NullPointerException();
        }
        return $this->pareceres->get($key);
    }
    
    public function removeParecer($key){
        if(!$this->pareceres->containsKey($key)){
            return;
        }
        $this->pareceres->remove($key);
    }
    
    public function getPareceres(){
        return $this->pareceres->toArray();
    }
}
