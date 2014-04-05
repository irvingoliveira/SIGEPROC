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
 * Description of Parecer
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Parecer {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idParecer;
    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    private $descricao;
    /**
     * @ORM\Column(type="date", nullable=false)
     * @var \DateTime
     */
    private $data;
    /**
     * @ORM\ManyToOne(targetEntity="Pendencia", inversedBy="pendencias")
     * @ORM\JoinColumn(name="Pendencia_idPendencia",
     *                 referencedColumnName="idPendencia")
     * @var Pendencia
     */
    private $pendencia;
    
    public function __construct() {
        
    }
    
    public function getIdParecer() {
        return $this->idParecer;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getData() {
        return $this->data;
    }

    public function getPendencia() {
        return $this->pendencia;
    }

    public function setIdParecer($idParecer) {
        $this->idParecer = $idParecer;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setData(\DateTime $data) {
        $this->data = $data;
    }

    public function setPendencia(Pendencia $pendencia) {
        $this->pendencia = $pendencia;
    }
}
