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
 * Description of Telefone
 *
 * @author Irving Fernando de Medeiros Oliveira
 * @ORM\Entity
 */
class Telefone {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $idTelefone;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $ddd;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    private $numero;
    /**
     * @ORM\ManyToOne(targetEntity="Requerente", inversedBy="telefones")
     * @ORM\JoinColumn(name="Requerente_idRequerente",
     *                 referencedColumnName="idRequerente", nullable=false)
     * @var Requerente
     */
    private $requerenteTelefone;
    
    public function __construct() {
        
    }
    
    public function getIdTelefone() {
        return $this->idTelefone;
    }

    public function getDdd() {
        return $this->ddd;
    }

    public function getNumero() {
        return $this->numero;
    }

    public function getRequerente() {
        return $this->requerenteTelefone;
    }

    public function setIdTelefone($idTelefone) {
        $this->idTelefone = $idTelefone;
    }

    public function setDdd($ddd) {
        $this->ddd = $ddd;
    }

    public function setNumero($numero) {
        $this->numero = $numero;
    }

    public function setRequerente(Requerente $requerente) {
        $this->requerenteTelefone = $requerente;
    }
}
