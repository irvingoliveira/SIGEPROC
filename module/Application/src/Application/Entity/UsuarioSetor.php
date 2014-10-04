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
 * Description of UsuarioSetor
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class UsuarioSetor {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idUsuarioSetor;
    /**
     * @ORM\Column(type="date", nullable=false)
     * @var \DateTime
     */
    private $dataLotacao;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @var \DateTime
     */
    private $dataSaida;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="setores")
     * @ORM\JoinColumn(name="Usuario_idUsuario", 
     *                 referencedColumnName="idUsuario", nullable=false)
     * @var Usuario
     */
    private $usuario;
    /**
     * @ORM\ManyToOne(targetEntity="Setor", inversedBy="usuarios")
     * @ORM\JoinColumn(name="Setor_idSetor", 
     *                 referencedColumnName="idSetor", nullable=false)
     * @var Setor
     */
    private $setor;
    
    public function __construct() {
        
    }
    
    public function getIdUsuarioSetor() {
        return $this->idUsuarioSetor;
    }

    public function getDataLotacao() {
        return $this->dataLotacao;
    }

    public function getDataSaida() {
        return $this->dataSaida;
    }

    public function getUsuario() {
        return $this->usuario;
    }

    public function getSetor() {
        return $this->setor;
    }

    public function setIdUsuarioSetor($idUsuarioSetor) {
        $this->idUsuarioSetor = $idUsuarioSetor;
    }

    public function setDataLotacao(\DateTime $dataLotacao) {
        $this->dataLotacao = $dataLotacao;
    }

    public function setDataSaida(\DateTime $dataSaida) {
        $this->dataSaida = $dataSaida;
    }

    public function setUsuario(Usuario $usuario) {
        $this->usuario = $usuario;
    }

    public function setSetor(Setor $setor) {
        $this->setor = $setor;
    }
}
