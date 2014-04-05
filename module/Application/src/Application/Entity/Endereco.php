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
/**
 * Description of Endereco
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Endereco {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idEndereco;
    /**
     * @ORM\Column(type="string", length=200, nullable=false)
     * @var string
     */
    private $logradouro;
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $numero;
    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @var string
     */
    private $complemento;
    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $bairro;
    /**
     * @ORM\ManyToOne(targetEntity="Cidade", inversedBy="enderecos")
     * @ORM\JoinColumn(name="Cidade_idCidade",
     *                 referencedColumnName="idCidade", nullable=false)
     * @var Cidade
     */
    private $cidade;
    /**
     * @ORM\OneToOne(targetEntity="OrgaoExterno", mappedBy="endereco")
     * @var OrgaoExterno
     */
    private $orgaoExterno;
    
    public function __construct() {
        
    }
    
    public function getIdEndereco() {
        return $this->idEndereco;
    }

    public function getLogradouro() {
        return $this->logradouro;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getComplemento() {
        return $this->complemento;
    }

    public function getBairro() {
        return $this->bairro;
    }

    public function getCidade() {
        return $this->cidade;
    }

    public function getOrgaoExterno() {
        return $this->orgaoExterno;
    }

    public function setIdEndereco($idEndereco) {
        $this->idEndereco = $idEndereco;
    }

    public function setLogradouro($logradouro) {
        $this->logradouro = $logradouro;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function setComplemento($complemento) {
        $this->complemento = $complemento;
    }

    public function setBairro($bairro) {
        $this->bairro = $bairro;
    }

    public function setCidade(Cidade $cidade) {
        $this->cidade = $cidade;
    }

    public function setOrgaoExterno(OrgaoExterno $orgaoExterno) {
        $this->orgaoExterno = $orgaoExterno;
    }
}
